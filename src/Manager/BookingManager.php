<?php

namespace App\Manager;

use App\Entity\Booking;
use App\Entity\Meeting;
use App\Entity\User;
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

    private EntityManagerInterface $manager;
    private LoggerInterface $logger;
    private RoomRepository $roomRepo;
    private MeetingRepository $meetingRepo;
    private BookingRepository $bookingRepo;

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
        /** @var User $user */
        $user = $this->getUser();
        $this->logger->info(
            sprintf('%s Création de la réservation -- %s', self::SVC_NAME, $user->getEmail())
        );

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $date = new \DateTime($data['date']);
        $roomId = $data['room'];
        $meetingId = $data['meeting'];

        /** @var ?Meeting $meeting */
        $meeting = $this->meetingRepo->find($meetingId);
        $room = $this->roomRepo->find($roomId);

        if (null === $meeting) {
            throw new NotFoundHttpException('The meeting does not exist');
        }
        if (null === $room) {
            throw new NotFoundHttpException('The room does not exist');
        }
        $this->logger->info(
            sprintf('%s Détails: [date: %s - salle: %s - séance: %s',
                self::SVC_NAME,
                $date->format('d-m-Y'),
                $room->getName(),
                $meeting->getLabel())
        );

        return (new Booking())
            ->setNotices($data['notices'])
            ->setBookingDate($date)
            ->setRoom($room)
            ->setMeeting($meeting)
            ->setNbPerson($data['nbPerson'])
            ->setTotal($data['total']);
    }

    public function getAllMeetingPerRoom(?string $stringDate): array
    {
        try {
            $date = new \DateTime($stringDate);
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

    public function getLatestBooking(User $user, int $limit = 10): array
    {
       return $this->bookingRepo->findBy(['user' => $user], ['bookingDate' => 'DESC'], $limit);
    }

    public function save(Booking $booking): void
    {
        /** @var User $user */
        $user = $this->getUser();
        $this->logger->info(
            sprintf('%s Enregistrement de la réservation -- %s', self::SVC_NAME, $user->getEmail())
        );
        $this->manager->persist($booking);
        $this->manager->flush();
    }
}
