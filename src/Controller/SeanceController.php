<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Entity\DateBlocked;
use App\Entity\Reservation;
use App\Repository\SeanceRepository;
use BraintreeHttp\Serializer\Json;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SeanceController extends AbstractController
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

        $repo = $manager->getRepository(Seance::class);
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
            $valueSeance = $manager->getRepository(Seance::class)->findOneBy([
            'libelle' => $seance,
            'salle' => $salle
            ]);

            if (null !== $valueSeance->getId()) {
                $resa = $manager->getRepository(Reservation::class)->findOneBy([
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
