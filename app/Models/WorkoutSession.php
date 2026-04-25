<?php

namespace App\Models;

use App\Enums\WorkoutSessionStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutSession extends Model
{
    protected $fillable = [
        'user_id',
        'workout_program_id',
        'program_name',
        'status',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'status'       => WorkoutSessionStatusEnum::class,
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(WorkoutProgram::class, 'workout_program_id');
    }

    public function sessionExercises(): HasMany
    {
        return $this->hasMany(WorkoutSessionExercise::class)->orderBy('order');
    }

    public function isActive(): bool
    {
        return $this->status === WorkoutSessionStatusEnum::Active;
    }
}