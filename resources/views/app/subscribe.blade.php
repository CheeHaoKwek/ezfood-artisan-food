@extends('layouts.app')

@section('title', 'Subscribe — '.$qrConfig->outlet->name)

@section('content')
    <h1 class="h4 mb-1">{{ $qrConfig->outlet->name }}</h1>
    <p class="text-muted">Weekly meal plan &middot; {{ $qrConfig->meal_mode->value === 'single' ? '1 meal per day' : 'Multiple meals per day' }}</p>

    <ul class="list-group mb-3">
        @foreach ($qrConfig->mealSlots as $slot)
            <li class="list-group-item d-flex justify-content-between">
                <span class="text-capitalize">{{ $slot->slot->value }}</span>
                <span class="text-muted">cut-off {{ $slot->cutoff_time }}</span>
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('app.subscribe.store', $qrConfig->code) }}">
        @csrf
        <button class="btn btn-primary w-100">Start weekly subscription</button>
    </form>
@endsection
