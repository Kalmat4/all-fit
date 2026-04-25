<?php

namespace App\Enums;

enum WorkoutProgramLevelEnum: string
{
    case Beginner     = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced     = 'advanced';

    public function label(): string
    {
        return match($this) {
            self::Beginner     => 'Начинающий',
            self::Intermediate => 'Средний',
            self::Advanced     => 'Продвинутый',
        };
    }
}