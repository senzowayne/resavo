<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;


class AvailableBookingController
{
    private $bookingRepository;
    private $roomRepository;
    private $meetingRepository;

    /**
     * AvailableBookingController constructor.
     * @param $bookingRepository
     * @param $roomRepository
     * @param $meetingRepository
     */
    public function __construct(BookingRepository $bookingRepository, RoomRepository $roomRepository, MeetingRepository $meetingRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
        $this->meetingRepository = $meetingRepository;
    }

    public function __invoke(Request $request): bool
    {
        $posts = json_decode($request->getContent(), true);

        $date = new \DateTime($posts['bookingDate']);
        $room = $this->roomRepository->findOneBy(['name' => $posts['room']]);
        $meeting = $this->meetingRepository->findOneBy(['room' => $room, 'label' => $posts['meeting']]);

        $data = $this->bookingRepository->findOneBy(['room' => $room, 'meeting' => $meeting, 'bookingDate' => $date]);

        return $data !== null ? false : true;
    }

}
