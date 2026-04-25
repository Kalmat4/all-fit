<?php

namespace App\Models;

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
        'order',
    ];

    protected $casts = [
        'weight' => 'float',
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