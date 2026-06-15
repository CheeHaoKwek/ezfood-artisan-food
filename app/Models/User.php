<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\DietaryPreference;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'nickname', 'mobile_number', 'company_name', 'dietary_preference',
    'email', 'password', 'is_admin', 'outlet_id', 'registered_qr_config_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    // TODO: replace with a proper staff/role system (later ticket)
    public function canAccessPanel(Panel $panel): bool
    {
        // Coerce: is_admin is null on in-memory instances that never read the
        // DB default (e.g. factory users) and on pre-flag legacy rows.
        return (bool) $this->is_admin;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'dietary_preference' => DietaryPreference::class,
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function registeredViaQrConfig(): BelongsTo
    {
        return $this->belongsTo(QrConfig::class, 'registered_qr_config_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscriptionFor(QrConfig $qrConfig): ?Subscription
    {
        return $this->subscriptions()
            ->active()
            ->where('qr_config_id', $qrConfig->id)
            ->whereDate('ends_on', '>=', now($qrConfig->outlet->timezone)->toDateString())
            ->latest('starts_on')
            ->first();
    }
}
