<?php

namespace App\Enum;

enum ActivityArea: int
{
    case AGROALIMENTAIRE = 1;
    case BANQUE_ASSURANCE = 2;
    case BOIS_PAPIER_CARTON_IMPRIMERIE = 3;
    case BTP_MATERIAUX_DE_CONSTRUCTION = 4;
    case CHIMIE_PARACHIMIE = 5;
    case COMMERCE_NEGOCE_DISTRIBUTION = 6;
    case EDITION_COMMUNICATION_MULTIMEDIA = 7;
    case ELECTRONIQUE_ELECTRICITE = 8;
    case ETUDES_ET_CONSEILS = 9;
    case INDUSTRIE_PHARMACEUTIQUE = 10;
    case INFORMATIQUE_TELECOMS = 11;
    case MACHINES_ET_EQUIPEMENTS_AUTOMOBILE = 12;
    case METALLURGIE_TRAVAIL_DU_METAL = 13;
    case PLASTIQUE_CAOUTCHOUC = 14;
    case SERVICES_AUX_ENTREPRISES = 15;
    case TEXTILE_HABILLEMENT_CHAUSSURE = 16;
    case TRANSPORTS_LOGISTIQUE = 17;
    case AUTRE = 18;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            self::AGROALIMENTAIRE => 'Agrolimentaire',
            self::BANQUE_ASSURANCE => 'Banque / Assurance',
            self::BOIS_PAPIER_CARTON_IMPRIMERIE => 'Bois / Papier / Carton / Imprimerie',
            self::BTP_MATERIAUX_DE_CONSTRUCTION => 'BTP / Matériaux de construction',
            self::CHIMIE_PARACHIMIE => 'Chimie / Parachimie',
            self::COMMERCE_NEGOCE_DISTRIBUTION => 'Commerce / Négoce / Distribution',
            self::EDITION_COMMUNICATION_MULTIMEDIA => 'Édition / Communication / Multimédia',
            self::ELECTRONIQUE_ELECTRICITE => 'Électronique / Électricité',
            self::ETUDES_ET_CONSEILS => 'Études et conseils',
            self::INDUSTRIE_PHARMACEUTIQUE => 'Industrie pharmaceutique',
            self::INFORMATIQUE_TELECOMS => 'Informatique / Télécoms',
            self::MACHINES_ET_EQUIPEMENTS_AUTOMOBILE => 'Machines et équipements / Automobiles',
            self::METALLURGIE_TRAVAIL_DU_METAL => 'Métallurgie / Travail du métal',
            self::PLASTIQUE_CAOUTCHOUC => 'Plastique / Caoutchouc',
            self::SERVICES_AUX_ENTREPRISES => 'Services aux entreprises',
            self::TEXTILE_HABILLEMENT_CHAUSSURE => 'Textile / Habillement / Chaussure',
            self::TRANSPORTS_LOGISTIQUE => 'Transports / Logistique',
            self::AUTRE => 'Autre',
        };
    }
}
