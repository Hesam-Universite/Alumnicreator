<?php

namespace App\Entity;

use App\Enum\StatusResume;
use App\Repository\ResumeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ResumeRepository::class)]
#[ORM\Table(name: 'resumes')]
#[Vich\Uploadable]
class Resume
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: false)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire.')]
    private ?string $presentation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['application/pdf'], mimeTypesMessage: 'Seuls les PDF sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'additional_file', fileNameProperty: 'additionalFileName')]
    #[Ignore]
    private ?File $additionalFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $additionalFileName = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['application/pdf'], mimeTypesMessage: 'Seuls les PDF sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'resume', fileNameProperty: 'resumeName')]
    #[Ignore]
    private ?File $resume = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $resumeName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $resumeUpdatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $activityAreaOther = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Vous devez obligatoirement sélectionner un statut.')]
    #[Assert\Positive(message: 'Ce statut n\'est pas valide.')]
    private ?int $status = null;

    #[ORM\OneToOne(inversedBy: 'resume', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityArea $activityArea = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Skill $skill = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setAdditionalFile(?File $additionalFile = null): void
    {
        $this->additionalFile = $additionalFile;

        if (null !== $additionalFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getAdditionalFile(): ?File
    {
        return $this->additionalFile;
    }

    public function setAdditionalFileName(?string $additionalFileName): void
    {
        $this->additionalFileName = $additionalFileName;
    }

    public function getAdditionalFileName(): ?string
    {
        return $this->additionalFileName;
    }

    public function setResume(?File $resume = null): void
    {
        $this->resume = $resume;

        if (null !== $resume) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getResume(): ?File
    {
        return $this->resume;
    }

    public function setResumeName(?string $resumeName): void
    {
        $this->resumeName = $resumeName;
    }

    public function getResumeName(): ?string
    {
        return $this->resumeName;
    }

    public function getResumeUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->resumeUpdatedAt;
    }

    public function setResumeUpdatedAt(?\DateTimeImmutable $resumeUpdatedAt): self
    {
        $this->resumeUpdatedAt = $resumeUpdatedAt;

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

    public function getStatus(): ?StatusResume
    {
        return StatusResume::tryFrom($this->status);
    }

    public function setStatus(?StatusResume $status): self
    {
        $this->status = $status->value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function getSkill(): ?Skill
    {
        return $this->skill;
    }

    public function setSkill(?Skill $skill): self
    {
        $this->skill = $skill;

        return $this;
    }
}
