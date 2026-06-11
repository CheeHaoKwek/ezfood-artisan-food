<?php

use App\Http\Controllers\App\AuthController;
use App\Http\Controllers\App\MealController as AppMealController;
use App\Http\Controllers\App\QrEntryController;
use App\Http\Controllers\App\SubscriptionController as AppSubscriptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Filament handles /cms and /cms/login — this fallback catches anything
// outside the QR scope that tries to hit the auth middleware redirect.
Route::get('login', fn () => redirect('/cms/login'))->name('login');

/*
|--------------------------------------------------------------------------
| Mobile web app — subscribers (workers / tenants)
|--------------------------------------------------------------------------
| Every page is scoped to the outlet QR config identified by {code}.
| The QR config is the source of truth for what the subscriber can see.
*/
Route::prefix('qr/{code}')->name('app.')->group(function () {
    Route::get('/', QrEntryController::class)->name('entry');

    Route::get('auth', [AuthController::class, 'show'])->name('auth.show');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::get('subscribe', [AppSubscriptionController::class, 'create'])->name('subscribe.create');
        Route::post('subscribe', [AppSubscriptionController::class, 'store'])->name('subscribe.store');

        Route::get('meals', [AppMealController::class, 'index'])->name('meals.index');
        Route::post('meals', [AppMealController::class, 'store'])->name('meals.store');
    });
});

// CMS is served by Filament at /cms — no web routes needed here.
