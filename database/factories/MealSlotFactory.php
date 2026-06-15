<?php

namespace Database\Factories;

use App\Enums\MealSlotType;
use App\Models\MealSlot;
use App\Models\QrConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MealSlot>
 */
class MealSlotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'qr_config_id' => QrConfig::factory(),
            'slot' => MealSlotType::Lunch,
            'starts_at' => '12:00',
            'ends_at' => '14:00',
            'cutoff_time' => '10:00',
            'is_active' => true,
        ];
    }
}
