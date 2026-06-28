<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Administrator-only routes
Route::middleware(['auth', 'can:is-admin'])->group(function () {
    // Routes added in future phases
});

// Operator and Administrator routes
Route::middleware(['auth', 'can:is-operator'])->group(function () {
    // Routes added in future phases
});

require __DIR__.'/auth.php';
