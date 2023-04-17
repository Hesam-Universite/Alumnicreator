<?php

namespace App\Service;

use App\Repository\FooterColumnRepository;

class FooterColumnsService
{
    public function __construct(
        private FooterColumnRepository $footerColumnRepository,
    ) {
    }

    public function getFooterColumnsName(): array
    {
        $footerColumnsName = [];
        $footerColumns = $this->footerColumnRepository->findBy([], ['position' => 'ASC']);

        foreach ($footerColumns as $column) {
            $footerColumnsName[] = $column->getName();
        }

        return $footerColumnsName;
    }

    public function getFooterColumnsContent(): array
    {
        $footerColumnsContent = [];
        $footerColumns = $this->footerColumnRepository->findBy([], ['position' => 'ASC']);

        foreach ($footerColumns as $column) {
            $footerColumnsContent[] = unserialize($column->getContent());
        }

        return $footerColumnsContent;
    }
}
