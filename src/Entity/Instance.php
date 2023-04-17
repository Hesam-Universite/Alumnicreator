<?php

namespace App\Entity;

use App\Repository\InstanceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InstanceRepository::class)]
#[ORM\Table(name: 'instances')]
class Instance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $name = null;

    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $localId;

    #[ORM\Column(type: 'uuid', unique: true, nullable: true)]
    #[Assert\Uuid(message: 'Ce champ doit être de type Uuid')]
    private ?Uuid $externalId = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: 'Cette URL n\'est pas valide')]
    private ?string $instanceUrl = null;

    #[ORM\Column]
    private ?bool $allowOtherInstance = null;

    #[ORM\Column]
    private ?bool $allowShareJobs = null;

    #[ORM\Column]
    private ?bool $allowShareResumes = null;

    #[ORM\Column]
    private ?bool $allowShareCompanies = null;

    #[ORM\Column]
    private ?bool $allowShareStudents = null;

    public function __construct()
    {
        $this->localId = Uuid::v4();
    }

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

    public function getLocalId(): ?Uuid
    {
        return $this->localId;
    }

    public function setLocalId(Uuid $localId): self
    {
        $this->localId = $localId;

        return $this;
    }

    public function getExternalId(): ?Uuid
    {
        return $this->externalId;
    }

    public function setExternalId(?Uuid $externalId): self
    {
        if ($this->isAllowOtherInstance() === false) {
            $this->externalId = $externalId;
        }

        return $this;
    }

    public function getInstanceUrl(): ?string
    {
        return $this->instanceUrl;
    }

    public function setInstanceUrl(string $instanceUrl): self
    {
        $this->instanceUrl = $instanceUrl;

        return $this;
    }

    public function isAllowOtherInstance(): ?bool
    {
        return $this->allowOtherInstance;
    }

    public function setAllowOtherInstance(bool $allowOtherInstance): self
    {
        $this->allowOtherInstance = $allowOtherInstance;

        return $this;
    }

    public function isAllowShareJobs(): ?bool
    {
        return $this->allowShareJobs;
    }

    public function setAllowShareJobs(bool $allowShareJobs): self
    {
        $this->allowShareJobs = $allowShareJobs;

        return $this;
    }

    public function isAllowShareResumes(): ?bool
    {
        return $this->allowShareResumes;
    }

    public function setAllowShareResumes(bool $allowShareResumes): self
    {
        $this->allowShareResumes = $allowShareResumes;

        return $this;
    }

    public function isAllowShareCompanies(): ?bool
    {
        return $this->allowShareCompanies;
    }

    public function setAllowShareCompanies(bool $allowShareCompanies): self
    {
        $this->allowShareCompanies = $allowShareCompanies;

        return $this;
    }

    public function isAllowShareStudents(): ?bool
    {
        return $this->allowShareStudents;
    }

    public function setAllowShareStudents(bool $allowShareStudents): self
    {
        $this->allowShareStudents = $allowShareStudents;

        return $this;
    }
}
