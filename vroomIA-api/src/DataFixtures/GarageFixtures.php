<?php

namespace App\DataFixtures;

use App\Entity\Adress;
use App\Entity\Garage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GarageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Read data from concessions.csv
        $file = fopen(__DIR__ . '/concessions.csv', 'r');
        if ($file === false) {
            throw new \RuntimeException('Failed to open concessions.csv');
        }

        // Skip the header row
        fgetcsv($file);

        // Load data into the database
        while (($data = fgetcsv($file, 0, ';')) !== false) {
            $garage = new Garage();
            $garage->setName($data[0]);

            $address = new Adress();
            $address->setCity($data[1]);
            $address->setStreet($data[2]);
            $address->setPostalCode($data[3]);
            $address->setCountry('France');
            $address->setRegion('');
            $address->setAdditionalInfo('');
            $address->setLatitude($data[4]);
            $address->setLongitude($data[5]);

            $garage->setAdress($address);
            $garage->setPhoneNumber('');

            $manager->persist($address);
            $manager->persist($garage);
        }

        fclose($file);
        $manager->flush();
    }
}
