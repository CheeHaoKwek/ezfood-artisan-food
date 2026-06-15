<?php

namespace App\Http\Controllers\App;

use App\Enums\DietaryPreference;
use App\Http\Controllers\Controller;
use App\Services\QrConfigService;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private QrConfigService $qrConfigService,
        private RegistrationService $registrationService,
    ) {}

    public function show(string $code): View
    {
        return view('app.auth', [
            'qrConfig' => $this->qrConfigService->resolveActiveByCode($code),
        ]);
    }

    public function login(Request $request, string $code): RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        // Named bag: the auth page renders sign-in and sign-up forms sharing
        // field names, so each form reads only its own errors.
        $credentials = $request->validateWithBag('login', [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Remember the session so subscribers are not re-prompted on the same phone.
        if (! Auth::attempt($credentials, remember: true)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')])
                ->errorBag('login');
        }

        $request->session()->regenerate();

        return redirect()->route('app.entry', $qrConfig->code);
    }

    public function register(Request $request, string $code): RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        $data = $request->validateWithBag('register', [
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'company_name' => ['required', 'string', 'max:255'],
            'dietary_preference' => ['required', Rule::enum(DietaryPreference::class)],
        ]);

        $user = $this->registrationService->register($qrConfig, $data);

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->route('app.entry', $qrConfig->code);
    }

    public function logout(Request $request, string $code): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('app.entry', $code);
    }
}
