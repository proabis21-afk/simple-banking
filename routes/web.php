<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthController::class,
        'create',
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'store',
    ])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [
        DashboardController::class,
        'index',
    ])->name('dashboard');

    Route::get('/api/dashboard/stats', [
        DashboardController::class,
        'stats',
    ])->name('dashboard.stats');

    Route::resource(
        'transactions',
        TransactionController::class
    )->only([
        'index',
        'create',
        'store',
    ]);

    Route::post('/logout', [
        AuthController::class,
        'destroy',
    ])->name('logout');
});