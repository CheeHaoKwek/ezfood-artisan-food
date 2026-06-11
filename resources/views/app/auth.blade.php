@extends('layouts.app')

@section('title', 'Sign in — '.$qrConfig->outlet->name)

@section('content')
    <h1 class="h4 mb-3">{{ $qrConfig->outlet->name }}</h1>

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="h6">Sign in</h2>
            <form method="POST" action="{{ route('app.auth.login', $qrConfig->code) }}">
                @csrf
                <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                <button class="btn btn-primary w-100">Sign in</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h2 class="h6">First time? Sign up</h2>
            <form method="POST" action="{{ route('app.auth.register', $qrConfig->code) }}">
                @csrf
                <input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
                <input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
                <input type="password" name="password" class="form-control mb-2" placeholder="Password" required>
                <input type="password" name="password_confirmation" class="form-control mb-2" placeholder="Confirm password" required>
                <button class="btn btn-outline-primary w-100">Sign up</button>
            </form>
        </div>
    </div>
@endsection
