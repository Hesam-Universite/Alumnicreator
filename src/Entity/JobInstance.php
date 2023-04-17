<?php

namespace App\Entity;

use App\Enum\TypeOfContract;
use App\Repository\JobInstanceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: JobInstanceRepository::class)]
#[ORM\Table(name: 'jobs_instance')]
class JobInstance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $instanceId = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyPresentation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column]
    private ?array $desiredLevel = [];

    #[ORM\Column]
    private ?int $typeOfContract = null;

    #[ORM\Column(length: 255)]
    private ?string $city = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $remuneration = null;

    #[ORM\Column(length: 255)]
    private ?string $contactEmail = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkToTheJobOffer = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $deadlineJobOffer = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creationDate = null;

    #[ORM\Column(length: 255)]
    private ?string $postalCode = null;

    #[ORM\Column(nullable: true)]
    private ?int $otherInstanceId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $pictureName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\Column]
    private ?int $authorId = null;

    #[ORM\Column(length: 255)]
    private ?string $activityArea = null;

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

    public function setTypeOfContract(int $typeOfContract): self
    {
        $this->typeOfContract = $typeOfContract;

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

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

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

    public function getOtherInstanceId(): ?int
    {
        return $this->otherInstanceId;
    }

    public function setOtherInstanceId(?int $otherInstanceId): self
    {
        $this->otherInstanceId = $otherInstanceId;

        return $this;
    }

    public function getPictureName(): ?string
    {
        return $this->pictureName;
    }

    public function setPictureName(?string $pictureName): self
    {
        $this->pictureName = $pictureName;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(?string $companyName): self
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAuthorId(): ?int
    {
        return $this->authorId;
    }

    public function setAuthorId(int $authorId): self
    {
        $this->authorId = $authorId;

        return $this;
    }

    public function getActivityArea(): ?string
    {
        return $this->activityArea;
    }

    public function setActivityArea(string $activityArea): self
    {
        $this->activityArea = $activityArea;

        return $this;
    }
}
