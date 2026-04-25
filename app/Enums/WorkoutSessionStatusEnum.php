<?php

namespace App\Enums;

enum WorkoutSessionStatusEnum: string
{
    case Active    = 'active';
    case Completed = 'completed';

    public function label(): string
    {
        return match($this) {
            self::Active    => 'В процессе',
            self::Completed => 'Завершена',
        };
    }
}