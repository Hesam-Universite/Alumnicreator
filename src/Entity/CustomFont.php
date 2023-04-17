<?php

namespace App\Entity;

use App\Repository\CustomFontRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: CustomFontRepository::class)]
#[ORM\Table(name: 'custom_fonts')]
#[Vich\Uploadable]
class CustomFont
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\File(maxSize: '2048k')]
    #[Vich\UploadableField(mapping: 'custom_font', fileNameProperty: 'fontFileName')]
    #[Ignore]
    private ?File $fontFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $fontFileName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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

    public function setFontFile(?File $fontFile = null): void
    {
        $this->fontFile = $fontFile;

        if (null !== $fontFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getFontFile(): ?File
    {
        return $this->fontFile;
    }

    public function setFontFileName(?string $fontFileName): void
    {
        $this->fontFileName = $fontFileName;
    }

    public function getFontFileName(): ?string
    {
        return $this->fontFileName;
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
}
