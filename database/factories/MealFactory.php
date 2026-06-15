<?php

namespace Database\Factories;

use App\Models\Meal;
use App\Models\Outlet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Meal>
 */
class MealFactory extends Factory
{
    public function definition(): array
    {
        return [
            'outlet_id' => Outlet::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 5, 15),
            'available_slots' => null,
            'is_vegetarian' => false,
            'is_active' => true,
        ];
    }

    public function vegetarian(): static
    {
        return $this->state(['is_vegetarian' => true]);
    }
}
