<?php

namespace App\Models;

use App\Enums\ExerciseModeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutProgramExercise extends Model
{
    protected $fillable = [
        'workout_program_id',
        'exercise_id',
        'sets',
        'reps',
        'weight',
        'mode',
        'target_reps',
        'order',
    ];

    protected $casts = [
        'weight'      => 'float',
        'mode'        => ExerciseModeEnum::class,
        'target_reps' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(WorkoutProgram::class, 'workout_program_id');
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class);
    }
}