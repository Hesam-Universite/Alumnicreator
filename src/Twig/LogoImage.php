<?php

namespace App\Twig;

use App\Entity\Content;
use App\Repository\ContentRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LogoImage extends AbstractExtension
{
    public function __construct(
        private ContentRepository $contentRepository,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('logoImage', [$this, 'getLogoImage']),
        ];
    }

    public function getLogoImage(): Content
    {
        return $this->contentRepository->findOneBy([], ['id' => 'ASC']);
    }
}
