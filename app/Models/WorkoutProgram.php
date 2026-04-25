<?php

namespace App\Models;

use App\Enums\WorkoutProgramLevelEnum;
use App\Enums\WorkoutProgramTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkoutProgram extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'type',
        'level',
        'is_system',
    ];

    protected $casts = [
        'type'      => WorkoutProgramTypeEnum::class,
        'level'     => WorkoutProgramLevelEnum::class,
        'is_system' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function programExercises(): HasMany
    {
        return $this->hasMany(WorkoutProgramExercise::class)->orderBy('order');
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}