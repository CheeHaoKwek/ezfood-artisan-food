<?php

namespace App\Http\Controllers\App;

use App\Enums\MealSlotType;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Services\MealSelectionService;
use App\Services\QrConfigService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MealController extends Controller
{
    public function __construct(
        private QrConfigService $qrConfigService,
        private MealSelectionService $mealSelectionService,
    ) {}

    public function index(string $code): View|RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->activeSubscriptionFor($qrConfig);

        if ($subscription === null) {
            return redirect()->route('app.subscribe.create', $qrConfig->code);
        }

        $today = Carbon::today($qrConfig->outlet->timezone);

        return view('app.meals', [
            'qrConfig' => $qrConfig,
            'subscription' => $subscription,
            'today' => $today,
            'meals' => Meal::query()
                ->where('outlet_id', $qrConfig->outlet_id)
                ->where('is_active', true)
                ->forDietaryPreference($user->dietary_preference)
                ->get(),
            'selections' => $subscription->mealSelections()
                ->whereDate('serve_date', '>=', $today->toDateString())
                ->with('meal')
                ->get(),
        ]);
    }

    public function store(Request $request, string $code): RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $subscription = $user->activeSubscriptionFor($qrConfig);

        if ($subscription === null) {
            return redirect()->route('app.subscribe.create', $qrConfig->code);
        }

        $data = $request->validate([
            'meal_id' => ['required', Rule::exists('meals', 'id')->where('outlet_id', $qrConfig->outlet_id)],
            'serve_date' => ['required', 'date'],
            'slot' => ['required', Rule::enum(MealSlotType::class)],
        ]);

        $this->mealSelectionService->select(
            $subscription,
            Meal::findOrFail($data['meal_id']),
            Carbon::parse($data['serve_date'], $qrConfig->outlet->timezone),
            MealSlotType::from($data['slot']),
        );

        return redirect()->route('app.meals.index', $qrConfig->code)
            ->with('status', 'Meal selected.');
    }
}
