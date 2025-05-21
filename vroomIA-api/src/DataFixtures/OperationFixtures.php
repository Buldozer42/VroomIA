<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class OperationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // Read data from concessions.csv
        $file = fopen(__DIR__ . '/car-operations.csv', 'r');
        if ($file === false) {
            throw new \RuntimeException('Failed to open car-operations.csv');
        }

        // Skip the header row
        fgetcsv($file);

        // Load data into the database
        while (($data = fgetcsv($file, 0, ';')) !== false) {
            $name = $data[0];
            $duration = $faker->numberBetween(30, 120);
            $price = $data[5];

            $operation = new Operation($name, $duration, $price);
            $operation->setCategory(Category::AUTRE);
            
            $manager->persist($operation);
        }

        fclose($file);
        $manager->flush();
    }
}
