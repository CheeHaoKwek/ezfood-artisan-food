<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\QrConfigService;
use App\Services\SubscriptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    public function __construct(
        private QrConfigService $qrConfigService,
        private SubscriptionService $subscriptionService,
    ) {}

    public function create(string $code): View|RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        // A user with an active subscription is never shown the subscription flow again.
        if (Auth::user()->activeSubscriptionFor($qrConfig) !== null) {
            return redirect()->route('app.meals.index', $qrConfig->code);
        }

        return view('app.subscribe', ['qrConfig' => $qrConfig]);
    }

    public function store(Request $request, string $code): RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        $this->subscriptionService->subscribe($request->user(), $qrConfig);

        return redirect()->route('app.meals.index', $qrConfig->code)
            ->with('status', 'Subscription started.');
    }
}
