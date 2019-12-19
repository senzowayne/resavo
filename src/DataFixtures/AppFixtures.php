<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $salle = [];

        for ($i = 0; $i < 3; $i++) {
            $salle[$i] = new Salle();
            $salle[$i]->setNom('Salle ' . $i);
            $salle[$i]->setPrix(90);

            $manager->persist($salle[$i]);
        }

        $seanceLibelles = array(    '10h - 12h',
                                    '12h30 - 14h30',
                                    '15h - 17h',
                                    '17h30 - 19h30',
                                    '20h - 22h'
        );

        for ($i = 0; $i < 5; $i++) {
            $seance[$i] = new Seance();
            $seance[$i]->setLibelle($seanceLibelles[$i]);

            $manager->persist($seance[$i]);
        }

        $faker = Factory::create('fr-FR');
        $nom = $faker->firstName;
        $prenom = $faker->lastName;
        $email = $faker->email;
        $slug = $faker->slug;
        $hash = '$2y$13$Li.rLne01AHwc.ituzQ/jejHxxKO.BU4A9Fc9hmCc3.PprUhaJbRa'; // "password"
        $avatar = $faker->imageUrl($width = 50, $height = 50);
        $user = [];

        for ($i = 0; $i < 20; $i++) {
            $user[$i] = new User();
            $user[$i]->setNom($nom)
                     ->setPrenom($prenom)
                     ->setEmail($email)
                     ->setAvatar($avatar)
                     ->setHash($hash)
                     ->setSlug($slug);

            $manager->persist($user[$i]);
        }

        $admin = new User();
        $admin  ->setNom('resavo')
                ->setPrenom('Admin')
                ->setEmail('admin@resavo.fr')
                ->setAvatar($faker->imageUrl($width = 50, $height = 50))
                ->setHash($hash)
                ->setSlug('admin');

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $adminRole->addUser($admin);

        $manager->persist($admin);
        $manager->persist($adminRole);

        $manager->flush();
    }
}
