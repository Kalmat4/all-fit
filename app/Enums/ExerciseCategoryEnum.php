<?php

namespace App\Enums;

enum ExerciseCategoryEnum: string
{
    case Chest       = 'chest';
    case Back        = 'back';
    case Shoulders   = 'shoulders';
    case Arms        = 'arms';
    case Core        = 'core';
    case Legs        = 'legs';
    case FullBody    = 'full_body';
    case Cardio      = 'cardio';

    public function label(): string
    {
        return match($this) {
            self::Chest      => 'Грудь',
            self::Back       => 'Спина',
            self::Shoulders  => 'Плечи',
            self::Arms       => 'Руки',
            self::Core       => 'Пресс',
            self::Legs       => 'Ноги',
            self::FullBody   => 'Всё тело',
            self::Cardio     => 'Кардио',
        };
    }
}