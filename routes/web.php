<?php

use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Dashboard')->name('dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('customers', CustomerController::class)->except(['show', 'destroy']);
    Route::resource('assets', AssetController::class)->only(['index', 'create', 'store']);
    Route::get('assets/{asset}/assign', [AssetAssignmentController::class, 'create'])->name('assets.assignments.create');
    Route::post('assets/{asset}/assign', [AssetAssignmentController::class, 'store'])->name('assets.assignments.store');
});
