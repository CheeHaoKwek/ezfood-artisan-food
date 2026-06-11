<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\QrConfigService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private QrConfigService $qrConfigService,
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

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Remember the session so subscribers are not re-prompted on the same phone.
        if (! Auth::attempt($credentials, remember: true)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $request->session()->regenerate();

        return redirect()->route('app.entry', $qrConfig->code);
    }

    public function register(Request $request, string $code): RedirectResponse
    {
        $qrConfig = $this->qrConfigService->resolveActiveByCode($code);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create($data);

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
