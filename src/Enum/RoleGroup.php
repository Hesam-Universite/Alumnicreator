<?php

namespace App\Enum;

enum RoleGroup: int
{
    case ADMIN = 1;
    case MODERATOR = 2;
    case MEMBER = 3;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public function getLabel(self $value): string
    {
        return match ($value) {
            self::ADMIN => 'Administrateur',
            self::MODERATOR => 'Modérateur',
            self::MEMBER => 'Membre',
        };
    }
}
