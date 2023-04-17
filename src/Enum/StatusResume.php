<?php

namespace App\Enum;

enum StatusResume: int
{
    case DISPONIBLE_IMMEDIATEMENT = 1;
    case OPEN_TO_WORK = 2;
    case EN_POSTE = 3;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::DISPONIBLE_IMMEDIATEMENT => 'Disponible immédiatement',
            self::OPEN_TO_WORK => 'Open to work',
            self::EN_POSTE => 'En poste',
        };
    }
}
