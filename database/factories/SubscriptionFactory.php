<?php

namespace Database\Factories;

use App\Enums\SubscriptionStatus;
use App\Models\QrConfig;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        // Straddle today in the default outlet timezone so activeSubscriptionFor()
        // never flakes around midnight boundaries.
        return [
            'user_id' => User::factory(),
            'qr_config_id' => QrConfig::factory(),
            'starts_on' => now('Asia/Kuala_Lumpur')->subDay()->toDateString(),
            'ends_on' => now('Asia/Kuala_Lumpur')->addDays(6)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ];
    }
}
