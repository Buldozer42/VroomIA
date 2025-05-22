<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Role;
use App\Entity\Person;
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
     * Initializes a conversation with an initial prompt.
     *
     * @param Conversation $conversation The conversation to initialize.
     * @return Conversation The initialized conversation.
     */
    public function intitConversationWithInitPrompt(Conversation $conversation) : Conversation
    {
        $initParts = $this->getInitPayload();
        $initPrompt = $initParts[0]['text'] ?? null;

        $message = new Message(Role::USER, $initPrompt);
        $conversation->addMessage($message);
        $this->entityManager->persist($message);

        $response = $this->generateText($initPrompt);
        if ($response) {
            $message = new Message(Role::MODEL, $response);
            $conversation->addMessage($message);
            $this->entityManager->persist($message);
        } else {
            throw new \Exception("Failed to generate initial response from Gemini API.");
        }

        return $conversation;
    }

    /**
     * Creates a new conversation for a given person with an initial prompt.
     *
     * @param Person $person The person to create the conversation for.
     * @return Conversation The created conversation.
     */
    public function conversationFactory(Person $person): Conversation
    {
        $conversation = new Conversation();
        $conversation->setPerson($person);
        $conversation = $this->intitConversationWithInitPrompt($conversation);

        $this->entityManager->persist($conversation);
        $this->entityManager->flush();
        return $conversation;
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

    public function formatJsonData(string $aiPayload): ?string {
        $filePath = dirname(__DIR__, 2) . "/config/jsonPrompt.txt";
        $textContent = "";

        if (file_exists($filePath)) {
            $textContent = file_get_contents($filePath);
        } else {
            $textContent = "No initial prompt found.";
        }
        $response = $this->generateText($textContent . $aiPayload);

        if (!$response) {
            throw new \Exception("Failed to generate initial response from Gemini API.");
        }
        // handleJson($response);
        return $response;
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
        $reflectionClass = new \ReflectionClass($className);

        $properties = [];
        $propertyOrdering = [];

        foreach ($metadata->getFieldNames() as $fieldName) {
            if ($fieldName === 'id' || $fieldName === 'password' || $fieldName === 'roles') {
                continue;
            }
            $type = $metadata->getTypeOfField($fieldName);
            $mappedType = $this->mapDoctrineTypeToSchemaType($type);

            $properties[$fieldName] = ['type' => $mappedType];
            
            // Vérifier les contraintes Assert\Choice pour ajouter des enum si nécessaire
            if ($reflectionClass->hasProperty($fieldName)) {
                $property = $reflectionClass->getProperty($fieldName);
                $attributes = $property->getAttributes(\Symfony\Component\Validator\Constraints\Choice::class, \ReflectionAttribute::IS_INSTANCEOF);
                
                if (!empty($attributes)) {
                    $attribute = $attributes[0]->newInstance();
                    if (isset($attribute->choices) && !empty($attribute->choices)) {
                        $properties[$fieldName]['enum'] = $attribute->choices;
                    }
                }
            }
            
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

    /**
     * Generates the initial payload for the conversation.
     *
     * @return array The initial payload.
     */
    private function getInitPayload(): array 
    {
        $filePath = dirname(__DIR__, 2) . "/config/prompt.txt";
        $textContent = "";
        $csvFilesPaths = ["src/DataFixtures/car-operations.csv", "src/DataFixtures/concessions.csv"];

        if (file_exists($filePath)) {
            $textContent = file_get_contents($filePath);
        } else {
            $textContent = "No initial prompt found.";
        }

        $parts = [['text' => $textContent]];

        // foreach ($csvFilesPaths as $filePath) {
        //     if (file_exists($filePath) && is_readable($filePath)) {
        //         $fileContent = file_get_contents($filePath);
        //         $mimeType ='text/csv';

        //         $parts[] = [
        //             'fileData' => [
        //                 'mimeType' => $mimeType,
        //                 'data' => base64_encode($fileContent),
        //             ],
        //         ];
        //     } else {
        //         // Log or handle the error for non-existent/unreadable files
        //         error_log("CSV file not found or not readable: " . $filePath);
        //     }
        // }
        return $parts;
    }
}