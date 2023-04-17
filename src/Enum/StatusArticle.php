<?php

namespace App\Enum;

enum StatusArticle: int
{
    case BROUILLON = 1;
    case PUBLIE = 2;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::BROUILLON => 'Brouillon',
            self::PUBLIE => 'Publié',
        };
    }
}
