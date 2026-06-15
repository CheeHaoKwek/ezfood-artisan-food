<?php

namespace Database\Factories;

use App\Enums\CutoffBasis;
use App\Enums\MealMode;
use App\Enums\OperationDays;
use App\Models\Outlet;
use App\Models\QrConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QrConfig>
 */
class QrConfigFactory extends Factory
{
    public function definition(): array
    {
        // `code` is intentionally omitted — the model's creating hook assigns a ULID.
        return [
            'outlet_id' => Outlet::factory(),
            'operation_days' => OperationDays::MonToSun,
            'is_24_hours' => true,
            'meal_mode' => MealMode::MultiplePerDay,
            'cutoff_basis' => CutoffBasis::PerSlot,
            'delivery_location' => 'Canteen',
            'is_active' => true,
        ];
    }
}
