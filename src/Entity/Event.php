<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'events')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $end = null;

    #[ORM\Column]
    private ?bool $allDay = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startFullday = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endFullday = null;

    #[ORM\ManyToOne]
    private ?Group $groupEvent = null;

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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function isAllDay(): ?bool
    {
        return $this->allDay;
    }

    public function setAllDay(bool $allDay): self
    {
        $this->allDay = $allDay;

        return $this;
    }

    public function getStartFullday(): ?\DateTimeInterface
    {
        return $this->startFullday;
    }

    public function setStartFullday(?\DateTimeInterface $startFullday): self
    {
        $this->startFullday = $startFullday;

        return $this;
    }

    public function getEndFullday(): ?\DateTimeInterface
    {
        return $this->endFullday;
    }

    public function setEndFullday(?\DateTimeInterface $endFullday): self
    {
        $this->endFullday = $endFullday;

        return $this;
    }

    public function getGroupEvent(): ?Group
    {
        return $this->groupEvent;
    }

    public function setGroupEvent(?Group $groupEvent): self
    {
        $this->groupEvent = $groupEvent;

        return $this;
    }
}
