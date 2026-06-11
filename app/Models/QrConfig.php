<?php

namespace App\Models;

use App\Enums\CutoffBasis;
use App\Enums\MealMode;
use App\Enums\OperationDays;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'outlet_id', 'code', 'operation_days', 'is_24_hours', 'opens_at', 'closes_at',
    'meal_mode', 'cutoff_basis', 'daily_cutoff_time', 'delivery_location', 'is_active',
])]
class QrConfig extends Model
{
    protected static function booted(): void
    {
        static::creating(function (QrConfig $config) {
            $config->code ??= (string) Str::ulid();
        });
    }

    protected function casts(): array
    {
        return [
            'operation_days' => OperationDays::class,
            'meal_mode' => MealMode::class,
            'cutoff_basis' => CutoffBasis::class,
            'is_24_hours' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function mealSlots(): HasMany
    {
        return $this->hasMany(MealSlot::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
