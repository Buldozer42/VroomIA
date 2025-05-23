<?php

namespace App\Service;

use App\Entity\Person;
use App\Entity\Vehicle;
use App\Repository\VehicleRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VehicleService
{
    private Person $client;
    private VehicleRepository $vehicleRepository;

    public function __construct(Person $client, VehicleRepository $vehicleRepository)
    {
        $this->client = $client;
        $this->vehicleRepository = $vehicleRepository;
    }

    /**
     * Import vehicle data from a registration number using an external API.
     * 
     * Note: limted number of requests, in prod we should look for a SIV Authorization : https://www.cnil.fr/fr/siv-systeme-dimmatriculation-des-vehicules
     * 
     * @param string $registrationNumber
     * @return Vehicle|null
     */
    public function importVehicleFromRegistrationNumber(string $registrationNumber): ?Vehicle
    {
        // Check if the registration number format is valid
        if (!$this->checkRegistrationNumber($registrationNumber)) {
            throw new \InvalidArgumentException('Invalid registration number format.');
        }

        $url = "https://api.apiplaqueimmatriculation.com/plaque?immatriculation=$registrationNumber&token=TokenDemo2025A&pays=FR&_gl=1*1jv608h*_ga*MjIyNjI4NTk1LjE3NDc2NjI1Mjg.*_ga_CE6NMY2D05*czE3NDc2NjI1MjckbzEkZzEkdDE3NDc2NjI1NDUkajAkbDAkaDA.";
    
        $response = $this->client->request('GET', $url);
        $data = $response->toArray();
        $vehicleData = $data['data'] ?? null;
        if ($vehicleData && $vehicleData['erreur'] === '') {
            $vehicle = new Vehicle();
            $vehicle->setRegistrationNumber($registrationNumber);
            $vehicle->setModel($vehicleData['modele'] ?? '');
            $vehicle->setBrand($vehicleData['marque'] ?? '');
            $vehicle->setVin($vehicleData['vin'] ?? '');

            return $vehicle;
        }
    }

    /**
     * Cjeck if the registration number format is valid.
     * 
     * @param string $registrationNumber
     * @return bool
     */
    public function checkRegistrationNumber(string $registrationNumber): bool
    {
        return preg_match('/^[A-Z]{2}-[0-9]{3}-[A-Z]{2}$/', $registrationNumber);
    }
}
