<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Roles;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();

        $faker = Factory::create('fr_FR');

        // Récupération des roles
        $roles = $manager->getRepository(Roles::class)->findAll();


        // Création de 10 sessions
        for($u=0;$u<4;$u++){
            // Création d'un nouvel objet
            $user = new Users;
            
            // On nourrit l'objet

            $user->setName($faker->name())
                ->setRole();

            // On fait persister les données
            $manager->persist($user);
        }

        $manager->flush();
        
    }
}
