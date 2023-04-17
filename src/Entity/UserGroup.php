<?php

namespace App\Entity;

use App\Enum\RoleGroup;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
class UserGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $userGroup = null;

    #[ORM\Column]
    #[Assert\Positive(message: 'Ce statut n\'est pas valide.')]
    private ?int $roleInGroup = null;

    #[ORM\Column]
    private ?bool $acceptedTheInvitation = null;

    #[ORM\ManyToOne(inversedBy: 'userGroups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserGroup(): ?Group
    {
        return $this->userGroup;
    }

    public function setUserGroup(?Group $userGroup): self
    {
        $this->userGroup = $userGroup;

        return $this;
    }

    public function getRoleInGroup(): ?RoleGroup
    {
        return RoleGroup::tryFrom($this->roleInGroup);
    }

    public function setRoleInGroup(?RoleGroup $roleGroup): self
    {
        $this->roleInGroup = $roleGroup->value;

        return $this;
    }

    public function isAcceptedTheInvitation(): ?bool
    {
        return $this->acceptedTheInvitation;
    }

    public function setAcceptedTheInvitation(bool $acceptedTheInvitation): self
    {
        $this->acceptedTheInvitation = $acceptedTheInvitation;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
