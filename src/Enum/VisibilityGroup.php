<?php

namespace App\Enum;

enum VisibilityGroup: int
{
    case PUBLIQUE = 1;
    case PRIVE = 2;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::PUBLIQUE => 'Publique',
            self::PRIVE => 'Privé',
        };
    }
}
