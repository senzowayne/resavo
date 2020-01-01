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
        $seanceLibelles = array(    '10h - 12h',
                                    '12h30 - 14h30',
                                    '15h - 17h',
                                    '17h30 - 19h30',
                                    '20h - 22h'
        );
        $e = 0;
        $nomSalle = ['Salle Bora-Bora', 'Salle Miami', 'Salle Phuket'];

        for ($i = 1; $i <= 3; $i++) {
            $salle[$i] = new Salle();
            $salle[$i]->setNom($nomSalle[$i -1]);
            $salle[$i]->setPrix(90);
            $salle[$i]->setDescription('Salle ' . $i);

            for ($x = 0; $x <= 4; $x++) {
                if ($e >= 5) {
                    $e = 0;
                }
                $seance[$x] = new Seance();
                $seance[$x]->setLibelle($seanceLibelles[$e]);
                $salle[$i]->addSeance($seance[$x]);
                $e++;
                $manager->persist($seance[$x]);
            }

            $manager->persist($salle[$i]);
        }


        $faker = Factory::create('fr-FR');
        $hash = '$2y$13$Li.rLne01AHwc.ituzQ/jejHxxKO.BU4A9Fc9hmCc3.PprUhaJbRa'; // "password"

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
