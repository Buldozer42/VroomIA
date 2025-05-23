<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\Person;
use App\Entity\Garage;
use App\Entity\Operation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReservationFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // $faker = Factory::create('fr_FR');
        // $persons = $manager->getRepository(Person::class)->findAll();
        // $garages = $manager->getRepository(Garage::class)->findAll();
        // $operations = $manager->getRepository(Operation::class)->findAll();

        // foreach ($persons as $person) {
        //     for ($i = 0; $i < 3; $i++) {
        //         $reservation = new Reservation();
        //         $reservation->setPerson($person);
        //         $reservation->addOperation($operations[array_rand($operations)]);
        //         $reservation->setGarage($garages[array_rand($garages)]);
        //         $reservation->setDescription($faker->sentence(10));
        //         $startDate = $faker->dateTimeBetween('now', '+1 month');
        //         $reservation->setStartDate($startDate);
        //         $reservation->setEndDate($faker->dateTimeBetween($startDate, (clone $startDate)->modify('+1 hour')));
        //         $reservation->setPrice($faker->randomFloat(2, 50, 500));
        //         $reservation->setComment($faker->sentence(5));
        //         $manager->persist($reservation);
        //     }
        // }

        // $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            // GarageFixtures::class,
            PersonFixtures::class,
            // OperationFixtures::class,
        ];
    }
}