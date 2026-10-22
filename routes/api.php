<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard/stats', [
        DashboardController::class,
        'stats',
    ])->name('dashboard.stats');
});
