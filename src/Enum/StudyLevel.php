<?php

namespace App\Enum;

enum StudyLevel: int
{
    case BAC = 1;
    case BAC_1 = 2;
    case BAC_3_LICENCE_BACHELOR = 3;
    case BAC_5_MASTER_ECOLES = 4;
    case DOCTORAT = 5;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::BAC => 'Bac',
            self::BAC_1 => 'Bac+1',
            self::BAC_3_LICENCE_BACHELOR => 'Bac+3/Licence/Bachelor',
            self::BAC_5_MASTER_ECOLES => 'Bac+5/Master/Écoles',
            self::DOCTORAT => 'Doctorat',
        };
    }
}
