<?php

namespace App\Manager;

use App\Entity\Booking;
use Psr\Log\LoggerInterface;
use App\Repository\RoomRepository;
use App\Repository\BookingRepository;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingManager extends AbstractManager
{
    private const SVC_NAME = '[BookingManager] ::';

    private $manager;
    private $logger;
    private $roomRepo;
    private $meetingRepo;
    private $bookingRepo;

    /**
     * @param EntityManagerInterface $manager
     * @param LoggerInterface $logger
     * @param RoomRepository $roomRepo
     * @param MeetingRepository $meetingRepo
     * @param BookingRepository $bookingRepo
     */
    public function __construct(
        EntityManagerInterface $manager,
        LoggerInterface $logger,
        RoomRepository $roomRepo,
        MeetingRepository $meetingRepo,
        BookingRepository $bookingRepo
    )
    {
        $this->manager = $manager;
        $this->logger = $logger;
        $this->roomRepo = $roomRepo;
        $this->meetingRepo = $meetingRepo;
        $this->bookingRepo = $bookingRepo;
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

        $meeting = $this->meetingRepo->find($meetingId);
        $room = $this->roomRepo->find($roomId);

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

        $booking = $this->bookingRepo->findBy(['bookingDate' => $date], ['room' => 'ASC']);
        $rooms = $this->roomRepo->findAll();

        if (!(count($rooms) >= 3)) {
            throw new \LogicException('you must be have 3 rooms');
        }
        $meeting1 = $this->meetingRepo->findBy(['room' => $rooms[0]]);
        $meeting2 = $this->meetingRepo->findBy(['room' => $rooms[1]]);
        $meeting3 = $this->meetingRepo->findBy(['room' => $rooms[2]]);

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
