<?php

namespace App\Enum;

enum StatusAlumni: int
{
    case EN_RECHERCHE_D_EMPLOI = 0;
    case RECRUTEUR = 1;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::EN_RECHERCHE_D_EMPLOI => 'En recherche d\'emploi',
            self::RECRUTEUR => 'Recruteur',
        };
    }
}
