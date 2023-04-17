<?php

namespace App\Entity;

use App\Enum\VisibilityGroup;
use App\Repository\GroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: 'groups')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Positive(message: 'Cette visibilité n\'est pas valide.')]
    private ?int $visibility = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column]
    private ?bool $isApproved = null;

    #[ORM\Column]
    private ?bool $approvalNotificationSent = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityArea $activityArea = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getVisibility(): ?VisibilityGroup
    {
        return VisibilityGroup::tryFrom($this->visibility);
    }

    public function setVisibility(?VisibilityGroup $visibilityGroup): self
    {
        $this->visibility = $visibilityGroup->value;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function isApprovalNotificationSent(): ?bool
    {
        return $this->approvalNotificationSent;
    }

    public function setApprovalNotificationSent(bool $approvalNotificationSent): self
    {
        $this->approvalNotificationSent = $approvalNotificationSent;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getActivityArea(): ?ActivityArea
    {
        return $this->activityArea;
    }

    public function setActivityArea(?ActivityArea $activityArea): self
    {
        $this->activityArea = $activityArea;

        return $this;
    }
}
