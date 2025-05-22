<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\Role;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service who handles synchronization between json data, entities and gemini API
 */
class SyncService
{
    private EntityManagerInterface $entityManager;
    private JsonSerializerService $jsonSerializerService;
    private GeminiService $geminiService;

    public function __construct(
        EntityManagerInterface $entityManager,
        JsonSerializerService $jsonSerializerService,
        GeminiService $geminiService
    ) {
        $this->entityManager = $entityManager;
        $this->jsonSerializerService = $jsonSerializerService;
        $this->geminiService = $geminiService;
    }  

    private function updatePersonFromConversation(Conversation $conversation): Person
    {
        $person = $conversation->getPerson();
        if (!$person) {
            throw new \Exception('Person not found in conversation.');
        }

        $conversation->addMessage(new Message(
            Role::USER,
            "A partir des messages précédents, génère un objet JSON représentant une personne (Person) selon le schéma attendu. Si certaines informations sont manquantes, laisse les champs correspondants vides (ex. : '', null ou [], selon le contexte). Respecte la structure exacte attendue du JSON. IMPORTANT: le champ 'title' doit obligatoirement être l'une de ces valeurs exactes: 'Mr', 'Mme' ou 'Companie'."
        ));

        $oldPerson = clone $person;

        $responseSchema = $this->geminiService->createResponseSchemaForGivenEntity(new Person());
        $result = $this->geminiService->generateWithConversationAndGenerationConfig($conversation, [
            "responseMimeType" => "application/json",
            "responseSchema" => $responseSchema,
        ]);        
        
        if ($result === null) {
            throw new \Exception('Échec de génération du texte.');
        }

        $decodedResult = json_decode($result, true);
        
        $personData = $decodedResult[0];
        $personJson = json_encode($personData);
        $res = $this->jsonSerializerService->updateEntityFromJson($personJson, $person);
        if ($res['success'] === false) {
            throw new \Exception('Échec de la mise à jour de l\'entité Person: ' . json_encode($res['errors']));
        }

        if ($oldPerson->getEmail() !== $person->getEmail()) {
            $person->setEmail($oldPerson->getEmail());
        }

        $this->entityManager->flush();

        return $res['entity'];
    }
}