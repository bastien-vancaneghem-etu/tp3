<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Roles;
use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    protected UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }


    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();

        $faker = Factory::create('fr_FR');

        // Récupération des roles
        $roles = $manager->getRepository(Roles::class)->findAll();


        // Création de 10 sessions
        /*for($u=0;$u<4;$u++){
            // Création d'un nouvel objet
            $user = new Users;
            
            // On nourrit l'objet

            $user->setName($faker->name())
                ->setRole();

            // On fait persister les données
            $manager->persist($user);
        }*/

        //création de 5 utilisateurs
        for($u=0;$u<5;$u++){
            // Création d'un nouvel objet
            $user = new User;

            // On nourrit l'objet
            //si c'est le 1er utilisateur, il est admin
            if($u == 0){
                $user->setRoles(['ROLE_ADMIN'])
                    ->setEmail('test@test.com');
            } else {
                $user->setEmail($faker->safeEmail());
            }

            // On hash le mot de passe
            $hash = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($hash);


            // On fait persister les données
            $manager->persist($user);
        }

        //push en bdd
        $manager->flush();
        
    }
}
