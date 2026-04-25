<?php

namespace App\Services;

use App\Enums\WorkoutSessionStatusEnum;
use App\Models\WorkoutProgram;
use App\Models\WorkoutSession;
use Illuminate\Support\Facades\Auth;

class WorkoutSessionService
{
    // Старт тренировки — копируем программу в сессию (снапшот)
    public function startFromProgram(WorkoutProgram $program): WorkoutSession
    {
        $session = WorkoutSession::create([
            'user_id'           => Auth::id(),
            'workout_program_id' => $program->id,
            'program_name'      => $program->name,
            'status'            => WorkoutSessionStatusEnum::Active,
            'started_at'        => now(),
        ]);

        foreach ($program->programExercises()->with('exercise')->get() as $pe) {
            $session->sessionExercises()->create([
                'exercise_id'    => $pe->exercise_id,
                'exercise_name'  => $pe->exercise->name,
                'planned_sets'   => $pe->sets,
                'planned_reps'   => $pe->reps,
                'planned_weight' => $pe->weight,
                'order'          => $pe->order,
            ]);
        }

        return $session;
    }

    // Завершить тренировку
    public function complete(WorkoutSession $session): void
    {
        $session->update([
            'status'       => WorkoutSessionStatusEnum::Completed,
            'completed_at' => now(),
        ]);
    }

    // Сохранить подход
    public function saveSet(int $sessionExerciseId, int $setNumber, string $reps, ?float $weight): void
    {
        \App\Models\WorkoutSessionSet::updateOrCreate(
            [
                'workout_session_exercise_id' => $sessionExerciseId,
                'set_number'                  => $setNumber,
            ],
            [
                'reps'         => $reps,
                'weight'       => $weight,
                'completed_at' => now(),
            ]
        );
    }
    
    // Сохранить комментарий
    public function saveComm(int $sessionExerciseId, string $comm): void
    {
        \App\Models\WorkoutSessionExercise::find($sessionExerciseId)->update(
            [
                'comm'                  => $comm,
            ]
        );
    }
}