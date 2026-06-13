<?php

namespace App\Enums;

enum ExerciseModeEnum: string
{
    case Sets       = 'sets';
    case TargetReps = 'target_reps';

    public function label(): string
    {
        return match($this) {
            self::Sets       => 'По подходам',
            self::TargetReps => 'По цели повторений',
        };
    }
}
