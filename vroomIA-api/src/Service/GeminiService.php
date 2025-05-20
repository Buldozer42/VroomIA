<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Role;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private $client;
    private $apiKey;
    private Conversation $conv;

    public function __construct(HttpClientInterface $client, string $googleGeminiApiKey)
    {
        $this->client = $client;
        $this->apiKey = $googleGeminiApiKey;
        $this->conv = new Conversation();
    }

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
}