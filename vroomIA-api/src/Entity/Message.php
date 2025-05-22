<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private ?array $files = null;

    public function __construct(Role $sender, string $message, ?array $fileArray = null)
    {
        $this->owner = $sender;
        $this->content = $message;
        $this->files = $fileArray;
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
        $parts = [['text' => $this->getContent()]];

        
        if ($this->files != null) {
            foreach ($this->files as $filePath) {
                $projectRoot = dirname(__DIR__);
                $absolutePath = $projectRoot . $filePath;
                if (file_exists($absolutePath) && is_readable($absolutePath)) {
                    $fileContent = file_get_contents($absolutePath);
                    $mimeType ='text/csv'; // TO CHANGE IF WE ALLOW FILES FOR THE USERS
    
                    $parts[] = [
                        'inlineData' => [
                            'mimeType' => $mimeType,
                            'data' => base64_encode($fileContent),
                        ],
                    ];
                } else {
                    error_log("CSV file not found or not readable: " . $absolutePath);
                }
            }
        }

        return [
            'role' => $this->getOwner()->value,
            'parts' => [$parts]
        ];
    }

    public function getFiles(): ?array
    {
        return $this->files;
    }

    public function setFiles(?array $files): static
    {
        $this->files = $files;

        return $this;
    }
}

enum Role: string {
    case USER = "user";
    case MODEL = "model";
    case NULL = "";
}