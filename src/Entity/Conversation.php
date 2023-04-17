<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Table(name: 'conversations')]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, orphanRemoval: true)]
    private Collection $messages;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastMessage = null;

    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: UserConversation::class, orphanRemoval: true)]
    private Collection $userConversations;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->userConversations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?\DateTimeInterface
    {
        return $this->lastMessage;
    }

    public function setLastMessage(\DateTimeInterface $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    /**
     * @return Collection<int, UserConversation>
     */
    public function getUserConversations(): Collection
    {
        return $this->userConversations;
    }

    public function addUserConversation(UserConversation $userConversation): self
    {
        if (!$this->userConversations->contains($userConversation)) {
            $this->userConversations->add($userConversation);
            $userConversation->setConversation($this);
        }

        return $this;
    }

    public function removeUserConversation(UserConversation $userConversation): self
    {
        if ($this->userConversations->removeElement($userConversation)) {
            // set the owning side to null (unless already changed)
            if ($userConversation->getConversation() === $this) {
                $userConversation->setConversation(null);
            }
        }

        return $this;
    }
}
