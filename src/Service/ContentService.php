<?php

namespace App\Service;

use App\Repository\ContentRepository;

class ContentService
{
    public function __construct(
        private ContentRepository $contentRepository,
    ) {
    }

    public function getContent()
    {
        $content = $this->contentRepository->findOneBy([], ['id' => 'ASC']);

        return $content;
    }
}
