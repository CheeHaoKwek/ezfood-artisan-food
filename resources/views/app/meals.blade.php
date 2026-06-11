@extends('layouts.app')

@section('title', 'Select meals — '.$qrConfig->outlet->name)

@section('content')
    <h1 class="h4 mb-1">{{ $qrConfig->outlet->name }}</h1>
    <p class="text-muted">Plan: {{ $subscription->starts_on->format('d M') }} – {{ $subscription->ends_on->format('d M') }}</p>

    {{-- Placeholder selection UI: per-slot selection screens come from the meal-selection tickets --}}
    <form method="POST" action="{{ route('app.meals.store', $qrConfig->code) }}" class="card card-body mb-3">
        @csrf
        <input type="date" name="serve_date" value="{{ $today->toDateString() }}" class="form-control mb-2" required>
        <select name="slot" class="form-select mb-2" required>
            @foreach ($qrConfig->mealSlots as $slot)
                <option value="{{ $slot->slot->value }}" class="text-capitalize">{{ ucfirst($slot->slot->value) }}</option>
            @endforeach
        </select>
        <select name="meal_id" class="form-select mb-2" required>
            @foreach ($meals as $meal)
                <option value="{{ $meal->id }}">{{ $meal->name }} (RM{{ $meal->price }})</option>
            @endforeach
        </select>
        <button class="btn btn-primary">Select meal</button>
    </form>

    <h2 class="h6">Your selections</h2>
    <ul class="list-group">
        @forelse ($selections as $selection)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $selection->serve_date->format('D d M') }} &middot; {{ ucfirst($selection->slot->value) }} &middot; {{ $selection->meal->name }}</span>
                <span class="badge text-bg-secondary">{{ $selection->status->value }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted">No meals selected yet.</li>
        @endforelse
    </ul>
@endsection
