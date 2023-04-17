<?php

namespace App\Form\DataTransformer;

use App\Enum\StudyLevel;
use Symfony\Component\Form\DataTransformerInterface;

class DesiredLevelToEnumTransformer implements DataTransformerInterface
{
    public function transform($desiredLevelAsArray): array
    {
        $enums = [];
        foreach ($desiredLevelAsArray as $level) {
            $enums[] = StudyLevel::tryFrom($level);
        }

        return $enums;
    }

    public function reverseTransform($desiredLevelAsInt)
    {
        return $desiredLevelAsInt;
    }
}
