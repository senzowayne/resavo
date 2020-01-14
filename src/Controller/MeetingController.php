<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\Meeting;
use App\Entity\DateBlocked;
use App\Entity\Booking;
use App\Repository\MeetingRepository;
use BraintreeHttp\Serializer\Json;
use Doctrine\ORM\EntityManagerInterface;
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
    public function seance(EntityManagerInterface $manager, int $room = 1): JsonResponse
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
     * @param Request                $request
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     * @throws Exception
     * @throws \Exception
     */
    public function verifDispo(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $posts = $request->request;
        $date = $posts->get('date');
        $seance = $posts->get('seance');
        $salle = $posts->get('salle');

        $blockedDate = $manager->getRepository(DateBlocked::class);
        $verifDate = $blockedDate->findOneBy([
          'blockedDate' => new \DateTime($date)
        ]);

        if ($verifDate) {
            return $this->json(['message' => 'Cette date n\'est pas disponible']);
        }

        try {
            $valueSalle = $manager->getRepository(Room::class)->findOneBy([
                'name' => $salle
            ]);

            $valueSeance = $manager->getRepository(Meeting::class)->findOneBy([
            'label' => $seance,
            'room' => $valueSalle
            ]);

            if (null !== $valueSeance->getId()) {
                $resa = $manager->getRepository(Booking::class)->findOneBy([
                  'bookingDate' => new \DateTime($date),
                  'meeting' => $valueSeance->getId(),
                  'room' => $salle
                ]);
            }
        } catch (Exception $e) {
            $e->getMessage();
        }

        if ($resa) {
            $reponse = new JsonResponse([
              'message' => 'Cette réservation est deja prise'
            ]);

            return $reponse;
        }

        $reponse = new JsonResponse([
          'message' => 'Cette réservation est disponible'
        ]);

        return $reponse;
    }
}
