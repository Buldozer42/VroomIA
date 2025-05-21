<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Person;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // Le json_login gère tout
        return $this->json(['error' => 'Authentication should be handled by JSON login']);
    }

    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
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
        $user->setPhoneNumber($data['phone']);
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