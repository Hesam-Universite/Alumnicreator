<?php

namespace App\Twig;

use App\Entity\Parameter;
use App\Repository\ParameterRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ParametersFunction extends AbstractExtension
{
    public function __construct(
        private ParameterRepository $parameterRepository,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('parameterNewsletter', [$this, 'getNewsletterParameter']),
            new TwigFunction('parameterPageMenu', [$this, 'getPageMenuParameter']),
            new TwigFunction('parameterMenuIcons', [$this, 'getMenuIconsParameter']),
        ];
    }

    public function getNewsletterParameter(): Parameter
    {
        return $this->parameterRepository->findOneBy(['code' => Parameter::NEWSLETTER_STATUS]);
    }

    public function getPageMenuParameter(): string
    {
        return $this->parameterRepository->findOneBy(['code' => Parameter::NAME_PAGE_MENU])->getValue();
    }

    public function getMenuIconsParameter(): int
    {
        return (int) $this->parameterRepository->findOneBy(['code' => Parameter::MENU_ICONS])->getValue();
    }
}
