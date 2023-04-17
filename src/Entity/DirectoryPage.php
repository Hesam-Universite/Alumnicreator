<?php

namespace App\Entity;

use App\Repository\DirectoryPageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DirectoryPageRepository::class)]
#[ORM\Table(name: 'directory_pages')]
class DirectoryPage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $firstname = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Range([
        'min' => 1900,
        'max' => 2100,
        'notInRangeMessage' => 'Cette promotion n\'est pas valide.',
    ])]
    private ?int $class = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\Email(message: 'Cette adresse n\'est pas valide')]
    private ?string $email = null;

    #[ORM\OneToOne(inversedBy: 'directoryPage', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $linkedinLink = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getClass(): ?int
    {
        return $this->class;
    }

    public function setClass(int $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getLinkedinLink(): ?string
    {
        return $this->linkedinLink;
    }

    public function setLinkedinLink(?string $linkedinLink): self
    {
        if (str_starts_with($linkedinLink, 'https://www.linkedin.com/in/')) {
            $linkedinLink = substr($linkedinLink, 28);
        }

        $this->linkedinLink = $linkedinLink;

        return $this;
    }
}
