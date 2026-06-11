<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Models\QrConfig;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Start a weekly plan for the user under the scanned QR config.
     * A user with an active subscription is never re-subscribed.
     */
    public function subscribe(User $user, QrConfig $qrConfig): Subscription
    {
        if ($existing = $user->activeSubscriptionFor($qrConfig)) {
            return $existing;
        }

        $startsOn = Carbon::today($qrConfig->outlet->timezone);

        return Subscription::create([
            'user_id' => $user->id,
            'qr_config_id' => $qrConfig->id,
            'starts_on' => $startsOn->toDateString(),
            'ends_on' => $startsOn->copy()->addDays(6)->toDateString(),
            'status' => SubscriptionStatus::Active,
        ]);
    }

    /**
     * Mark active subscriptions whose weekly plan has ended as expired.
     * Runs from the scheduler.
     */
    public function expireLapsed(): int
    {
        return Subscription::query()
            ->where('status', SubscriptionStatus::Active)
            ->whereDate('ends_on', '<', today()->toDateString())
            ->update(['status' => SubscriptionStatus::Expired]);
    }
}
