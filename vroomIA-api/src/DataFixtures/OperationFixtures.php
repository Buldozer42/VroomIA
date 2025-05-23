<?php

namespace App\DataFixtures;

use App\Entity\Operation;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class OperationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // // Read data from concessions.csv
        // $file = fopen(__DIR__ . '/car-operations.csv', 'r');
        // if ($file === false) {
        //     throw new \RuntimeException('Failed to open car-operations.csv');
        // }

        // // Skip the header row
        // fgetcsv($file);

        // // Load data into the database
        // while (($data = fgetcsv($file, 0, ';')) !== false) {
        //     $name = $data[0];
        //     $duration = $data[4];
        //     $price = $data[5];

        //     $operation = new Operation($name, $duration, $price);
        //     $operation->setCategory(Category::AUTRE);
            
        //     $manager->persist($operation);
        // }

        // fclose($file);
        // $manager->flush();
    }
}
