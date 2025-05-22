<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\Person;
use App\Entity\Message;
use App\Entity\Role;
use App\Repository\ConversationRepository;
use App\Repository\PersonRepository;
use App\Service\GeminiService;
use App\Service\JsonSerializerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api', name: 'api_')]
class GeminiController extends AbstractController
{
    private $geminiService;
    private $jsonSerializerService;

    private $entityManager;

    public function __construct(GeminiService $geminiService, JsonSerializerService $jsonSerializerService, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->geminiService = $geminiService;
        $this->jsonSerializerService = $jsonSerializerService;
    }

    #[Route('/gemini/conversation/new', name: 'gemini_conversation_new', methods: ['POST'])]
    public function newConversation(Request $request, PersonRepository $personRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $personId = $data['personId'] ?? null;

        $person = $personRepository->find($personId);
        if (!$person) {
            return $this->json(['error' => 'Person not found'], 404);
        }

        $conversation = $this->geminiService->conversationFactory($person);

        return $this->json([
            'conversationId' => $conversation->getId(),
        ]);
    }

    #[Route('/gemini/message/send', name: 'gemini_message_send', methods: ['POST'])]
    public function sendMessage(Request $request, ConversationRepository $conversationRepository): JsonResponse        
    {
        $data = json_decode($request->getContent(), true);
        $conversationId = $data['conversationId'] ?? null;
        $messageContent = $data['messageContent'] ?? null;
        if (!$conversationId || !$messageContent) {
            return $this->json(['error' => 'Conversation ID and message content are required'], 400);
        }

        $conversation = $conversationRepository->find($conversationId);
        if (!$conversation) {
            return $this->json(['error' => "Conversation not found id : $conversationId"], 404);
        }

        $newMessage = new Message(
            Role::USER,
            $messageContent
        );

        $conversation->addMessage($newMessage);
        $this->entityManager->persist($newMessage);
        $this->entityManager->flush();

        try {
            $result = $this->geminiService->generateWithConversation($conversation);
            if ($result === null) {
                return $this->json(['error' => 'Échec de génération du texte.'], 500);
            }

            $geminiMessage = new Message(
                Role::MODEL,
                $result
            );

            $conversation->addMessage($geminiMessage);
            $this->entityManager->persist($geminiMessage);
            $this->entityManager->flush();

            return $this->json([
                'geminiResponse' => $result,
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }        
    }

    // ------------------------------------------------------------------------------------------------
    // TEST ROUTE
    // ------------------------------------------------------------------------------------------------
    #[Route('/gemini/test/create-person', name: 'gemini_test_create-person', methods: ['POST'])]
    public function createPerson(): JsonResponse
    {
        $conversation = new Conversation();
        $conversation->addMessage(new Message(
            Role::USER,
            "Je me nomme Jean Dupont, je suis un homme de 35 ans, mon email est j" . rand(1, 100) . "@gmail.com"
        ));
        $conversation->addMessage(new Message(
            Role::USER,
            "A partir des messages précédents, génère un objet JSON représentant une personne (Person) selon le schéma attendu. Si certaines informations sont manquantes, laisse les champs correspondants vides (ex. : '', null ou [], selon le contexte). Respecte la structure exacte attendue du JSON. IMPORTANT: le champ 'title' doit obligatoirement être l'une de ces valeurs exactes: 'Mr', 'Mme' ou 'Companie'."
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
            
            $personData = $decodedResult[0];
            $personJson = json_encode($personData);   
            $res = $this->jsonSerializerService->createEntityFromJson($personJson, Person::class);
            
            return $this->json([
                'decodedResult' => $decodedResult,
                'res' => $res,
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }        
    }    #[Route('/gemini/test/update-person/{id}', name: 'gemini_test_update-person', methods: ['GET'])]
    public function updatePerson(Person $person): JsonResponse
    {
        if (!$person) {
            return $this->json(['error' => 'Person not found'], 404);
        }

        $mail = $person->getEmail();

        $conversation = new Conversation();
        $conversation->addMessage(new Message(
            Role::USER,
            "Je me nomme Karl Loco, je suis un homme de 35 ans"
        ));
        $conversation->addMessage(new Message(
            Role::USER,
            "A partir des messages précédents, génère un objet JSON représentant une personne (Person) selon le schéma attendu. Si certaines informations sont manquantes, laisse les champs correspondants vides (ex. : '', null ou [], selon le contexte). Respecte la structure exacte attendue du JSON. IMPORTANT: le champ 'title' doit obligatoirement être l'une de ces valeurs exactes: 'Mr', 'Mme' ou 'Companie'."
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
            
            $personData = $decodedResult[0];
            $personJson = json_encode($personData);   
            $res = $this->jsonSerializerService->updateEntityFromJson($personJson, $person);
            
            return $this->json([
                'baseEmail' => $mail,
                'decodedResult' => $decodedResult,
                'res' => $res,
            ]);

        } catch (\Exception $e) {
            return $this->json(['error' => 'Une erreur est survenue: ' . $e->getMessage()], 500);
        }
    }
}
