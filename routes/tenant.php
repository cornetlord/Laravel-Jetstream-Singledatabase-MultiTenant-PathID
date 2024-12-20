<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

Route::group([
    'prefix' => '{tenant}',
    'middleware' => ['web', InitializeTenancyByPath::class], // Added 'web' middleware here
], function () {
    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        Route::get('/user/profile', [\Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController::class, 'show'])->name('profile.show');
        Route::get('/user/api-tokens', [\Laravel\Jetstream\Http\Controllers\Livewire\ApiTokenController::class, 'index'])->name('api-tokens.index');
    });
});

