<?php

namespace App\Services;

use App\Enums\ConsolidatedOrderStatus;
use App\Enums\MealSlotType;
use App\Enums\SelectionStatus;
use App\Models\ConsolidatedOrder;
use App\Models\MealSelection;
use App\Models\Outlet;
use App\Models\QrConfig;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OrderConsolidationService
{
    public function __construct(
        private CutoffService $cutoffService,
    ) {}

    /**
     * Find every outlet slot whose cut-off has passed today and consolidate it.
     * Runs from the scheduler; safe to call repeatedly (one order per outlet/date/slot).
     *
     * @return Collection<int, ConsolidatedOrder>
     */
    public function consolidateDue(): Collection
    {
        $orders = collect();

        $configs = QrConfig::query()
            ->where('is_active', true)
            ->whereHas('outlet', fn ($query) => $query->where('is_active', true))
            ->with(['outlet', 'mealSlots' => fn ($query) => $query->where('is_active', true)])
            ->get();

        foreach ($configs as $config) {
            $today = Carbon::today($config->outlet->timezone);

            if (! $config->operation_days->includes($today)) {
                continue;
            }

            foreach ($config->mealSlots as $mealSlot) {
                if (! $this->cutoffService->isPastCutoff($config, $today, $mealSlot->slot)) {
                    continue;
                }

                $order = $this->consolidate($config->outlet, $today, $mealSlot->slot);

                if ($order !== null) {
                    $orders->push($order);
                }
            }
        }

        return $orders;
    }

    /**
     * Aggregate all pending selections for an outlet's slot into one
     * consolidated order for logistics, then lock the selections.
     */
    public function consolidate(Outlet $outlet, CarbonInterface $serveDate, MealSlotType $slot): ?ConsolidatedOrder
    {
        return DB::transaction(function () use ($outlet, $serveDate, $slot) {
            $selections = MealSelection::query()
                ->whereHas('subscription.qrConfig', fn ($query) => $query->where('outlet_id', $outlet->id))
                ->whereDate('serve_date', $serveDate->toDateString())
                ->where('slot', $slot)
                ->where('status', SelectionStatus::Selected)
                ->with('meal')
                ->lockForUpdate()
                ->get();

            if ($selections->isEmpty()) {
                return null;
            }

            $payload = $selections
                ->groupBy('meal_id')
                ->map(fn (Collection $group) => [
                    'meal_id' => $group->first()->meal_id,
                    'meal_name' => $group->first()->meal->name,
                    'quantity' => $group->count(),
                ])
                ->values()
                ->all();

            $order = ConsolidatedOrder::updateOrCreate(
                [
                    'outlet_id' => $outlet->id,
                    'serve_date' => $serveDate->toDateString(),
                    'slot' => $slot,
                ],
                [
                    'payload' => $payload,
                    'total_meals' => $selections->count(),
                    'status' => ConsolidatedOrderStatus::Pending,
                    'consolidated_at' => now(),
                ],
            );

            MealSelection::whereIn('id', $selections->pluck('id'))
                ->update(['status' => SelectionStatus::Locked->value]);

            return $order;
        });
    }
}
