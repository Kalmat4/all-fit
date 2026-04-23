<?php

namespace App\Models;

use App\Enums\ExerciseCategoryEnum;
use App\Enums\ExerciseTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'type',
        'description',
        'is_system',
    ];

    protected $casts = [
        'category'  => ExerciseCategoryEnum::class,
        'type'      => ExerciseTypeEnum::class,
        'is_system' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Системные упражнения (доступны всем)
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    // Упражнения конкретного пользователя
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}