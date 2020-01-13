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
    public function seance(int $salle = 1, EntityManagerInterface $manager): JsonResponse
    {
        if (isset($_POST['salle'])) {
            $salle = htmlentities($_POST['salle']);
        }

        $repo = $manager->getRepository(Meeting::class);
        $seance = $repo->findBy(['salle' => $salle]);

        $datas = [];
        foreach ($seance as $data) {
            $datas[$data->getId()] = $data->getLibelle();
        }

        return $this->json($datas);
    }

    /**
     * @Route("/reservation/verif/dispo", name="dispo", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return JsonResponse
     * @throws Exception
     */
    public function verifDispo(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $posts = $request->request;
        $date = $posts->get('date');
        $seance = $posts->get('seance');
        $salle = $posts->get('salle');

        $dateBloqued = $manager->getRepository(DateBlocked::class);
        $verifDate = $dateBloqued->findOneBy([
          'dateBlocked' => new \DateTime($date)
        ]);

        if ($verifDate) {
            return $this->json(['message' => 'Cette date n\'est pas disponible']);
        }

        try {
            $valueSalle = $manager->getRepository(Room::class)->findOneBy([
                'nom' => $salle
            ]);

            $valueSeance = $manager->getRepository(Meeting::class)->findOneBy([
            'libelle' => $seance,
            'salle' => $valueSalle
            ]);

            if (null !== $valueSeance->getId()) {
                $resa = $manager->getRepository(Booking::class)->findOneBy([
                  'dateReservation' => new \DateTime($date),
                  'seance' => $valueSeance->getId(),
                  'salle' => $salle
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
