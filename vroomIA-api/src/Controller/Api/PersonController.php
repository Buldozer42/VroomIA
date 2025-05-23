<?php

namespace App\Controller\Api;

use App\Entity\Person;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/person/reservations', name: '_person_reservations', methods: ['POST'])]
    public function getUserReservations(Request $request, EntityManagerInterface $em, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $token = $tokenStorage->getToken();
        if (!$token) {
            return $this->json(['error' => 'Non authentifié'], 401);
        }
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
                'operations' => $formattedOperations // Ajout du tableau des opérations
            ];
        }

        return $this->json($formattedReservations,201);
    }

    #[Route('/person/register', name: '_person_register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['email']) || !isset($data['password']) || !isset($data['firstname']) || !isset($data['lastname'])) {
            return $this->json(['error' => 'Email and password are required'], 400);
        }

        $existingUser = $em->getRepository(Person::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['error' => 'Email already exists'], 409);
        }

        $user = new Person();
        $user->setEmail($data['email']);
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        $user->setTitle($data['title']);
        $user->setPhoneNumber($data['phoneNumber']);
        $user->setRoles(['ROLE_USER']);
        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            /*
             * Uses a __toString method on the $errors variable which is a
             * ConstraintViolationList object. This gives us a nice string
             * for debugging.
             */
            $errorsString = (string) $errors;

            return $this->json([
                'message' => $errorsString,
                'user' => [
                    'email' => $user->getEmail(),
                    'id' => $user->getId()
                ]
            ], 201);
        }

        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'User created successfully',
            'user' => [
                'email' => $user->getEmail(),
                'id' => $user->getId()
            ]
        ], 201);
    }
}