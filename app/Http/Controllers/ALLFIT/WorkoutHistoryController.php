<?php

namespace App\Http\Controllers\ALLFIT;

use App\Enums\WorkoutSessionStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutHistoryController extends Controller
{
    public function index(): Response
    {
        $sessions = WorkoutSession::query()
            ->where('user_id', Auth::id())
            ->where('status', WorkoutSessionStatusEnum::Completed)
            ->withCount('sessionExercises')
            ->orderByDesc('completed_at')
            ->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'program_name'     => $s->program_name,
                'completed_at'     => $s->completed_at->format('d.m.Y H:i'),
                'duration_minutes' => $s->started_at
                    ? (int) $s->started_at->diffInMinutes($s->completed_at)
                    : null,
                'exercises_count'  => $s->session_exercises_count,
                'started_at'       => $s->started_at?->format('H:i'),
                'completed_date'   => $s->completed_at->format('d.m.Y'),
                'day_of_week'      => $s->completed_at->locale('ru')->dayName,
            ]);

        return Inertia::render('WorkoutHistory/Index', [
            'sessions' => $sessions,

        ]);
    }

    public function destroy(WorkoutSession $workoutSession): \Illuminate\Http\RedirectResponse
    {
        abort_if($workoutSession->user_id !== Auth::id(), 403);

        $workoutSession->delete();

        return redirect()->route('workout-history.index');
    }

    public function show(WorkoutSession $workoutSession): Response
    {
        abort_if($workoutSession->user_id !== Auth::id(), 403);

        $exercises = $workoutSession->sessionExercises()
            ->with('sets')
            ->get()
            ->map(fn($se) => [
                'id'             => $se->id,
                'exercise_name'  => $se->exercise_name,
                'planned_sets'   => $se->planned_sets,
                'planned_reps'   => $se->planned_reps,
                'planned_weight' => $se->planned_weight,
                'comm'           => $se->comm,
                'sets'           => $se->sets->map(fn($s) => [
                    'set_number' => $s->set_number,
                    'reps'       => $s->reps,
                    'weight'     => $s->weight,
                ]),
            ]);

        return Inertia::render('WorkoutHistory/Show', [
            'session' => [
                'id'               => $workoutSession->id,
                'program_name'     => $workoutSession->program_name,
                'completed_at'     => $workoutSession->completed_at->format('d.m.Y H:i'),
                'duration_minutes' => $workoutSession->started_at
                    ? (int) $workoutSession->started_at->diffInMinutes($workoutSession->completed_at)
                    : null,
            ],
            'exercises' => $exercises,
        ]);
    }
}
