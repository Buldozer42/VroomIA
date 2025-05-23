<?php

namespace App\Controller\Api;

use App\Entity\Person;
use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use App\Service\VehicleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/vehicule', name: 'api_vehicule_')]
class VehiculeController extends AbstractController
{
    #[Route('/add', name: 'add', methods: ['POST'])]
    public function addVehicule(Request $request, EntityManagerInterface $em, VehicleRepository $vm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $client = $em->getRepository(Person::class)->findOneBy(['id' => $data['person_id']]);
        $vehiculeService = new VehicleService($client,$vm);
        if(!$vehiculeService->checkRegistrationNumber($data['registration_number'])){
            return $this->json(['messsage' => 'Invalid registration number']);
        }

        $vehicle = new Vehicle();
        $vehicle->setRegistrationNumber($data['registration_number']);
        $vehicle->setModel($data['model'] ?? '');
        $vehicle->setBrand($data['marque'] ?? '');
        $vehicle->setVin($data['vin'] ?? '');
        return $this->json([
            'message' => 'Vehicule created successfully',
        ], 201);
    }

    #[Route('/remove', name: 'remove', methods: ['POST'])]
    public function removeVehicule(Request $request, EntityManagerInterface $em, VehicleRepository $vm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $vehicule = $vm->findOneBy(['id' => $data['vehicule_id']]);
        if (empty($vehicule)) {
            return $this->json(['message' => 'Vehicule not found']);
        }
        $em->remove($vehicule);
        $em->flush();
        return $this->json(['message' => 'Vehicule deleted successfully'], 201);
    }

    #[Route('/update', name: 'update', methods: ['PATCH'])]
    public function updateVehicule(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['vehicule_id'])) {
            return $this->json(['error' => 'vehicule_id missing'], 400);
        }

        $vehicule = $em->getRepository(Vehicle::class)->find($data['vehicule_id']);

        if (!$vehicule) {
            return $this->json(['error' => 'Véhicule non trouvé'], 404);
        }

        if (isset($data['brand'])) {
            $vehicule->setMarque($data['brand']);
        }
        if (isset($data['model'])) {
            $vehicule->setModel($data['model']);
        }

        if (isset($data['registration_number'])) {
            $vehicule->setImmatriculation($data['immatriculation']);
        }

        $em->flush();

        return $this->json([
            'message' => 'Véhicule mis à jour avec succès',
            'vehicule_id' => $vehicule->getId()
        ], 200);
    }

    #[Route('/get', name: 'get', methods: ['POST'])]
    public function getVehicule(Request $request, VehicleRepository $vm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $vehicule = $vm->findOneBy(['id' => $data['vehicule_id']]);

        if (empty($vehicule)) {
            return $this->json(['message' => 'Vehicule not found'], 404);
        }

        return $this->json([
            'id' => $vehicule->getId(),
            'brand' => $vehicule->getBrand(),
            'model' => $vehicule->getModel(),
            'vin' => $vehicule->getVin(),
            'last_technical_inspection_date' => $vehicule->getLastTechnicalInspectionDate(),
            'kilometrage' => $vehicule->getMileage(),
            'registration_number' => $vehicule->getRegistrationNumber(),
            'year' => $vehicule->getYear(),
        ], 200);
    }

}