<?php

use App\Http\Controllers\ALLFIT\DashboardController;
use App\Http\Controllers\ALLFIT\ExerciseController;
use App\Http\Controllers\ALLFIT\WorkoutHistoryController;
use App\Http\Controllers\ALLFIT\WorkoutProgramController;
use App\Http\Controllers\ALLFIT\WorkoutSessionController;
use App\Http\Controllers\Certificate\CertificateController;
use App\Http\Controllers\SettingsController;
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
    Route::delete('/history/{workoutSession}', [WorkoutHistoryController::class, 'destroy'])->name('workout-history.destroy');

    // Настройки
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
});


Route::get('/certificate',               [CertificateController::class, 'index'])->name('certificate.index');
Route::post('/certificate/download-word', [CertificateController::class, 'downloadWord'])->name('certificate.word');
Route::post('/certificate/download-pdf',  [CertificateController::class, 'downloadPdf'])->name('certificate.pdf');
