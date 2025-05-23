<?php

namespace App\Controller\Api;

use App\Entity\Adress;
use App\Entity\Garage;
use App\Entity\Operation;
use App\Entity\Person;
use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/reservation', name: 'api_reservation_')]
class ReservationController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['POST'])]
    public function createReservation(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['description']) || !isset($data['start_date']) || !isset($data['end_date']) || !isset($data['comment']) || !isset($data['garage_id']) || !isset($data['person_id'])) {
            return $this->json(['error' => 'Données manquante'], 400);
        }

        $user = $em->getRepository(Person::class)->findOneBy(['id' => $data['person_id']]);
        if (!$user) {
            return $this->json(['error' => 'No user found'], 409);
        }
        $start_date = \DateTime::createFromFormat('d/m/Y',$data['start_date']);
        $end_date = \DateTime::createFromFormat('d/m/Y',$data['end_date']);
        $reservation = new Reservation();
        $reservation->setDescription($data['description']);
        $reservation->setStartDate($start_date);
        $reservation->setEndDate($end_date);
        $reservation->setComment($data['comment']);
        $prix = 0;
        foreach ($data['operations'] as $operation) {
            $operationObj = $em->getRepository(Operation::class)->findOneBy(['id' => $operation]);
            $prix += $operationObj->getPrice();
        }
        $reservation->setPrice($prix);
        $garage = $em->getRepository(Garage::class)->findOneBy(['id' => $data['garage_id']]);
        $reservation->setGarage($garage);
        $reservation->setPerson($user);

        $em->persist($reservation);
        $em->flush();

        return $this->json(['message' => 'Reservation created successfully',], 201);
    }

    #[Route('/remove', name: 'remove', methods: ['POST'])]
    public function removeVehicule(Request $request, EntityManagerInterface $em, ReservationRepository $rm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $reservation = $rm->findOneBy(['id' => $data['reservation_id']]);
        if (empty($reservation)) {
            return $this->json(['message' => 'Reservation not found']);
        }
        $em->remove($reservation);
        $em->flush();
        return $this->json(['message' => 'Reservation deleted successfully'], 201);
    }

    #[Route('/update', name: 'update', methods: ['POST'])]
    public function updateReservationDates(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['reservation_id']) || !isset($data['start_date']) || !isset($data['end_date'])) {
            return $this->json(['error' => 'Données manquantes'], 400);
        }

        $reservation = $em->getRepository(Reservation::class)->findOneBy(['id' => $data['reservation_id']]);

        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        $start_date = \DateTime::createFromFormat('d/m/Y', $data['start_date']);
        $end_date = \DateTime::createFromFormat('d/m/Y', $data['end_date']);

        if (!$start_date || !$end_date) {
            return $this->json(['error' => 'Format de date invalide.'], 400);
        }

        $reservation->setStartDate($start_date);
        $reservation->setEndDate($end_date);

        $em->flush();

        return $this->json([
            'message' => 'Dates mises à jour avec succès',
            'start_date' => $reservation->getStartDate()->format('d/m/Y'),
            'end_date' => $reservation->getEndDate()->format('d/m/Y')
        ], 200);
    }

}