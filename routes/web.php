<?php

use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PublicLandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', PublicLandingController::class);

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::prefix('admin')->group(function (): void {
        Route::get('dashboard', DashboardController::class)->name('dashboard');

        Route::resource('customers', CustomerController::class)->except(['show', 'destroy']);
        Route::resource('assets', AssetController::class)->only(['index', 'create', 'store']);
        Route::get('assets/{asset}/assign', [AssetAssignmentController::class, 'create'])->name('assets.assignments.create');
        Route::post('assets/{asset}/assign', [AssetAssignmentController::class, 'store'])->name('assets.assignments.store');
    });
});
