<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Room;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RoomRepositoryTest extends KernelTestCase
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

    final public function testSearchByName(): void
    {
        // Arrange
        $roomName = "Salle Bora-Bora";

        // Act
        $room = $this->entityManager
            ->getRepository(Room::class)
            ->findOneBy(['name' => $roomName]);

        // Assert
        $this->assertSame(1, $room->getId());
    }
}
