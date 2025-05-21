<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\Person;
use App\Entity\Message;
use App\Entity\Role;
use App\Service\GeminiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class GeminiController extends AbstractController
{
    private $geminiService;
    private $exportJsonService;

    public function __construct(GeminiService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    #[Route('/gemini/json', name: 'gemini_json', methods: ['POST'])]
    public function handleJson(): JsonResponse
    {
        $conversation = new Conversation();
        $conversation->addMessage(new Message(
            Role::USER,
            "Je me nomme Jean Dupont, je suis un homme de 35 ans, mon email est jd@exemple.com"
        ));
        $conversation->addMessage(new Message(
            Role::USER,
            "A partir des messages précédents, génère un objet JSON représentant une personne (Person) selon le schéma attendu. Si certaines informations sont manquantes, laisse les champs correspondants vides (ex. : '' ou null, selon le contexte). Respecte la structure exacte attendue du JSON."
        ));

        try {
            $responseSchema = $this->geminiService->createResponseSchemaForGivenEntity(new Person());
            $result = $this->geminiService->generateWithConversationAndGenerationConfig($conversation, [
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
