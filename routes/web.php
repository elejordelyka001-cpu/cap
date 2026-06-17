<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerformanceGoalController;
use App\Http\Controllers\PerformanceTrackingController;
use App\Http\Controllers\PerformanceReviewController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Performance Goals
    Route::resource('goals', PerformanceGoalController::class);

    // Performance Tracking
    Route::post('/goals/{goal}/tracking', [PerformanceTrackingController::class, 'store'])->name('tracking.store');
    Route::get('/goals/{goal}/history', [PerformanceTrackingController::class, 'history'])->name('tracking.history');

    // Performance Reviews
    Route::resource('reviews', PerformanceReviewController::class);
});

Route::redirect('/', '/login');
