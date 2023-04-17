<?php

namespace App\Enum;

enum TypeOfContract: int
{
    case STAGE = 1;
    case APPRENTISSAGE = 2;
    case ALTERNANCE = 3;
    case PROFESSIONNALISATION = 4;
    case CDD = 5;
    case CDI = 6;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::STAGE => 'Stage',
            self::APPRENTISSAGE => 'Apprentissage',
            self::ALTERNANCE => 'Alternance',
            self::PROFESSIONNALISATION => 'Professionnalisation',
            self::CDD => 'CDD',
            self::CDI => 'CDI',
        };
    }
}
