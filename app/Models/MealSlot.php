<?php

namespace App\Models;

use App\Enums\MealSlotType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['qr_config_id', 'slot', 'starts_at', 'ends_at', 'cutoff_time', 'is_active'])]
class MealSlot extends Model
{
    protected function casts(): array
    {
        return [
            'slot' => MealSlotType::class,
            'is_active' => 'boolean',
        ];
    }

    public function qrConfig(): BelongsTo
    {
        return $this->belongsTo(QrConfig::class);
    }
}
