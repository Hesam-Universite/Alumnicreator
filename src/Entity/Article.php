<?php

namespace App\Entity;

use App\Enum\StatusArticle;
use App\Repository\ArticleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: 'articles')]
#[Vich\Uploadable]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true, nullable: true)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $author = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $metaDescription = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'featured_image_article', fileNameProperty: 'featuredImageName')]
    #[Ignore]
    private ?File $featuredImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $featuredImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $featuredImageUpdatedAt = null;

    private ?bool $isFromExternalFeed = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tag = null;

    #[ORM\ManyToOne]
    private ?Group $groupArticle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getStatus(): ?StatusArticle
    {
        return StatusArticle::tryFrom($this->status);
    }

    public function setStatus(?StatusArticle $statusArticle): self
    {
        $this->status = $statusArticle->value;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(?string $metaDescription): self
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function setFeaturedImageFile(?File $featuredImageFile = null): void
    {
        $this->featuredImageFile = $featuredImageFile;

        if (null !== $featuredImageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->featuredImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getFeaturedImageFile(): ?File
    {
        return $this->featuredImageFile;
    }

    public function setFeaturedImageName(?string $featuredImageName): void
    {
        $this->featuredImageName = $featuredImageName;
    }

    public function getFeaturedImageName(): ?string
    {
        return $this->featuredImageName;
    }

    public function getFeaturedImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->featuredImageUpdatedAt;
    }

    public function setFeaturedImageUpdatedAt(?\DateTimeImmutable $featuredImageUpdatedAt): self
    {
        $this->featuredImageUpdatedAt = $featuredImageUpdatedAt;

        return $this;
    }

    public function isFromExternalFeed(): ?bool
    {
        return $this->isFromExternalFeed;
    }

    public function setIsFromExternalFeed(bool $isFromExternalFeed): self
    {
        $this->isFromExternalFeed = $isFromExternalFeed;

        return $this;
    }

    public function getTag(): ?string
    {
        return $this->tag;
    }

    public function setTag(?string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getGroupArticle(): ?Group
    {
        return $this->groupArticle;
    }

    public function setGroupArticle(?Group $groupArticle): self
    {
        $this->groupArticle = $groupArticle;

        return $this;
    }
}
