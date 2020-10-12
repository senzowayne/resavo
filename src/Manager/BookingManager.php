<?php


namespace App\Manager;


use App\Entity\Booking;
use App\Entity\Meeting;
use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookingManager
{
    private $manager;

    /**
     * BookingManager constructor.
     * @param $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }
    
    public function createBooking(Request $request): Booking
    {
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

    public function save(Booking $booking): void
    {
        $this->manager->persist($booking);
        $this->manager->flush();
    }
}
