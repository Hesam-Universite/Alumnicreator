<?php

namespace App\Service;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;

class VisualConfigurations
{
    public function __construct(
        private ParameterRepository $parameterRepository,
    ) {
    }

    public function getVisualConfigurations(): array
    {
        $primaryColor = $this->parameterRepository->findOneBy(['code' => Parameter::PRIMARY_COLOR])->getValue();
        $secondaryColor = $this->parameterRepository->findOneBy(['code' => Parameter::SECONDARY_COLOR])->getValue();
        $footerColor = $this->parameterRepository->findOneBy(['code' => Parameter::FOOTER_COLOR])->getValue();
        $menuPosition = $this->parameterRepository->findOneBy(['code' => Parameter::MENU_POSITION])->getValue();
        $menuColor = $this->parameterRepository->findOneBy(['code' => Parameter::MENU_COLOR])->getValue();

        return [$primaryColor, $secondaryColor, $footerColor, $menuPosition, $menuColor];
    }
}
