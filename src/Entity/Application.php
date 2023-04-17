<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\Table(name: 'applications')]
#[Vich\Uploadable]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['application/pdf'], mimeTypesMessage: 'Seuls les PDF sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'application_additional_file', fileNameProperty: 'additionalFileName')]
    private ?File $additionalFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $additionalFileName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $additionalFileUpdatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Job $job = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function setAdditionalFile(?File $additionalFile = null): void
    {
        $this->additionalFile = $additionalFile;

        if (null !== $additionalFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->additionalFileUpdatedAt = new \DateTimeImmutable();
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

    public function getAdditionalFileUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->additionalFileUpdatedAt;
    }

    public function setAdditionalFileUpdatedAt(?\DateTimeImmutable $additionalFileUpdatedAt): self
    {
        $this->additionalFileUpdatedAt = $additionalFileUpdatedAt;

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): self
    {
        $this->job = $job;

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
