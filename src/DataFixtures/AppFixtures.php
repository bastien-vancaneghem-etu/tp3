<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Tasks;
use App\Entity\Comment;
use App\Entity\Sessions;
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
        // $roles = $manager->getRepository(Roles::class)->findAll();


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
        $users = [];
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

                // On ajoute l'utilisateur au tableau
                $users[] = $user;
            }

            // On hash le mot de passe
            $hash = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($hash);


            // On fait persister les données
            $manager->persist($user);
        }

        $sessions = [];
        $statueSession = ['started', 'finished', 'stopped'];

        //création de 10 sessions
        for($t=0;$t<10;$t++){
            // Création d'un nouvel objet Sessions
            $session = new Sessions;

            // On nourrit l'objet
            $session->setStartTime($faker->dateTimeBetween('-1 years', 'now'))
                ->setEndTime($faker->dateTimeBetween('-1 years', 'now'))
                ->setDate($faker->dateTimeBetween('-1 years', 'now'))
                ->setDuration($faker->dateTimeBetween('-1 years', 'now'))
                ->setUser($faker->randomElement($users))
                ->setStatue($faker->randomElement($statueSession));

            // On ajoute la session au tableau
            $sessions[] = $session;

            // On fait persister les données
            $manager->persist($session);
        }

        //création de commentaires
        for($c=0;$c<20;$c++){
            // Création d'un nouvel objet Tasks
            $comment = new Comment;

            // On nourrit l'objet
            $comment->setQuestion($faker->sentence(3))
                ->setResponse($faker->sentence(3))
                ->setSession($faker->randomElement($sessions));

            // On fait persister les données
            $manager->persist($comment);
        }

        //création de 50 tâches
        for($t=0;$t<50;$t++){
            // Création d'un nouvel objet Tasks
            $task = new Tasks;

            // On nourrit l'objet
            $task->setName($faker->sentence(3))
                ->setStatue($faker->boolean())
                ->setSession($faker->randomElement($sessions));

            // On fait persister les données
            $manager->persist($task);
        }

        //push en bdd
        $manager->flush();
        
    }
}
