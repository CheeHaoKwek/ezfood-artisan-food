<?php

namespace App\Services;

use App\Enums\MealSlotType;
use App\Enums\SelectionStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Meal;
use App\Models\MealSelection;
use App\Models\Subscription;
use Carbon\CarbonInterface;
use Illuminate\Validation\ValidationException;

class MealSelectionService
{
    public function __construct(
        private CutoffService $cutoffService,
    ) {}

    /**
     * Record (or replace) the subscriber's meal choice for a serve date and slot,
     * enforcing the QR config's operation days, active slots, and cut-off time.
     */
    public function select(
        Subscription $subscription,
        Meal $meal,
        CarbonInterface $serveDate,
        MealSlotType $slot,
    ): MealSelection {
        $config = $subscription->qrConfig->load(['outlet', 'mealSlots']);

        if ($subscription->status !== SubscriptionStatus::Active) {
            throw ValidationException::withMessages(['subscription' => 'Subscription is not active.']);
        }

        if ($serveDate->lt($subscription->starts_on) || $serveDate->gt($subscription->ends_on)) {
            throw ValidationException::withMessages(['serve_date' => 'Date is outside the current weekly plan.']);
        }

        if (! $config->operation_days->includes($serveDate)) {
            throw ValidationException::withMessages(['serve_date' => 'The outlet does not operate on this day.']);
        }

        $configSlot = $config->mealSlots->firstWhere('slot', $slot);

        if ($configSlot === null || ! $configSlot->is_active) {
            throw ValidationException::withMessages(['slot' => 'This meal slot is not available at this outlet.']);
        }

        if ($this->cutoffService->isPastCutoff($config, $serveDate, $slot)) {
            throw ValidationException::withMessages(['slot' => 'The cut-off time for this slot has passed.']);
        }

        if ($meal->outlet_id !== $config->outlet_id || ! $meal->is_active || ! $meal->availableForSlot($slot)) {
            throw ValidationException::withMessages(['meal' => 'This meal is not available for the selected slot.']);
        }

        return MealSelection::updateOrCreate(
            [
                'subscription_id' => $subscription->id,
                'serve_date' => $serveDate->toDateString(),
                'slot' => $slot,
            ],
            [
                'meal_id' => $meal->id,
                'status' => SelectionStatus::Selected,
            ],
        );
    }
}
