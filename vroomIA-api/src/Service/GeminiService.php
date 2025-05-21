<?php

namespace App\Service;

use App\Entity\Conversation;
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
    private string $url;

    public function __construct(HttpClientInterface $client, string $googleGeminiApiKey, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
        $this->apiKey = $googleGeminiApiKey;
        $this->url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$this->apiKey";
    }

    /**
     * Generates a response using the Gemini API from a given prompt.
     *
     * @param string $prompt The prompt to generate text from.
     * @return string|null The generated text or null if an error occurs.
     */
    public function generateText(string $prompt): ?string
    {
        $response = $this->client->request('POST', $this->url, [
            'json' => [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]
        ]);

        $data = $response->toArray(false);

        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    /**
     * Generates a response using the Gemini API folwing a given Conversation.
     *
     * @param Conversation $conversation The conversation to generate text from.
     * @return string|null The generated text or null if an error occurs.
     */
    public function generateWithConversation(Conversation $conversation): ?string
    {
        $response = $this->client->request('POST', $this->url, [
            'json' => [
                'contents' => $conversation->conversationToPayload(),
            ]
        ]);

        $data = $response->toArray(false);
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }

    /**
     * Generates a response using the Gemini API with a given Conversation and generation configuration.
     *
     * @param Conversation $conversation The conversation to generate text from.
     * @param array $generationConfig The generation configuration.
     * @return string|null The generated text or null if an error occurs.
     */
    public function generateWithConversationAndGenerationConfig(Conversation $conversation, array $generationConfig)
    {
        $response = $this->client->request('POST', $this->url, [
            'json' => [
                'contents' => $conversation->conversationToPayload(),
                'generationConfig' => $generationConfig
            ]
        ]);

        $data = $response->toArray(false);
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
            if ($fieldName === 'id' || $fieldName === 'password' || $fieldName === 'roles') {
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