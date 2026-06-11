<?php

namespace App\Services;

use App\Enums\CutoffBasis;
use App\Enums\MealSlotType;
use App\Models\QrConfig;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class CutoffService
{
    /**
     * The cut-off moment for a slot on a given serve date, in the outlet's timezone.
     * Returns null when the slot is not configured for this QR config.
     */
    public function cutoffFor(QrConfig $config, CarbonInterface $serveDate, MealSlotType $slot): ?Carbon
    {
        $timezone = $config->outlet->timezone;

        $time = $config->cutoff_basis === CutoffBasis::PerDay
            ? $config->daily_cutoff_time
            : $config->mealSlots->firstWhere('slot', $slot)?->cutoff_time;

        if ($time === null) {
            return null;
        }

        return Carbon::parse($serveDate->toDateString().' '.$time, $timezone);
    }

    public function isPastCutoff(QrConfig $config, CarbonInterface $serveDate, MealSlotType $slot): bool
    {
        $cutoff = $this->cutoffFor($config, $serveDate, $slot);

        return $cutoff !== null && Carbon::now($config->outlet->timezone)->gte($cutoff);
    }
}
