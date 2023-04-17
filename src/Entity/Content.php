<?php

namespace App\Entity;

use App\Repository\ContentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[ORM\Table(name: 'contents')]
#[Vich\Uploadable]
class Content
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'front_logo', fileNameProperty: 'logoName')]
    #[Ignore]
    private ?File $logoFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $logoName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $logoUpdatedAt = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seul le format png est autorisé pour ce champ.')]
    #[Vich\UploadableField(mapping: 'favicon', fileNameProperty: 'faviconName')]
    #[Ignore]
    private ?File $faviconFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $faviconName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $faviconUpdatedAt = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'front_hero', fileNameProperty: 'heroImageName')]
    #[Ignore]
    private ?File $heroImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $heroImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $heroImageUpdatedAt = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'front_students', fileNameProperty: 'studentsImageName')]
    #[Ignore]
    private ?File $studentsImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $studentsImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $studentsImageUpdatedAt = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'front_companies', fileNameProperty: 'companiesImageName')]
    #[Ignore]
    private ?File $companiesImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $companiesImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $companiesImageUpdatedAt = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'front_directory', fileNameProperty: 'directoryImageName')]
    #[Ignore]
    private ?File $directoryImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $directoryImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $directoryImageUpdatedAt = null;

    #[Assert\File(maxSize: '5000k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'front_career', fileNameProperty: 'careerImageName')]
    #[Ignore]
    private ?File $careerImageFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $careerImageName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $careerImageUpdatedAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mainTitle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $youtubeVideoLink = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphOne = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphTwo = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphThree = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphFour = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphFive = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphSix = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphSeven = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $paragraphEight = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLogoFile(?File $logoFile = null): void
    {
        $this->logoFile = $logoFile;

        if (null !== $logoFile) {
            $this->logoUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getLogoFile(): ?File
    {
        return $this->logoFile;
    }

    public function getLogoName(): ?string
    {
        return $this->logoName;
    }

    public function setLogoName(?string $logoName): void
    {
        $this->logoName = $logoName;
    }

    public function getLogoUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->logoUpdatedAt;
    }

    public function setLogoUpdatedAt(\DateTimeImmutable $logoUpdatedAt): self
    {
        $this->logoUpdatedAt = $logoUpdatedAt;

        return $this;
    }

    public function setFaviconFile(?File $faviconFile = null): void
    {
        $this->faviconFile = $faviconFile;

        if (null !== $faviconFile) {
            $this->faviconUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getFaviconFile(): ?File
    {
        return $this->faviconFile;
    }

    public function getFaviconName(): ?string
    {
        return $this->faviconName;
    }

    public function setFaviconName(?string $faviconName): void
    {
        $this->faviconName = $faviconName;
    }

    public function getFaviconUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->faviconUpdatedAt;
    }

    public function setFaviconUpdatedAt(\DateTimeImmutable $faviconUpdatedAt): self
    {
        $this->faviconUpdatedAt = $faviconUpdatedAt;

        return $this;
    }

    public function setHeroImageFile(?File $heroImageFile = null): void
    {
        $this->heroImageFile = $heroImageFile;

        if (null !== $heroImageFile) {
            $this->heroImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getHeroImageFile(): ?File
    {
        return $this->heroImageFile;
    }

    public function getHeroImageName(): ?string
    {
        return $this->heroImageName;
    }

    public function setHeroImageName(?string $heroImageName): void
    {
        $this->heroImageName = $heroImageName;
    }

    public function getHeroImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->heroImageUpdatedAt;
    }

    public function setHeroImageUpdatedAt(\DateTimeImmutable $heroImageUpdatedAt): self
    {
        $this->heroImageUpdatedAt = $heroImageUpdatedAt;

        return $this;
    }

    public function setStudentsImageFile(?File $studentsImageFile = null): void
    {
        $this->studentsImageFile = $studentsImageFile;

        if (null !== $studentsImageFile) {
            $this->studentsImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getStudentsImageFile(): ?File
    {
        return $this->studentsImageFile;
    }

    public function getStudentsImageName(): ?string
    {
        return $this->studentsImageName;
    }

    public function setStudentsImageName(?string $studentsImageName): void
    {
        $this->studentsImageName = $studentsImageName;
    }

    public function getStudentsImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->studentsImageUpdatedAt;
    }

    public function setStudentsImageUpdatedAt(\DateTimeImmutable $studentsImageUpdatedAt): self
    {
        $this->studentsImageUpdatedAt = $studentsImageUpdatedAt;

        return $this;
    }

    public function setCompaniesImageFile(?File $companiesImageFile = null): void
    {
        $this->companiesImageFile = $companiesImageFile;

        if (null !== $companiesImageFile) {
            $this->companiesImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getCompaniesImageFile(): ?File
    {
        return $this->companiesImageFile;
    }

    public function getCompaniesImageName(): ?string
    {
        return $this->companiesImageName;
    }

    public function setCompaniesImageName(?string $companiesImageName): void
    {
        $this->companiesImageName = $companiesImageName;
    }

    public function getCompaniesImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->companiesImageUpdatedAt;
    }

    public function setCompaniesImageUpdatedAt(\DateTimeImmutable $companiesImageUpdatedAt): self
    {
        $this->companiesImageUpdatedAt = $companiesImageUpdatedAt;

        return $this;
    }

    public function setDirectoryImageFile(?File $directoryImageFile = null): void
    {
        $this->directoryImageFile = $directoryImageFile;

        if (null !== $directoryImageFile) {
            $this->directoryImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getDirectoryImageFile(): ?File
    {
        return $this->directoryImageFile;
    }

    public function getDirectoryImageName(): ?string
    {
        return $this->directoryImageName;
    }

    public function setDirectoryImageName(?string $directoryImageName): void
    {
        $this->directoryImageName = $directoryImageName;
    }

    public function getDirectoryImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->directoryImageUpdatedAt;
    }

    public function setDirectoryImageUpdatedAt(\DateTimeImmutable $directoryImageUpdatedAt): self
    {
        $this->directoryImageUpdatedAt = $directoryImageUpdatedAt;

        return $this;
    }

    public function setCareerImageFile(?File $careerImageFile = null): void
    {
        $this->careerImageFile = $careerImageFile;

        if (null !== $careerImageFile) {
            $this->careerImageUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getCareerImageFile(): ?File
    {
        return $this->careerImageFile;
    }

    public function getCareerImageName(): ?string
    {
        return $this->careerImageName;
    }

    public function setCareerImageName(?string $careerImageName): void
    {
        $this->careerImageName = $careerImageName;
    }

    public function getCareerImageUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->careerImageUpdatedAt;
    }

    public function setCareerImageUpdatedAt(\DateTimeImmutable $careerImageUpdatedAt): self
    {
        $this->careerImageUpdatedAt = $careerImageUpdatedAt;

        return $this;
    }

    public function getMainTitle(): ?string
    {
        return $this->mainTitle;
    }

    public function setMainTitle(string $mainTitle): self
    {
        $this->mainTitle = $mainTitle;

        return $this;
    }

    public function getYoutubeVideoLink(): ?string
    {
        return $this->youtubeVideoLink;
    }

    public function setYoutubeVideoLink(string $youtubeVideoLink): self
    {
        $this->youtubeVideoLink = $youtubeVideoLink;

        return $this;
    }

    public function getParagraphOne(): ?string
    {
        return $this->paragraphOne;
    }

    public function setParagraphOne(?string $paragraphOne): self
    {
        $this->paragraphOne = $paragraphOne;

        return $this;
    }

    public function getParagraphTwo(): ?string
    {
        return $this->paragraphTwo;
    }

    public function setParagraphTwo(?string $paragraphTwo): self
    {
        $this->paragraphTwo = $paragraphTwo;

        return $this;
    }

    public function getParagraphThree(): ?string
    {
        return $this->paragraphThree;
    }

    public function setParagraphThree(?string $paragraphThree): self
    {
        $this->paragraphThree = $paragraphThree;

        return $this;
    }

    public function getParagraphFour(): ?string
    {
        return $this->paragraphFour;
    }

    public function setParagraphFour(?string $paragraphFour): self
    {
        $this->paragraphFour = $paragraphFour;

        return $this;
    }

    public function getParagraphFive(): ?string
    {
        return $this->paragraphFive;
    }

    public function setParagraphFive(?string $paragraphFive): self
    {
        $this->paragraphFive = $paragraphFive;

        return $this;
    }

    public function getParagraphSix(): ?string
    {
        return $this->paragraphSix;
    }

    public function setParagraphSix(?string $paragraphSix): self
    {
        $this->paragraphSix = $paragraphSix;

        return $this;
    }

    public function getParagraphSeven(): ?string
    {
        return $this->paragraphSeven;
    }

    public function setParagraphSeven(?string $paragraphSeven): self
    {
        $this->paragraphSeven = $paragraphSeven;

        return $this;
    }

    public function getParagraphEight(): ?string
    {
        return $this->paragraphEight;
    }

    public function setParagraphEight(?string $paragraphEight): self
    {
        $this->paragraphEight = $paragraphEight;

        return $this;
    }
}
