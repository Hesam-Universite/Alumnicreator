<?php

namespace App\Twig;

use App\Repository\UserInstanceRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserInstanceIdByUuidAndUserId extends AbstractExtension
{
    public function __construct(
        private UserInstanceRepository $userInstanceRepository,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getUserInstanceId', [$this, 'getUserInstanceId']),
        ];
    }

    public function getUserInstanceId($userId, $uuid): ?int
    {
        $userInstanceId = $this->userInstanceRepository->findOneBy(['otherInstanceId' => $userId, 'instanceId' => $uuid]);

        return $userInstanceId?->getId();
    }
}
