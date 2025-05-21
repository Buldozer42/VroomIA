<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * GeminiService
 *
 * This service interacts with the Google Gemini API.
 */
class GeminiService
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $client;
    private string $apiKey;
    private Conversation $conv;

    public function __construct(HttpClientInterface $client, string $googleGeminiApiKey, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->apiKey = $googleGeminiApiKey;
        $this->conv = new Conversation();
    }

    /**
     * Generates responsce using the Gemini API from a given prompt.
     *
     * @param string $prompt The prompt to generate text from.
     * @return string|null The generated text or null if an error occurs.
     */
    public function generateText(string $prompt): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey;
        $this->conv->addMessage(new Message(Role::USER, $prompt));
        $response = $this->client->request('POST', $url, [
            'json' => [
                'contents' => [
                    $this->conv->conversationToPayload()
                ]
            ]
        ]);



        $data = $response->toArray(false);
        $message = $data['candidates'][0]['content']['parts'][0]['text'];
        dump($message);
        $this->conv->addMessage(new Message(Role::MODEL, $message));
        return $message ?? null;
    }

    public function getConversation() {
        return $this->conv;
    }

    /**
     * Generates a response using the Gemini API from a given context (like a conversation).
     *
     * @param array $context The context to generate text from.
     * @return string|null The generated text or null if an error occurs.
     */
    public function generateWithContext($context): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey;

        $response = $this->client->request('POST', $url, [
            'json' => $context
        ]);

        $data = $response->toArray(false);
    }

    public function getConversation() {
        return $this->conv;
    }

    /**
     * Generates a response using the Gemini API from a given context and generation configuration.
     *
     * @param array $context The context to generate text from.
     * @param array $generationConfig The generation configuration.
     * @return string|null The generated text or null if an error occurs.
     */
    public function generateWithContextAndGenerationConfig($context, $generationConfig): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey;

        $response = $this->client->request('POST', $url, [
            'json' => [
                'contents' => $context,
                'generationConfig' => $generationConfig
            ]
        ]);

        $data = $response->toArray(false);
        $message = $data['candidates'][0]['content']['parts'][0]['text'];
        dump($message);
        $this->conv->addMessage(new Message(Role::MODEL, $message));
        return $message ?? null;
    }

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
    
    /**
     * Creates a response schema for a given entity.
     *
     * @param object $entity The entity to create the schema for.
     * @return array The response schema.
     */
    public function createResponseSchemaForGivenEntity(object $entity): array
    {
        $className = get_class($entity);

        $metadata = $this->entityManager->getClassMetadata($className);

        $properties = [];
        $propertyOrdering = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($fieldName === 'id' || $fieldName === 'password') {
                continue;
            }
            $type = $metadata->getTypeOfField($fieldName);
            $mappedType = $this->mapDoctrineTypeToSchemaType($type);

            $properties[$fieldName] = ['type' => $mappedType];
            $propertyOrdering[] = $fieldName;
        }

        return [
            "type" => "ARRAY",
            "items" => [
                "type" => "OBJECT",
                "properties" => $properties,
                "propertyOrdering" => $propertyOrdering
            ]
        ];
    }

    /**
     * Maps Doctrine types to schema types.
     *
     * @param string $doctrineType The Doctrine type.
     * @return string The mapped schema type.
     */
    private function mapDoctrineTypeToSchemaType(string $doctrineType): string
    {
        return match ($doctrineType) {
            'integer', 'bigint', 'smallint' => 'INTEGER',
            'string', 'text', 'guid' => 'STRING',
            'datetime', 'datetimetz', 'date', 'time' => 'DATE',
            'boolean' => 'BOOLEAN',
            'float', 'decimal' => 'NUMBER',
            default => 'STRING',
        };
    }
}