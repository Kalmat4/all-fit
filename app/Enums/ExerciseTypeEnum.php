<?php

namespace App\Enums;

enum ExerciseTypeEnum: string
{
    case Calisthenics = 'calisthenics';
    case Weighted     = 'weighted';
    case Cardio       = 'cardio';
    case Stretching   = 'stretching';

    public function label(): string
    {
        return match($this) {
            self::Calisthenics => 'Калистеника',
            self::Weighted     => 'С весом',
            self::Cardio       => 'Кардио',
            self::Stretching   => 'Растяжка',
        };
    }
}