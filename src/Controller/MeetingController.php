<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Meeting;
use App\Entity\DateBlocked;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeetingController extends AbstractController
{

    #[Route("/reservation/verif/dispo", name: "dispo", methods: ["GET"])]
    public function availabilityCheck(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $date = $request->query->get('date');
        $meeting = $request->query->get('meeting');
        $room = $request->query->get('room');

        $verifyDate = $em->getRepository(DateBlocked::class)
                              ->findOneBy(['blockedDate' => new \DateTime($date)]);

        if ($verifyDate) {
            return $this->json(['message' => 'Cette date n\'est pas disponible']);
        }

            $roomValue = $em->getRepository(Room::class)
                                 ->find($room);

            $meetingValue = $em->getRepository(Meeting::class)
                                    ->find($meeting);

            $booking = $em->getRepository(Booking::class)->findOneBy([
                  'bookingDate' => new \DateTime($date),
                  'meeting' => $meetingValue,
                  'room' => $roomValue
            ]);

        $data = ($booking) ? ['available' => false] : ['available' => true];

        return new JsonResponse($data);
    }
}
