<?php

namespace App\Models;

use App\Enums\MealSlotType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['outlet_id', 'name', 'description', 'price', 'available_slots', 'image_path', 'is_active'])]
class Meal extends Model
{
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'available_slots' => 'array',
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
}
