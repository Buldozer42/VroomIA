<?php

namespace App\Controller\Api;

use App\Entity\Person;
use App\Repository\PersonRepository;
use App\Service\GeminiService;
use App\Service\ExportJsonService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class GeminiController extends AbstractController
{
    private $geminiService;
    private $exportJsonService;

    public function __construct(GeminiService $geminiService, ExportJsonService $exportJsonService)
    {
        $this->geminiService = $geminiService;
        $this->exportJsonService = $exportJsonService;
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

    #[Route('/gemini/json', name: 'gemini_json', methods: ['POST'])]
    public function handleJson(Request $request, PersonRepository $personRepository): JsonResponse
    {
        $messages[] = [
            'role' => 'user',
            'parts' => [['text' => "Je me nomme Jean Dupont, je suis un homme de 35 ans, mon email est jd@exemple.com"]]
        ];

        $prompt = "A partir des messages précédents, génère un objet JSON représentant une personne (Person) selon le schéma attendu. Si certaines informations sont manquantes, laisse les champs correspondants vides (ex. : '' ou null, selon le contexte). Respecte la structure exacte attendue du JSON.";

        $messages[] = [
            'role' => 'user',
            'parts' => [['text' => $prompt]]
        ];

        try {
            $responseSchema = $this->geminiService->createResponseSchemaForGivenEntity(new Person());
            $result = $this->geminiService->generateWithContextAndGenerationConfig($messages, [
                "responseMimeType" => "application/json",
                "responseSchema" => $responseSchema,
            ]);
            
            
            if ($result === null) {
                return $this->json(['error' => 'Échec de génération du texte.'], 500);
            }

            $decodedResult = json_decode($result, true);
            return $this->json($decodedResult);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }
    }
}
