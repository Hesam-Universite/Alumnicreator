<?php

namespace App\Service;

class UtilService
{
    public function createZip(): array
    {
        $name = @tempnam('tmp', 'zip');
        $zip = new \ZipArchive();
        $zip->open($name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        return [$name, $zip];
    }
}
