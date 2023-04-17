<?php

namespace App\Service;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;

class Fonts
{
    public function __construct(
        private ParameterRepository $parameterRepository,
    ) {
    }

    public function getSelectedFonts(): array
    {
        $fontFirstTitle = $this->parameterRepository->findOneBy(['code' => Parameter::FONT_H1])->getValue();
        $fontSecondTitle = $this->parameterRepository->findOneBy(['code' => Parameter::FONT_H2])->getValue();
        $fontContent = $this->parameterRepository->findOneBy(['code' => Parameter::FONT_PARAGRAPH])->getValue();

        return [$fontFirstTitle, $fontSecondTitle, $fontContent];
    }
}
