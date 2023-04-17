<?php

namespace App\Service;

use App\Entity\Group;
use App\Entity\User;
use App\Repository\UserGroupRepository;

class CheckAuthorisationGroupService
{
    public function __construct(
        private UserGroupRepository $userGroupRepository,
    ) {
    }

    public function isInStaffAndGroupIsApproved(User $user, Group $group): bool
    {
        $connectedUserInGroup = $this->userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $user, 'acceptedTheInvitation' => true]);

        if (($connectedUserInGroup->getRoleInGroup()->value !== 1 && $connectedUserInGroup->getRoleInGroup()->value !== 2) || !$group->isApproved()) {
            return false;
        }

        return true;
    }

    public function isInStaffAndGroupIsApprovedOrIsAdmin(User $user, Group $group): bool
    {
        $connectedUserInGroup = $this->userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $user, 'acceptedTheInvitation' => true]);

        if (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return true;
        }

        if (($connectedUserInGroup->getRoleInGroup()->value !== 1 && $connectedUserInGroup->getRoleInGroup()->value !== 2) || !$group->isApproved()) {
            return false;
        }

        return true;
    }

    public function isInStaffAndGroupIsApprovedAndActive(User $user, Group $group): bool
    {
        $connectedUserInGroup = $this->userGroupRepository->findOneBy(['userGroup' => $group, 'user' => $user, 'acceptedTheInvitation' => true]);

        if (($connectedUserInGroup->getRoleInGroup()->value !== 1 && $connectedUserInGroup->getRoleInGroup()->value !== 2) || !$group->isApproved() || !$group->isActive()) {
            return false;
        }

        return true;
    }

    public function isUserInTheGroup(User $user, Group $group): bool
    {
        $connectedUserInGroup = $this->userGroupRepository->findOneBy(['user' => $user, 'userGroup' => $group, 'acceptedTheInvitation' => true]);

        if (null === $connectedUserInGroup) {
            return false;
        }

        return true;
    }
}
