<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\Room;
use App\Entity\Meeting;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $room = [];
        $labelMeetings = ['10h - 12h', '12h30 - 14h30', '15h - 17h', '17h30 - 19h30', '20h - 22h'];
        $e = 0;
        $roomName = ['Salle Bora-Bora', 'Salle Miami', 'Salle Phuket'];

        for ($i = 1; $i <= 3; $i++) {
            $room[$i] = (new Room())
                ->setName($roomName[$i -1])
                ->setPrice(90)
                ->setDescription('Salle ' . $i)
            ;

            for ($x = 0; $x <= 4; $x++) {
                if ($e >= 5) {
                    $e = 0;
                }
                $meeting[$x] = new Meeting();
                $meeting[$x]->setLabel($labelMeetings[$e]);
                $room[$i]->addMeeting($meeting[$x]);
                $e++;
                $manager->persist($meeting[$x]);
            }

            $manager->persist($room[$i]);
        }

        $faker = Factory::create('fr-FR');
        $hash = '$2y$13$Li.rLne01AHwc.ituzQ/jejHxxKO.BU4A9Fc9hmCc3.PprUhaJbRa'; // "password"

        $admin = (new User())
            ->setName('resavo')
            ->setFirstName('Admin')
            ->setEmail('admin@resavo.fr')
            ->setAvatar($faker->imageUrl($width = 50, $height = 50))
            ->setHash($hash);

        $adminRole = (new Role())
            ->setTitle('ROLE_ADMIN')
            ->addUser($admin)
        ;

        $manager->persist($admin);
        $manager->persist($adminRole);

        $manager->flush();
    }
}
