<?php

namespace App\Twig;

use App\Repository\PageRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PageFunction extends AbstractExtension
{
    public function __construct(
        private PageRepository $pageRepository,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('page', [$this, 'getPages']),
        ];
    }

    public function getPages(): array
    {
        return $this->pageRepository->findBy(['status' => 2], ['id' => 'DESC']);
    }
}
