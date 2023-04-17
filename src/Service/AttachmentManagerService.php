<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AttachmentManagerService
{
    public function __construct(
        private ParameterBagInterface $parameterBag,
    ) {
    }

    public function uploadAttachment(UploadedFile $file)
    {
        $filename = md5(uniqid()).'.'.$file->guessExtension();

        $file->move(
            $this->getUploadsDir(),
            $filename
        );

        return [
            'filename' => $filename,
            'path' => '/images/contenus/'.$filename,
        ];
    }

    public function getUploadsDir()
    {
        return $this->parameterBag->get('uploads');
    }
}
