<?php

use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Dashboard')->name('dashboard');
Route::resource('customers', CustomerController::class)->except(['show', 'destroy']);
Route::resource('assets', AssetController::class)->only(['index', 'create', 'store']);
Route::get('assets/{asset}/assign', [AssetAssignmentController::class, 'create'])->name('assets.assignments.create');
Route::post('assets/{asset}/assign', [AssetAssignmentController::class, 'store'])->name('assets.assignments.store');
