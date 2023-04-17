<?php

namespace App\Twig;

use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UserGroupInvitations extends AbstractExtension
{
    public function __construct(
        private UserRepository $userRepository,
        private UserGroupRepository $userGroupRepository,
    ) {
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getGroupInvitationsOfConnectedUser', [$this, 'getGroupInvitationsOfConnectedUser']),
        ];
    }

    public function getGroupInvitationsOfConnectedUser(int $userId): int
    {
        $user = $this->userRepository->findOneBy(['id' => $userId]);

        $invitationsPending = $this->userGroupRepository->findBy(['user' => $user, 'acceptedTheInvitation' => false]);

        return count($invitationsPending);
    }
}
