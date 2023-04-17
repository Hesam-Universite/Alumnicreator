<?php

namespace App\Entity;

use App\Repository\SocialPostRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: SocialPostRepository::class)]
#[ORM\Table(name: 'social_posts')]
#[Vich\Uploadable]
class SocialPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?\DateTimeInterface $timeToPublished = null;

    #[ORM\Column]
    private ?bool $isSent = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'post_image', fileNameProperty: 'postImageName')]
    #[Ignore]
    private ?File $postImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $postImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $postImageUpdatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getTimeToPublished(): ?\DateTimeInterface
    {
        return $this->timeToPublished;
    }

    public function setTimeToPublished(\DateTimeInterface $timeToPublished): self
    {
        $this->timeToPublished = $timeToPublished;

        return $this;
    }

    public function isSent(): ?bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function setPostImageFile(?File $postImageFile = null): void
    {
        $this->postImageFile = $postImageFile;

        if (null !== $postImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->postImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getPostImageFile(): ?File
    {
        return $this->postImageFile;
    }

    public function setPostImageName(?string $postImageName): void
    {
        $this->postImageName = $postImageName;
    }

    public function getPostImageName(): ?string
    {
        return $this->postImageName;
    }

    public function getPostImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->postImageUpdatedAt;
    }

    public function setPostImageUpdatedAt(?\DateTimeImmutable $postImageUpdatedAt): self
    {
        $this->postImageUpdatedAt = $postImageUpdatedAt;

        return $this;
    }
}
