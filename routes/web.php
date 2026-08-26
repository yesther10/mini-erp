<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Dashboard')->name('dashboard');
Route::resource('customers', CustomerController::class)->except(['show', 'destroy']);
