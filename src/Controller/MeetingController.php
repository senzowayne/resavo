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
     * Recuperer les seances d'une salle
     * @Route("/reservation/seance/horaire", methods={"POST"})
     */
    public function seance(EntityManagerInterface $manager, int $room = 1): Response
    {
        if (isset($_POST['room'])) {
            $room = htmlentities($_POST['room']);
        }

        $repo = $manager->getRepository(Meeting::class);
        $seance = $repo->findBy(['room' => $room]);

        $datas = [];
        foreach ($seance as $data) {
            $datas[$data->getId()] = $data->getLabel();
        }

        return $this->json($datas);
    }

    /**
     * @Route("/reservation/verif/dispo", name="dispo", methods={"POST"})
     * @return JsonResponse
     * @throws Exception
     */
    public function verifDispo(Request $request, EntityManagerInterface $manager): Response
    {
        $posts = $request->request;
        $date = $posts->get('date');
        $meeting = $posts->get('meeting');
        $room = $posts->get('room');

        $blockedDate = $manager->getRepository(DateBlocked::class);
        $verifyDate = $blockedDate->findOneBy([
          'blockedDate' => new \DateTime($date)
        ]);

        if ($verifyDate) {
            return $this->json(['message' => 'Cette date n\'est pas disponible']);
        }

        try {
            $roomValue = $manager->getRepository(Room::class)->findOneBy([
                'name' => $room
            ]);

            $meetingValue = $manager->getRepository(Meeting::class)->findOneBy([
            'label' => $meeting,
            'room' => $roomValue
            ]);

            if (null !== $meetingValue->getId()) {
                $resa = $manager->getRepository(Booking::class)->findOneBy([
                  'bookingDate' => new \DateTime($date),
                  'meeting' => $meetingValue->getId(),
                  'room' => $room
                ]);
            }
        } catch (Exception $e) {
            $e->getMessage();
        }

        if ($resa) {
            return new JsonResponse([
              'message' => 'Cette réservation est deja prise'
            ]);
        }
        return new JsonResponse([
          'message' => 'Cette réservation est disponible'
        ]);
    }
}
