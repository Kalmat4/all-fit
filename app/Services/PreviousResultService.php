<?php

namespace App\Services;

use App\Enums\WorkoutSessionStatusEnum;
use App\Models\WorkoutSessionExercise;
use Illuminate\Support\Facades\Auth;

class PreviousResultService
{
    // Возвращает последние результаты по упражнению для текущего пользователя
    public function getForExercise(int $exerciseId): array
    {
        $previous = WorkoutSessionExercise::query()
            ->where('exercise_id', $exerciseId)
            ->whereHas(
                'session',
                fn($q) => $q
                    ->where('user_id', Auth::id())
                    ->where('status', WorkoutSessionStatusEnum::Completed)
            )
            ->with('sets')
            ->latest()
            ->first();

        if (!$previous) {
            return [];
        }

        return [
            'comm' => $previous->comm,
            'sets' => $previous->sets->map(fn($s) => [
                'set_number' => $s->set_number,
                'reps'       => $s->reps,
                'weight'     => $s->weight,
            ])->toArray()
        ];
    }
}
