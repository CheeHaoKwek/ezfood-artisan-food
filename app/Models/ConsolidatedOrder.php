<?php

namespace App\Models;

use App\Enums\ConsolidatedOrderStatus;
use App\Enums\MealSlotType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['outlet_id', 'serve_date', 'slot', 'payload', 'total_meals', 'status', 'consolidated_at'])]
class ConsolidatedOrder extends Model
{
    protected function casts(): array
    {
        return [
            'serve_date' => 'date',
            'slot' => MealSlotType::class,
            'payload' => 'array',
            'status' => ConsolidatedOrderStatus::class,
            'consolidated_at' => 'datetime',
        ];
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }
}
