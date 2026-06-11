<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\QrConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Step 3 entry point — every QR scan lands here and is routed by
 * auth state and subscription state.
 */
class QrEntryController extends Controller
{
    public function __invoke(string $code, QrConfigService $qrConfigService): RedirectResponse
    {
        $qrConfig = $qrConfigService->resolveActiveByCode($code);

        if (! Auth::check()) {
            return redirect()->route('app.auth.show', $qrConfig->code);
        }

        if (Auth::user()->activeSubscriptionFor($qrConfig) === null) {
            return redirect()->route('app.subscribe.create', $qrConfig->code);
        }

        return redirect()->route('app.meals.index', $qrConfig->code);
    }
}
