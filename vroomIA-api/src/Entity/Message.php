<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?Role $owner = null;

    #[ORM\Column(length: 10000)]
    private ?string $content = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    private ?Conversation $conversation = null;

    public function __construct(Role $sender, string $message)
    {
        $this->owner = $sender;
        $this->content = $message;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): Role
    {
        return $this->owner;
    }

    public function setOwner(Role $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    public function setConversation(?Conversation $conversation): static
    {
        $this->conversation = $conversation;

        return $this;
    }

    public function messageToPayload() {
        return [
            'role' => $this->getOwner()->value,
            'parts' => [['text' => $this->getContent()]]
        ];
    }
}

enum Role: string {
    case USER = "user";
    case MODEL = "model";
    case NULL = "";
}