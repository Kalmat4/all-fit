<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkoutSessionSetRequest;
use App\Models\WorkoutProgram;
use App\Models\WorkoutSession;
use App\Models\WorkoutSessionExercise;
use App\Services\PreviousResultService;
use App\Services\WorkoutSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class WorkoutSessionController extends Controller
{
    public function __construct(
        private WorkoutSessionService $sessionService,
        private PreviousResultService $previousResultService,
    ) {}

    // Старт тренировки из программы
    public function start(WorkoutProgram $workoutProgram): RedirectResponse
    {
        // Проверяем — нет ли уже активной сессии
        $existing = WorkoutSession::where('user_id', Auth::id())
            ->where('status', 'active')
            ->first();

        if ($existing) {
            return redirect()->route('workout-sessions.show', $existing->id);
        }

        $session = $this->sessionService->startFromProgram($workoutProgram);

        return redirect()->route('workout-sessions.show', $session->id);
    }

    // Страница активной тренировки
    public function show(WorkoutSession $workoutSession): Response
    {
        abort_if($workoutSession->user_id !== Auth::id(), 403);

        $exercises = $workoutSession->sessionExercises()
            ->with('sets')
            ->get()
            ->map(fn($se) => [
                'id'             => $se->id,
                'exercise_id'    => $se->exercise_id,
                'exercise_name'  => $se->exercise_name,
                'planned_sets'   => $se->planned_sets,
                'planned_reps'   => $se->planned_reps,
                'planned_weight' => $se->planned_weight,
                'comm' => $se->comm,
                'sets'           => $se->sets->map(fn($s) => [
                    'set_number'   => $s->set_number,
                    'reps'         => $s->reps,
                    'weight'       => $s->weight,
                    'completed_at' => $s->completed_at,
                ]),
                'previous' => $se->exercise_id
                    ? $this->previousResultService->getForExercise($se->exercise_id)
                    : [],
            ]);

        return Inertia::render('WorkoutSessions/Show', [
            'session' => [
                'id'           => $workoutSession->id,
                'program_name' => $workoutSession->program_name,
                'status'       => $workoutSession->status->value,
                'started_at'   => $workoutSession->started_at,
            ],
            'exercises' => $exercises,
        ]);
    }

    // Сохранить подход
    public function saveSet(WorkoutSessionSetRequest $request, WorkoutSessionExercise $sessionExercise): \Illuminate\Http\JsonResponse
    {
        abort_if($sessionExercise->session->user_id !== Auth::id(), 403);

        $this->sessionService->saveSet(
            $sessionExercise->id,
            $request->set_number,
            $request->reps,
            $request->weight,
        );

        return response()->json(['ok' => true]);
    }

    // Сохранить комментарий
    public function saveComm(Request $request, WorkoutSessionExercise $sessionExercise): \Illuminate\Http\JsonResponse
    {
        abort_if($sessionExercise->session->user_id !== Auth::id(), 403);


        $sessionExercise->update([
            'comm' => $request->input('comm'),
        ]);


        return response()->json(['ok' => true]);
    }


    public function complete(WorkoutSession $workoutSession): RedirectResponse
    {
        abort_if($workoutSession->user_id !== Auth::id(), 403);

        $this->sessionService->complete($workoutSession);

        return redirect()->route('workout-history.index')
            ->with('success', 'Тренировка завершена!');
    }
}
