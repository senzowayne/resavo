<?php

namespace App\Controller;

use App\Repository\BookingRepository;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\Request;

class AvailableBookingController
{
    private BookingRepository $bookingRepository;
    private RoomRepository $roomRepository;
    private MeetingRepository $meetingRepository;

    public function __construct(
        BookingRepository $bookingRepository,
        RoomRepository $roomRepository,
        MeetingRepository $meetingRepository
    )
    {
        $this->bookingRepository = $bookingRepository;
        $this->roomRepository = $roomRepository;
        $this->meetingRepository = $meetingRepository;
    }

    public function __invoke(Request $request): bool
    {
        $posts = json_decode($request->getContent(), true);

        $date = new \DateTime($posts['bookingDate']);
        $room = $this->roomRepository->find($posts['room']);

        $meeting = $this->meetingRepository
                        ->findOneBy(['room' => $room, 'id' => $posts['meeting']]);

        $data = $this->bookingRepository
                     ->findOneBy(['room' => $room, 'meeting' => $meeting, 'bookingDate' => $date]);

        return $data === null;
    }

}
