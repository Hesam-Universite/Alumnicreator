<?php

namespace App\Enum;

enum Region: int
{
    case AUVERGNE_RHONE_ALPES = 1;
    case BOURGOGNE_FRANCHE_COMTE = 2;
    case BRETAGNE = 3;
    case CENTRE_VAL_DE_LOIRE = 4;
    case CORSE = 5;
    case GRAND_EST = 6;
    case HAUTS_DE_FRANCE = 7;
    case ILE_DE_FRANCE = 8;
    case NORMANDIE = 9;
    case NOUVELLE_AQUITAINE = 10;
    case OCCITANIE = 11;
    case PAYS_DE_LA_LOIRE = 12;
    case PROVENCE_ALPES_COTE_D_AZUR = 13;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::AUVERGNE_RHONE_ALPES => 'Auvergne-Rhône-Alpes',
            self::BOURGOGNE_FRANCHE_COMTE => 'Bourgogne-Franche-Comté',
            self::BRETAGNE => 'Bretagne',
            self::CENTRE_VAL_DE_LOIRE => 'Centre-Val de Loire',
            self::CORSE => 'Corse',
            self::GRAND_EST => 'Grand Est',
            self::HAUTS_DE_FRANCE => 'Hauts-de-France',
            self::ILE_DE_FRANCE => 'Île-de-France',
            self::NORMANDIE => 'Normandie',
            self::NOUVELLE_AQUITAINE => 'Nouvelle-Aquitaine',
            self::OCCITANIE => 'Occitanie',
            self::PAYS_DE_LA_LOIRE => 'Pays de la Loire',
            self::PROVENCE_ALPES_COTE_D_AZUR => 'Provence-Alpes-Côte d\'Azur',
        };
    }
}
