@extends('layouts.app')

@section('title', 'Sign in — '.$qrConfig->outlet->name)

@section('content')
    <div class="mx-auto" style="max-width: 480px;">
        <header class="text-center mb-4">
            <p class="text-uppercase text-muted small mb-1">EzFood</p>
            <h1 class="h4 mb-1">{{ $qrConfig->outlet->name }}</h1>
            <p class="text-muted small mb-0">Daily meals, delivered to your workplace.</p>
        </header>

        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Sign in</h2>
                <form method="POST" action="{{ route('app.auth.login', $qrConfig->code) }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="login-email" class="form-label">Email</label>
                        <input type="email" name="email" id="login-email" inputmode="email" autocomplete="email"
                               value="{{ old('email') }}"
                               class="form-control form-control-lg @error('email', 'login') is-invalid @enderror" required>
                        @error('email', 'login')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="login-password" class="form-label">Password</label>
                        <div class="input-group input-group-lg has-validation">
                            <input type="password" name="password" id="login-password" autocomplete="current-password"
                                   class="form-control @error('password', 'login') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="login-password" aria-label="Show password">
                                @include('app.partials.eye-icon')
                            </button>
                            @error('password', 'login')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button class="btn btn-primary btn-lg w-100">Sign in</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm" id="signup" data-has-errors="{{ $errors->getBag('register')->any() ? '1' : '' }}">
            <div class="card-body p-4">
                <h2 class="h5 mb-1">First time here?</h2>
                <p class="text-muted small mb-3">Create your account to start ordering meals at {{ $qrConfig->outlet->name }}.</p>
                <form method="POST" action="{{ route('app.auth.register', $qrConfig->code) }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="reg-name" class="form-label">Full name</label>
                        <input type="text" name="name" id="reg-name" autocomplete="name"
                               value="{{ old('name') }}"
                               class="form-control form-control-lg @error('name', 'register') is-invalid @enderror" required>
                        @error('name', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reg-nickname" class="form-label">Nickname</label>
                        <input type="text" name="nickname" id="reg-nickname" autocomplete="nickname"
                               value="{{ old('nickname') }}"
                               class="form-control form-control-lg @error('nickname', 'register') is-invalid @enderror" required>
                        @error('nickname', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reg-mobile" class="form-label">Mobile number</label>
                        <input type="tel" name="mobile_number" id="reg-mobile" inputmode="tel" autocomplete="tel"
                               value="{{ old('mobile_number') }}"
                               class="form-control form-control-lg @error('mobile_number', 'register') is-invalid @enderror" required>
                        @error('mobile_number', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reg-email" class="form-label">Email</label>
                        <input type="email" name="email" id="reg-email" inputmode="email" autocomplete="email"
                               value="{{ old('email') }}"
                               class="form-control form-control-lg @error('email', 'register') is-invalid @enderror" required>
                        @error('email', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reg-password" class="form-label">Password</label>
                        <div class="input-group input-group-lg has-validation">
                            <input type="password" name="password" id="reg-password" autocomplete="new-password"
                                   class="form-control @error('password', 'register') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary" type="button" data-toggle-password="reg-password" aria-label="Show password">
                                @include('app.partials.eye-icon')
                            </button>
                            @error('password', 'register')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">At least 8 characters.</div>
                    </div>

                    <div class="mb-3">
                        <label for="reg-password-confirm" class="form-label">Confirm password</label>
                        <input type="password" name="password_confirmation" id="reg-password-confirm" autocomplete="new-password"
                               class="form-control form-control-lg" required>
                    </div>

                    <div class="mb-3">
                        <label for="reg-company" class="form-label">Company name</label>
                        <input type="text" name="company_name" id="reg-company" autocomplete="organization"
                               value="{{ old('company_name') }}"
                               class="form-control form-control-lg @error('company_name', 'register') is-invalid @enderror" required>
                        @error('company_name', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="reg-dietary" class="form-label">Dietary preference</label>
                        <select name="dietary_preference" id="reg-dietary"
                                class="form-select form-select-lg @error('dietary_preference', 'register') is-invalid @enderror" required>
                            <option value="" disabled @selected(old('dietary_preference') === null)>Choose…</option>
                            @foreach (\App\Enums\DietaryPreference::cases() as $preference)
                                <option value="{{ $preference->value }}" @selected(old('dietary_preference') === $preference->value)>
                                    {{ $preference->getLabel() }}
                                </option>
                            @endforeach
                        </select>
                        @error('dietary_preference', 'register')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button class="btn btn-outline-primary btn-lg w-100">Create account</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.getAttribute('data-toggle-password'));
                var show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                button.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
            });
        });

        var signup = document.getElementById('signup');
        if (signup.dataset.hasErrors) {
            signup.scrollIntoView();
        }
    </script>
@endsection
