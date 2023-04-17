<?php

namespace App\Entity;

use App\Repository\NewsletterCampaignRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: NewsletterCampaignRepository::class)]
#[ORM\Table(name: 'newsletter_campaigns')]
class NewsletterCampaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    private ?string $subject = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $sendingTime = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'Cette adresse n\'est pas valide')]
    private ?string $sendingEmail = null;

    #[ORM\Column(length: 255)]
    #[Assert\Email(message: 'Cette adresse n\'est pas valide')]
    private ?string $recipientEmail = null;

    #[ORM\Column]
    private ?bool $isSent = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSendingTime(): ?\DateTimeInterface
    {
        return $this->sendingTime;
    }

    public function setSendingTime(?\DateTimeInterface $sendingTime): self
    {
        $this->sendingTime = $sendingTime;

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

    public function getSendingEmail(): ?string
    {
        return $this->sendingEmail;
    }

    public function setSendingEmail(string $sendingEmail): self
    {
        $this->sendingEmail = $sendingEmail;

        return $this;
    }

    public function getRecipientEmail(): ?string
    {
        return $this->recipientEmail;
    }

    public function setRecipientEmail(string $recipientEmail): self
    {
        $this->recipientEmail = $recipientEmail;

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
}
