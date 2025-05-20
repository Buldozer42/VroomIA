<?php

namespace App\Controller\Api;

use App\Service\GeminiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class GeminiController extends AbstractController
{
    private $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    #[Route('/gemini/generate-text', name: 'gemini_generate_text', methods: ['POST'])]
    public function generateText(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $prompt = $data['prompt'] ?? null;

        if (!$prompt) {
            return $this->json(['error' => 'Le paramètre "prompt" est requis.'], 400);
        }

        try {
            $result = $this->geminiService->generateText($prompt);
            
            if ($result === null) {
                return $this->json(['error' => 'Échec de génération du texte.'], 500);
            }
            
            return $this->json(['text' => $result]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }
    }
}
