<?php

namespace App\Models;

use App\Enums\DietaryPreference;
use App\Enums\MealSlotType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['outlet_id', 'name', 'description', 'price', 'available_slots', 'is_vegetarian', 'image_path', 'is_active'])]
class Meal extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'available_slots' => 'array',
            'is_vegetarian' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function availableForSlot(MealSlotType $slot): bool
    {
        return $this->available_slots === null
            || in_array($slot->value, $this->available_slots, true);
    }

    /**
     * Default menu filter for a subscriber's dietary preference — a default,
     * not a restriction: deliberately not re-enforced at selection time.
     */
    public function scopeForDietaryPreference(Builder $query, ?DietaryPreference $preference): Builder
    {
        return $query->when(
            $preference === DietaryPreference::Vegetarian,
            fn (Builder $q) => $q->where('is_vegetarian', true),
        );
    }
}
