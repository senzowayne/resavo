<?php

namespace App\Repository;

use App\Entity\Booking;
use App\Entity\Meeting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

class MeetingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meeting::class);
    }

    /**
     * Retourne les ids des séances déjà prise
     * @param int $room
     * @param string $date
     * @return array|int|string
     * @throws \Exception
     */
    public function meetingBlocked(int $room, string $date)
    {
        return $this->_em->createQueryBuilder()
            ->select('m.id')
            ->from(Booking::class, 'b')
            ->leftJoin(Meeting::class, 'm', Join::WITH, 'b.meeting = m')
            ->where('b.room = :room')
            ->andWhere('b.bookingDate = :date')
            ->setParameter('room', $room)
            ->setParameter('date', new \DateTime($date))
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * Retourne uniquement les séances disponible
     * @param int $room
     * @param array $booking
     * @return int|mixed|string
     */
    public function meetingAvailable(int $room, array $booking)
    {
        $query = $this->createQueryBuilder('m')
            ->addSelect('m')
            ->addSelect("CASE WHEN m.id IN(:booking) 
            THEN 'blocked'
            ELSE 'available' END as status")
            ->setParameter('booking', $booking)
            ->where('m.room = :room')
            ->andWhere('m.isActive = true')
            ->setParameter('room', $room);

        return $query->getQuery()->getResult();
    }
}
