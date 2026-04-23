<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('exercises', ExerciseController::class);
});