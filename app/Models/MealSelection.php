<?php

namespace App\Models;

use App\Enums\MealSlotType;
use App\Enums\SelectionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['subscription_id', 'meal_id', 'serve_date', 'slot', 'status'])]
class MealSelection extends Model
{
    protected function casts(): array
    {
        return [
            'serve_date' => 'date',
            'slot' => MealSlotType::class,
            'status' => SelectionStatus::class,
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
