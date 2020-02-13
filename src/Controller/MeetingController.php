<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Meeting;
use App\Entity\DateBlocked;
use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MeetingController extends AbstractController
{
    /**
     * Récupérer les seances d'une salle
     * @Route("/reservation/seance/horaire", methods={"POST"})
     */
    public function meeting(EntityManagerInterface $manager, int $room = 1): Response
    {
        if (isset($_POST['room'])) {
            $room = htmlentities($_POST['room']);
        }

        $meetings = $manager
            ->getRepository(Meeting::class)
            ->findBy(['room' => $room]);

        $datas = [];
        foreach ($meetings as $data) {
            $datas[$data->getId()] = $data->getLabel();
        }

        return $this->json($datas);
    }

    /**
     * @Route("/reservation/verif/dispo", name="dispo", methods={"POST"})
     * @return JsonResponse
     * @throws Exception
     */
    public function AvailabilityCheck(Request $request, EntityManagerInterface $manager): Response
    {
        $date = $request->request->get('date');
        $meeting = $request->request->get('meeting');
        $room = $request->request->get('room');

        $verifyDate = $manager
            ->getRepository(DateBlocked::class)
            ->findOneBy([
              'blockedDate' => new \DateTime($date)
            ]);

        if ($verifyDate) {
            return $this->json(['message' => 'Cette date n\'est pas disponible']);
        }

        try {
            $roomValue = $manager
                ->getRepository(Room::class)
                ->findOneBy(['name' => $room]);

            $meetingValue = $manager
                ->getRepository(Meeting::class)
                ->findOneBy(['label' => $meeting, 'room' => $roomValue->getId()]);

            if (null !== $meetingValue->getId()) {
                $booking = $manager->getRepository(Booking::class)->findOneBy([
                  'bookingDate' => new \DateTime($date),
                  'meeting' => $meetingValue->getId(),
                  'room' => $roomValue->getId()
                ]);
            }
        } catch (Exception $e) {
            $e->getMessage();
        }

        if ($booking) {
            return new JsonResponse([
              'message' => 'Cette réservation est déjà prise'
            ]);
        }
        return new JsonResponse([
          'message' => 'Cette réservation est disponible'
        ]);
    }
}
