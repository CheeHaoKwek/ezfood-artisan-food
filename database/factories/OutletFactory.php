<?php

namespace Database\Factories;

use App\Enums\OutletType;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Outlet>
 */
class OutletFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => OutletType::Factory,
            'address' => fake()->address(),
            'contact_person' => fake()->name(),
            'contact_phone' => fake()->phoneNumber(),
            'timezone' => 'Asia/Kuala_Lumpur',
            'is_active' => true,
        ];
    }
}
