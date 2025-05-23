<?php

namespace App\Controller\Api;

use App\Entity\Person;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api')]
class PersonController extends AbstractController
{
    #[Route('/person/register', name: '_person_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation des champs obligatoires
        $requiredFields = ['email', 'password', 'firstname', 'lastname'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return $this->json(['error' => "Le champ $field est requis"], 400);
            }
        }

        // Vérification de l'unicité de l'email
        $existingUser = $em->getRepository(Person::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['error' => 'Cet email est déjà utilisé'], 409);
        }

        // Création du nouvel utilisateur
        $user = new Person();
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setRoles(['ROLE_USER']);

        // Champs optionnels
        if (isset($data['title'])) {
            $user->setTitle($data['title']);
        }
        if (isset($data['phone'])) {
            $user->setPhoneNumber($data['phoneNumber']);
        }

        // Validation de l'entité
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
                'error' => 'Validation failed',
                'details' => (string) $errors
            ], 422);
        }

        // Persistance de l'utilisateur
        $em->persist($user);
        $em->flush();

        // Génération du token JWT
        $token = $jwtManager->create($user);

        return $this->json([
            'message' => 'Utilisateur créé avec succès',
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ], 201);
    }

    #[Route('/person', name: '_person', methods: ['POST'])]
    public function getUserData(TokenStorageInterface $tokenStorage, EntityManagerInterface $em): JsonResponse
    {
        $token = $tokenStorage->getToken();
        $user = $token->getUser();
        if (!$user instanceof Person) {
            return $this->json(['error' => 'Utilisateur invalide'], 401);
        }
        $reservations = $em->getRepository(Reservation::class)->findBy(['person' => $user]);

        $formattedReservations = [];
        foreach ($reservations as $reservation) {
            $garage = $reservation->getGarage();
            $formattedOperations = [];

            foreach ($reservation->getOperations() as $operation) {
                $formattedOperations[] = [
                    'name' => $operation->getName(),
                    'duration' => $operation->getDuration(),
                    'price' => $operation->getPrice()
                ];
            }

            $formattedReservations[] = [
                'id' => $reservation->getId(),
                'start_date' => $reservation->getStartDate()->format('d/m/Y'),
                'end_date' => $reservation->getEndDate()->format('d/m/Y'),
                'price' => $reservation->getPrice(),
                'comment' => $reservation->getComment(),
                'description' => $reservation->getDescription(),
                'garage_name' => $garage->getName(),
                'garage_adress' => $garage->getAdress(),
                'garage_phone' => $garage->getPhoneNumber(),
                'operations' => $formattedOperations
            ];
        }
        $response = [
            'user' => [
                'first_name' => $user->getFirstname(),
                'last_name' => $user->getLastname(),
                'address' => $user->getAdress(),
                'phoneNumber' => $user->getPhoneNumber(),
            ],
            'reservations' => $formattedReservations
        ];
        return $this->json($response, 201);
    }
}