<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\WorkoutHistoryController;
use App\Http\Controllers\WorkoutProgramController;
use App\Http\Controllers\WorkoutSessionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('exercises', ExerciseController::class);
    Route::resource('workout-programs', WorkoutProgramController::class);


    // Сессии
    Route::post('/sessions/start/{workoutProgram}', [WorkoutSessionController::class, 'start'])->name('workout-sessions.start');
    Route::get('/sessions/{workoutSession}', [WorkoutSessionController::class, 'show'])->name('workout-sessions.show');
    Route::post('/sessions/{sessionExercise}/set', [WorkoutSessionController::class, 'saveSet'])->name('workout-sessions.save-set');
    Route::post('/sessions/{sessionExercise}/comm', [WorkoutSessionController::class, 'saveComm'])->name('workout-sessions.save-comm');
    Route::post('/sessions/{workoutSession}/complete', [WorkoutSessionController::class, 'complete'])->name('workout-sessions.complete');

    // История
    Route::get('/history', [WorkoutHistoryController::class, 'index'])->name('workout-history.index');
    Route::get('/history/{workoutSession}', [WorkoutHistoryController::class, 'show'])->name('workout-history.show');
});