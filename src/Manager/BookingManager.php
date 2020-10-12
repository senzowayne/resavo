<?php

namespace App\Manager;

use App\Entity\Room;
use App\Entity\Booking;
use App\Entity\Meeting;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingManager extends AbstractManager
{
    private const SVC_NAME = '[BookingManager ::';

    private $manager;
    private $logger;

    /**
     * @param EntityManagerInterface $manager
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManagerInterface $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function createBooking(Request $request): Booking
    {
        $this->logger->info(
            sprintf('%s Création de la réservation -- %s', self::SVC_NAME, $this->getUser()->getEmail())
        );
        $this->logger->info(
            sprintf('%s Détails: [date: %s - salle: %s - séance: %s',
                self::SVC_NAME,
                $request->request->get('date'),
                $request->request->get('room'),
                $request->request->get('meeting'))
        );

        $data = json_decode($request->getContent(), true);

        $date = new \DateTime($data['date']);
        $date->format('dd-mm-yyyy');
        $roomId = $data['room'];
        $meetingId = $data['meeting'];

        $meeting = $this->manager->getRepository(Meeting::class)->find($meetingId);
        $room = $this->manager->getRepository(Room::class)->find($roomId);

        if (null === $meeting) {
            throw new NotFoundHttpException('The meeting does not exist');
        }
        if (null === $room) {
            throw new NotFoundHttpException('The room does not exist');
        }

        return (new Booking())
            ->setNotices($data['notices'])
            ->setBookingDate($date)
            ->setRoom($room)
            ->setMeeting($meeting)
            ->setNbPerson($data['nbPerson'])
            ->setTotal($data['total']);
    }

    public function getAllMeetingPerRoom($date): array
    {
        try {
            $date = new \DateTime($date);
        } catch (\Exception $e) {
            $date = new \DateTime();
        }

        $bookingRepo = $this->manager->getRepository(Booking::class);
        $meetingRepo = $this->manager->getRepository(Meeting::class);
        $roomRepo = $this->manager->getRepository(Room::class);
        $booking = $bookingRepo->findBy(['bookingDate' => $date], ['room' => 'ASC']);
        $rooms = $roomRepo->findAll();

        if (!(count($rooms) >= 3)) {
            throw new \LogicException('you must be have 3 rooms');
        }
        $meeting1 = $meetingRepo->findBy(['room' => $rooms[0]]);
        $meeting2 = $meetingRepo->findBy(['room' => $rooms[1]]);
        $meeting3 = $meetingRepo->findBy(['room' => $rooms[2]]);

        $data = [];
        foreach ($booking as $key => $value) {
            $data[] = $value;
        }

        return [
            'booking' => $data,
            'meeting1' => $meeting1,
            'meeting2' => $meeting2,
            'meeting3' => $meeting3,
            'date' => $date
        ];
    }

    public function save(Booking $booking): void
    {
        $this->logger->info(
            sprintf('%s Enregistrement de la réservation -- %s', self::SVC_NAME, $this->getUser()->getEmail())
        );
        $this->manager->persist($booking);
        $this->manager->flush();
    }
}
