<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Livewire\UserSettingsController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/user/settings', [UserSettingsController::class, 'show'])->name('settings.show');
});
