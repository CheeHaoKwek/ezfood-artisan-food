<?php

namespace Tests\Feature\App;

use App\Enums\DietaryPreference;
use App\Models\Meal;
use App\Models\QrConfig;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VegetarianMenuTest extends TestCase
{
    use RefreshDatabase;

    private QrConfig $qrConfig;

    private Meal $vegMeal;

    private Meal $nonVegMeal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->qrConfig = QrConfig::factory()->create();
        $this->vegMeal = Meal::factory()->vegetarian()->create([
            'outlet_id' => $this->qrConfig->outlet_id,
            'name' => 'Veg Delight Bowl',
        ]);
        $this->nonVegMeal = Meal::factory()->create([
            'outlet_id' => $this->qrConfig->outlet_id,
            'name' => 'Ayam Goreng Special',
        ]);
    }

    private function subscriberWithPreference(?DietaryPreference $preference): User
    {
        $user = User::factory()->create([
            'dietary_preference' => $preference?->value,
            'outlet_id' => $this->qrConfig->outlet_id,
        ]);

        Subscription::factory()->create([
            'user_id' => $user->id,
            'qr_config_id' => $this->qrConfig->id,
        ]);

        return $user;
    }

    public function test_vegetarian_subscriber_sees_only_vegetarian_meals(): void
    {
        $user = $this->subscriberWithPreference(DietaryPreference::Vegetarian);

        $this->actingAs($user)
            ->get(route('app.meals.index', $this->qrConfig->code))
            ->assertOk()
            ->assertSee('Veg Delight Bowl')
            ->assertDontSee('Ayam Goreng Special');
    }

    public function test_non_vegetarian_subscriber_sees_all_meals(): void
    {
        $user = $this->subscriberWithPreference(DietaryPreference::NonVegetarian);

        $this->actingAs($user)
            ->get(route('app.meals.index', $this->qrConfig->code))
            ->assertOk()
            ->assertSee('Veg Delight Bowl')
            ->assertSee('Ayam Goreng Special');
    }

    public function test_subscriber_without_preference_sees_all_meals(): void
    {
        $user = $this->subscriberWithPreference(null);

        $this->actingAs($user)
            ->get(route('app.meals.index', $this->qrConfig->code))
            ->assertOk()
            ->assertSee('Veg Delight Bowl')
            ->assertSee('Ayam Goreng Special');
    }
}
