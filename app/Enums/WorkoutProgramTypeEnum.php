<?php

namespace App\Enums;

enum WorkoutProgramTypeEnum: string
{
    case Calisthenics = 'calisthenics';
    case Weighted     = 'weighted';
    case Mixed        = 'mixed';

    public function label(): string
    {
        return match($this) {
            self::Calisthenics => 'Калистеника',
            self::Weighted     => 'С весом',
            self::Mixed        => 'Смешанный',
        };
    }
}