<?php

namespace App\Services;

use App\Models\WorkoutProgram;
use Illuminate\Support\Facades\Auth;

class WorkoutProgramService
{
    public function create(array $data): WorkoutProgram
    {
        $program = WorkoutProgram::create([
            'user_id'     => Auth::id(),
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'level'       => $data['level'],
            'is_system'   => false,
        ]);

        $this->syncExercises($program, $data['exercises'] ?? []);

        return $program;
    }

    public function update(WorkoutProgram $program, array $data): WorkoutProgram
    {
        $program->update([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'type'        => $data['type'],
            'level'       => $data['level'],
        ]);

        $this->syncExercises($program, $data['exercises'] ?? []);

        return $program;
    }

    private function syncExercises(WorkoutProgram $program, array $exercises): void
    {
        $program->programExercises()->delete();

        foreach ($exercises as $index => $item) {
            $program->programExercises()->create([
                'exercise_id' => $item['exercise_id'],
                'sets'        => $item['sets'],
                'reps'        => $item['reps'],
                'weight'      => $item['weight'] ?? null,
                'order'       => $index,
            ]);
        }
    }
}