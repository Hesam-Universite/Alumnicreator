<?php

namespace App\Entity;

use App\Enum\StatusResume;
use App\Repository\ResumeInstanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ResumeInstanceRepository::class)]
#[ORM\Table(name: 'resumes_instance')]
class ResumeInstance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $otherInstanceId = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $instanceId = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $presentation = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthday = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userPostalCode = null;

    #[ORM\Column]
    private ?int $userId = null;

    #[ORM\Column(length: 255)]
    private ?string $resumeName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $additionalFileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activityArea = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activityAreaOther = null;

    #[ORM\Column(length: 255)]
    private ?string $skill = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInstanceId(): ?Uuid
    {
        return $this->instanceId;
    }

    public function setInstanceId(Uuid $instanceId): self
    {
        $this->instanceId = $instanceId;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getOtherInstanceId(): ?int
    {
        return $this->otherInstanceId;
    }

    public function setOtherInstanceId(?int $otherInstanceId): self
    {
        $this->otherInstanceId = $otherInstanceId;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getStatus(): ?StatusResume
    {
        return StatusResume::tryFrom($this->status);
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUserPostalCode(): ?string
    {
        return $this->userPostalCode;
    }

    public function setUserPostalCode(?string $userPostalCode): self
    {
        $this->userPostalCode = $userPostalCode;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    public function getResumeName(): ?string
    {
        return $this->resumeName;
    }

    public function setResumeName(string $resumeName): self
    {
        $this->resumeName = $resumeName;

        return $this;
    }

    public function getAdditionalFileName(): ?string
    {
        return $this->additionalFileName;
    }

    public function setAdditionalFileName(?string $additionalFileName): self
    {
        $this->additionalFileName = $additionalFileName;

        return $this;
    }

    public function getActivityArea(): ?string
    {
        return $this->activityArea;
    }

    public function setActivityArea(?string $activityArea): self
    {
        $this->activityArea = $activityArea;

        return $this;
    }

    public function getActivityAreaOther(): ?string
    {
        return $this->activityAreaOther;
    }

    public function setActivityAreaOther(?string $activityAreaOther): self
    {
        $this->activityAreaOther = $activityAreaOther;

        return $this;
    }

    public function getSkill(): ?string
    {
        return $this->skill;
    }

    public function setSkill(string $skill): self
    {
        $this->skill = $skill;

        return $this;
    }
}
