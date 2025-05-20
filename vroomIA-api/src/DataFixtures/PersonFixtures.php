<?php

namespace App\DataFixtures;

use App\Entity\Adress;
use App\Entity\Person;
use App\Entity\Driver;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class PersonFixtures extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $vehicles = $manager->getRepository(Vehicle::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $person = new Person();
            
            $gender = $faker->randomElement(['male', 'female']);
            $title = ($gender === 'male') ? 'M.' : 'Mme';
            
            $firstName = $faker->firstName($gender);
            $lastName = $faker->lastName;
            $email = strtolower($firstName) . '.' . strtolower($lastName) . '@' . $faker->safeEmailDomain;
            
            $person->setEmail($email);
            $person->setTitle($title);
            $person->setFirstname($firstName);
            $person->setLastname($lastName);
            $person->setPhoneNumber($faker->phoneNumber);
            
            $password = $this->passwordHasher->hashPassword($person, 'test');
            $person->setPassword($password);
            
            $person->setRoles(['ROLE_USER']);
            
            $address = new Adress();
            $address->setStreet($faker->streetAddress);
            $address->setCity($faker->city);
            $address->setPostalCode($faker->numberBetween(10000, 99999));
            $address->setCountry('France');
            $address->setRegion($faker->departmentName);
            $address->setAdditionalInfo('');
            $address->setLatitude($faker->latitude(42.0, 51.0));
            $address->setLongitude($faker->longitude(-5.0, 8.0));
            
            $manager->persist($address);
            $person->setAdress($address);

            $person->addVehicle($vehicles[$i]);
            
            if ($i == 8 || $i == 9) {
                $person->setTitle('Société');
                $person->setCompanyName($faker->company);

                $driver = new Driver();
                $driver->setPerson($person);
                $driver->setLastname($faker->lastName);
                $driver->setFirstname($faker->firstName($gender));
                $driver->setPhoneNumber($faker->phoneNumber);
            }

            $manager->persist($person);
        }
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            VehicleFixtures::class,
        ];
    }
}