<?php

namespace App\Entity;

use App\Enum\TypeOfContract;
use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'jobs')]
#[Vich\Uploadable]
class Job
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyPresentation = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $description = null;

    #[ORM\Column]
    private ?array $desiredLevel = [];

    #[ORM\Column]
    #[Assert\Positive(message: 'Ce type de contrat n\'est pas valide.')]
    private ?int $typeOfContract = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $city = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Assert\Positive(message: 'Cette rémunération n\'est pas valide.')]
    private ?string $remuneration = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Email(message: 'Cette adresse mail n\'est pas valide')]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Url(message: 'Cette URL n\'est pas valide')]
    private ?string $linkToTheJobOffer = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadlineJobOffer = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['image/png', 'image/jpeg'], mimeTypesMessage: 'Seuls les png et jpg sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'job_company_logo', fileNameProperty: 'companyLogoName')]
    #[Ignore]
    private ?File $companyLogoFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $companyLogoName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $companyLogoUpdatedAt = null;

    #[Assert\File(maxSize: '2048k', mimeTypes: ['application/pdf'], mimeTypesMessage: 'Seuls les PDF sont autorisés pour ce champ.')]
    #[Vich\UploadableField(mapping: 'job_attachment', fileNameProperty: 'attachmentName')]
    #[Ignore]
    private ?File $attachmentFile = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $attachmentName = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $attachmentUpdatedAt = null;

    #[ORM\Column]
    private ?bool $isApproved = null;

    #[ORM\ManyToOne(inversedBy: 'jobs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    #[ORM\Column(length: 255)]
    #[Assert\Positive(message: 'Ce code postal n\'est pas valide.')]
    #[Assert\Length(min: 5, max: 5, exactMessage: 'Votre code postal n\'est pas valide.')]
    private ?string $postalCode = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActivityArea $activityArea = null;

    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

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

    public function getCompanyPresentation(): ?string
    {
        return $this->companyPresentation;
    }

    public function setCompanyPresentation(?string $companyPresentation): self
    {
        $this->companyPresentation = $companyPresentation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDesiredLevel(): ?array
    {
        return $this->desiredLevel;
    }

    public function setDesiredLevel(array $desiredLevel): self
    {
        $this->desiredLevel = $desiredLevel;

        return $this;
    }

    public function getTypeOfContract(): ?TypeOfContract
    {
        return TypeOfContract::tryFrom($this->typeOfContract);
    }

    public function setTypeOfContract(?TypeOfContract $typeOfContract): self
    {
        $this->typeOfContract = $typeOfContract->value;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getRemuneration(): ?string
    {
        return $this->remuneration;
    }

    public function setRemuneration(?string $remuneration): self
    {
        $this->remuneration = $remuneration;

        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(string $contactEmail): self
    {
        $this->contactEmail = $contactEmail;

        return $this;
    }

    public function getLinkToTheJobOffer(): ?string
    {
        return $this->linkToTheJobOffer;
    }

    public function setLinkToTheJobOffer(?string $linkToTheJobOffer): self
    {
        $this->linkToTheJobOffer = $linkToTheJobOffer;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDeadlineJobOffer(): ?\DateTimeInterface
    {
        return $this->deadlineJobOffer;
    }

    public function setDeadlineJobOffer(?\DateTimeInterface $deadlineJobOffer): self
    {
        $this->deadlineJobOffer = $deadlineJobOffer;

        return $this;
    }

    public function setCompanyLogoFile(?File $companyLogoFile = null): void
    {
        $this->companyLogoFile = $companyLogoFile;

        if (null !== $companyLogoFile) {
            $this->companyLogoUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getCompanyLogoFile(): ?File
    {
        return $this->companyLogoFile;
    }

    public function setCompanyLogoName(?string $companyLogoName): void
    {
        $this->companyLogoName = $companyLogoName;
    }

    public function getCompanyLogoName(): ?string
    {
        return $this->companyLogoName;
    }

    public function getCompanyLogoUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->companyLogoUpdatedAt;
    }

    public function setCompanyLogoUpdatedAt(?\DateTimeImmutable $companyLogoUpdatedAt): self
    {
        $this->companyLogoUpdatedAt = $companyLogoUpdatedAt;

        return $this;
    }

    public function setAttachmentFile(?File $attachmentFile = null): void
    {
        $this->attachmentFile = $attachmentFile;

        if (null !== $attachmentFile) {
            $this->attachmentUpdatedAt = new \DateTimeImmutable();
        }
    }

    public function getAttachmentFile(): ?File
    {
        return $this->attachmentFile;
    }

    public function setAttachmentName(?string $attachmentName): void
    {
        $this->attachmentName = $attachmentName;
    }

    public function getAttachmentName(): ?string
    {
        return $this->attachmentName;
    }

    public function getAttachmentUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->attachmentUpdatedAt;
    }

    public function setAttachmentUpdatedAt(?\DateTimeImmutable $attachmentUpdatedAt): self
    {
        $this->attachmentUpdatedAt = $attachmentUpdatedAt;

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

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
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

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setJob($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            // set the owning side to null (unless already changed)
            if ($application->getJob() === $this) {
                $application->setJob(null);
            }
        }

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

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
