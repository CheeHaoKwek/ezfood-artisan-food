<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Consolidation runs frequently so each outlet slot is picked up shortly after its cut-off.
Schedule::command('orders:consolidate')->everyFiveMinutes();

// Weekly plans lapse at midnight; expire them early in the day.
Schedule::command('subscriptions:expire')->dailyAt('00:05');
