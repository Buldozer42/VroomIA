<?php

namespace App\DataFixtures;

use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class VehicleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $vehicles = [
            [
                'registrationNumber' => 'AA-123-BB',
                'model' => 'Clio',
                'brand' => 'Renault',
                'year' => 2020,
                'vin' => '1HGCM82633A123456',
                'mileage' => 25000,
                'lastTechnicalInspectionDate' => '2024-03-15',
            ],
            [
                'registrationNumber' => 'CC-456-DD',
                'model' => '308',
                'brand' => 'Peugeot',
                'year' => 2019,
                'vin' => '2FMDK48C87BA98765',
                'mileage' => 42000,
                'lastTechnicalInspectionDate' => '2024-01-10',
            ],
            [
                'registrationNumber' => 'EE-789-FF',
                'model' => 'Golf',
                'brand' => 'Volkswagen',
                'year' => 2021,
                'vin' => 'WVWZZZ1KZAW654321',
                'mileage' => 18000,
                'lastTechnicalInspectionDate' => '2024-04-22',
            ],
            [
                'registrationNumber' => 'GG-012-HH',
                'model' => 'C3',
                'brand' => 'Citroën',
                'year' => 2018,
                'vin' => 'VF7DDNFPBJJ135790',
                'mileage' => 58000,
                'lastTechnicalInspectionDate' => '2023-11-05',
            ],
            [
                'registrationNumber' => 'II-345-JJ',
                'model' => 'Captur',
                'brand' => 'Renault',
                'year' => 2022,
                'vin' => 'VF15SRHA4GA246813',
                'mileage' => 12000,
                'lastTechnicalInspectionDate' => '2024-05-01',
            ],
            [
                'registrationNumber' => 'KK-678-LL',
                'model' => '2008',
                'brand' => 'Peugeot',
                'year' => 2020,
                'vin' => 'VF3CCBHX6JT579246',
                'mileage' => 31000,
                'lastTechnicalInspectionDate' => '2024-02-18',
            ],
            [
                'registrationNumber' => 'MM-901-NN',
                'model' => 'Sandero',
                'brand' => 'Dacia',
                'year' => 2019,
                'vin' => 'UU18SDNL551368024',
                'mileage' => 47000,
                'lastTechnicalInspectionDate' => '2023-12-14',
            ],
            [
                'registrationNumber' => 'OO-234-PP',
                'model' => 'Corsa',
                'brand' => 'Opel',
                'year' => 2021,
                'vin' => 'W0L0XEP68J4157893',
                'mileage' => 19500,
                'lastTechnicalInspectionDate' => '2024-04-10',
            ],
            [
                'registrationNumber' => 'QQ-567-RR',
                'model' => 'Twingo',
                'brand' => 'Renault',
                'year' => 2018,
                'vin' => 'VF1CN0C0F39246875',
                'mileage' => 62000,
                'lastTechnicalInspectionDate' => '2023-10-20',
            ],
            [
                'registrationNumber' => 'SS-890-TT',
                'model' => '208',
                'brand' => 'Peugeot',
                'year' => 2022,
                'vin' => 'VF3CCHMZ6MT513987',
                'mileage' => 10000,
                'lastTechnicalInspectionDate' => '2024-04-28',
            ],
        ];

        foreach ($vehicles as $vehicleData) {
            $vehicle = new Vehicle();
            $vehicle->setRegistrationNumber($vehicleData['registrationNumber']);
            $vehicle->setModel($vehicleData['model']);
            $vehicle->setBrand($vehicleData['brand']);
            $vehicle->setYear($vehicleData['year']);
            $vehicle->setVin($vehicleData['vin']);
            $vehicle->setMileage($vehicleData['mileage']);
            $vehicle->setLastTechnicalInspectionDate(new \DateTime($vehicleData['lastTechnicalInspectionDate']));

            $manager->persist($vehicle);
        }

        $manager->flush();
    }
}
