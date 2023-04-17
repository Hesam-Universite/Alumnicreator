<?php

namespace App\Entity;

use App\Repository\MediaGroupRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: MediaGroupRepository::class)]
#[Vich\Uploadable]
class MediaGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'media_group', fileNameProperty: 'mediaName')]
    #[Ignore]
    private ?File $mediaFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $mediaName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $mediaGroup = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setMediaFile(?File $mediaFile = null): void
    {
        $this->mediaFile = $mediaFile;

        if (null !== $mediaFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getMediaFile(): ?File
    {
        return $this->mediaFile;
    }

    public function setMediaName(?string $mediaName): void
    {
        $this->mediaName = $mediaName;
    }

    public function getMediaName(): ?string
    {
        return $this->mediaName;
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

    public function getMediaGroup(): ?Group
    {
        return $this->mediaGroup;
    }

    public function setMediaGroup(?Group $mediaGroup): self
    {
        $this->mediaGroup = $mediaGroup;

        return $this;
    }
}
