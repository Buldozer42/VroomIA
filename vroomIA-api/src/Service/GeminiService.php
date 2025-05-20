<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeminiService
{
    private $client;
    private $apiKey;

    public function __construct(HttpClientInterface $client, string $googleGeminiApiKey)
    {
        $this->client = $client;
        $this->apiKey = $googleGeminiApiKey;
    }

    public function generateText(string $prompt): ?string
    {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $this->apiKey;

        $response = $this->client->request('POST', $url, [
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
}