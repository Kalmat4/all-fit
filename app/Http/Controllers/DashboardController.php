<?php

namespace App\Http\Controllers;

use App\Enums\WorkoutSessionStatusEnum;
use App\Models\WorkoutProgram;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class DashboardController extends Controller
{
    public function index(): InertiaResponse
    {
        $userId = Auth::id();

        // Активная сессия (если есть)
        $todaySession = WorkoutSession::where('user_id', $userId)
            ->where('status', WorkoutSessionStatusEnum::Active)
            ->latest()
            ->first();

        // Последние 3 программы пользователя + системные
        $recentPrograms = WorkoutProgram::query()
            ->where(function ($q) use ($userId) {
                $q->where('is_system', true)
                    ->orWhere('user_id', $userId);
            })
            ->withCount('programExercises')
            ->orderBy('is_system', 'asc')
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'exercises_count' => $p->program_exercises_count,
            ]);

        // Последние 3 завершённые тренировки
        $recentHistory = WorkoutSession::where('user_id', $userId)
            ->where('status', WorkoutSessionStatusEnum::Completed)
            ->orderByDesc('completed_at')
            ->limit(3)
            ->get()
            ->map(fn($s) => [
                'id'           => $s->id,
                'program_name' => $s->program_name,
                'completed_at' => $s->completed_at->format('d.m.Y'),
                'day_of_week'  => $s->completed_at->locale('ru')->dayName,
            ]);

        return Inertia::render('Dashboard/Index', [
            'todaySession'  => $todaySession ? [
                'id'           => $todaySession->id,
                'program_name' => $todaySession->program_name,
            ] : null,
            'recentPrograms' => $recentPrograms,
            'recentHistory'  => $recentHistory,
        ]);
    }
}
