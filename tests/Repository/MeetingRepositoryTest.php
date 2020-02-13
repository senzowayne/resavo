<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Meeting;
use App\Entity\Room;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MeetingRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    final protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    final public function testSearchByLabelAndRoom(): void
    {
        // Arrange
        $room = (new Room())
            ->setId(1)
            ->setName("Salle Bora-Bora")
            ->setDescription("Salle 1")
            ->setPrice(90)
        ;
        $meetingLabel = "12h30 - 14h30";

        // Act
        $meeting = $this->entityManager
            ->getRepository(Meeting::class)
            ->findOneBy(['label' => $meetingLabel, 'room' => $room->getId()]);

        // Assert
        $this->assertSame(2, $meeting->getId());
        $this->assertSame("12h30 - 14h30", $meeting->getLabel());
        $this->assertSame(1, $meeting->getRoom()->getId());
        $this->assertSame("Salle Bora-Bora", $meeting->getRoom()->getName());
    }
}
