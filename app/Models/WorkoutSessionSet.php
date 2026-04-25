<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkoutSessionSet extends Model
{
    protected $fillable = [
        'workout_session_exercise_id',
        'set_number',
        'reps',
        'weight',
        'completed_at',
    ];

    protected $casts = [
        'weight'       => 'float',
        'completed_at' => 'datetime',
    ];

    public function sessionExercise(): BelongsTo
    {
        return $this->belongsTo(WorkoutSessionExercise::class, 'workout_session_exercise_id');
    }
}