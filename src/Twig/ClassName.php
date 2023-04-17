<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ClassName extends AbstractExtension
{
    public function __construct()
    {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('class', [$this, 'getClassNameOfTheObject']),
        ];
    }

    public function getClassNameOfTheObject($object): string
    {
        return (new \ReflectionClass($object))->getShortName();
    }
}
