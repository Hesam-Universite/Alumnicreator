<?php

namespace App\Service;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;

class FooterInformationsService
{
    public function __construct(
        private ParameterRepository $parameterRepository,
    ) {
    }

    public function getFooterInformations(): array
    {
        $phone = $this->parameterRepository->findOneBy(['code' => Parameter::PHONE])->getValue();
        $address = $this->parameterRepository->findOneBy(['code' => Parameter::ADDRESS])->getValue();
        $mail = $this->parameterRepository->findOneBy(['code' => Parameter::EMAIL])->getValue();
        $facebook = $this->parameterRepository->findOneBy(['code' => Parameter::FACEBOOK])->getValue();
        $instagram = $this->parameterRepository->findOneBy(['code' => Parameter::INSTAGRAM])->getValue();
        $twitter = $this->parameterRepository->findOneBy(['code' => Parameter::TWITTER])->getValue();

        return [$phone, $address, $mail, $facebook, $instagram, $twitter];
    }
}
