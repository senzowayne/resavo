<?php

namespace App\Controller;

use App\Entity\GetOrder;
use App\Entity\CaptureAuthorization;
use App\Entity\Paypal;
use App\Entity\Reservation;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Entity\User;
use App\Entity\DateBlocked;
use App\Form\ReservationType;
use BraintreeHttp\Serializer\Json;
use Exception;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Console\Exception\LogicException;
use App\Controller\CheckReservationController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    private $notif;
    private $check;
    private $manager;
    private $session;

    public function __construct(EntityManagerInterface $manager, NotificationController $notif, CheckReservationController $check, SessionInterface $session)
    {
        $this->notif = $notif;
        $this->check = $check;
        $this->manager = $manager;
        $this->session = $session;
    }

    /**
     * @Route("/reserve", name="new_reservation",  methods={"POST", "GET"})
     * @Route("/reserve/{salle}", name="new_reservation_salle",  methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     * @param Request $request
     */
    public function index(Request $request, $salle = null)
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        $repoDate = $this->manager->getRepository(DateBlocked::class);
        $blocked = $repoDate->myfindAll();
        $paypalClient = "https://www.paypal.com/sdk/js?client-id=" . $this->getParameter('PAYPAL_CLIENT_ID') . "&currency=EUR&debug=false&disable-card=amex&intent=authorize";

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(), 'salle' => $salle, 'blocked' => $blocked, 'client' => $paypalClient
        ]);
    }

    /**
     * @Route("/before-reservation", name="before_reservation")
     */
    public function reservationPage() {

        return $this->render('reservation/reservation.html.twig', [
            'controller_name' => 'Controller', 'reservation' => 'reservation'
        ]);
    }
    
    /**
     * @Route("/resa", name="resa_day")
     */
    public function resaDay()
    {
        $date = new \DateTime();
        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository(Reservation::class);
        $repoSalle = $manager->getRepository(Seance::class);
        $resa = $repo->findBy(['dateReservation' => $date], ['salle' => 'ASC']);
        $seance1 = $repoSalle->findBy(['salle' => 1]);
        $seance2 = $repoSalle->findBy(['salle' => 2]);
        $seance3 = $repoSalle->findBy(['salle' => 3]);
        $datas = [];
        foreach ($resa as $cle => $valeur) {
                array_push($datas, $valeur);
        }
        return $this->render('reservation/resa-day.html.twig', [
            'resa' => $datas, 'seance1' => $seance1, 'seance2'=> $seance2, 'seance3' => $seance3
        ]);
    }


    public function createReservation()
    {
        $date = new \DateTime($_POST['date']);
        if (!$this->check->verifDate($date)) {
            throw new \LogicException("Vous ne pouvez reserver que 2 jours après la date d'aujourd'hui !", 1);
        }
        $date->format('dd-mm-yyyy');
        $salle = $this->check->checkSalleValue($_POST['salle']);

        $seance = $this->manager->getRepository(Seance::class)->findOneBy(['libelle' => htmlspecialchars($_POST['seance'])]);
        $salle = $this->manager->getRepository(Salle::class)->find($salle);

        $reservation = new Reservation();
        $reservation->setRemarques($_POST['remarques'])
                    ->setDateReservation($date)
                    ->setSalle($salle)
                    ->setSeance($seance)
                    ->setNbPersonne($_POST['nbPersonne'])
                    ->setSoirWeek($_POST['weekEnd'])
                    ->setTotal($_POST['total']);

        return $reservation;
    }

    /**
     * @Route("/api-reserve", name="reserve", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function reserve(Request $request)
    {
        $reservation = $this->createReservation();
        $user = $this->getUser();
        $pay = $this->session->get('pay');

        if ($this->check->verifPaiment($pay, $reservation)) {
            $paiement = $this->manager->getRepository(Paypal::class)->find($pay);

            $reservation->setPaiement($paiement);
            $reservation->setUser($user);
            $this->manager->persist($reservation);
            $this->manager->flush();
            $reservation->setNom('resa_' . substr($this->getUser()->getNom(), 0, 3) . '&' . $reservation->getId() . '&' . $this->getUser()->getId());
            $this->manager->persist($reservation);
            $this->manager->flush();
            $this->session->remove('pay');
            $this->session->set('resa', $reservation);

            if ($this->capturePaiement($reservation, $paiement)) {
                $this->notif->mailConfirmation();
                $this->addFlash('success', 'Félicitations votre reservation à bien été enregistrée, un e-mail de confirmation vous a été envoyer sur ' . $this->getUser()->getEmail());
            }

            return $this->json('Réservation ok');
        } else {
            $this->addFlash('danger', 'un problème est survenu pendant la réservation');
        }
        return $this->json('ok');
    }

    /**
     * Reponse de l'API paypal & entré en bdd des informations d'auritsation du paiment
     * @Route("/paypal-transaction-complete", name="pay", methods={"POST", "GET"})
     */
    public function authorizePaiement(Request $request)
    {
        $this->session->remove('pay');
        $data = $request->request->get('authorization');
        $authID = $request->request->get('authorizationID');
        $this->session->set('authorizationID', $authID);

        $data = GetOrder::getOrder($data['id']);
       if ($data['status'] == 'COMPLETED') {
            $paiment = new Paypal();
            $paiment->setPaymentId($data['orderID']);
            $paiment->setPaymentCurrency($data['currency']);
            $paiment->setPaymentAmount($data['value']);
            $paiment->setPaymentDate(new \DateTime());
            $paiment->setPaymentStatus($data['status']);
            $paiment->setPayerEmail($data['mail']);
            $paiment->setUser($this->getUser());
            $paiment->setCapture(0);
            $this->manager->persist($paiment);
            $this->manager->flush();

            $this->session->set('pay', $paiment);

            return $this->json(['success' => 'ok', 'resa' => true]);
        }

        return $this->json(['error' => 'problème de paiment', 'resa' => false,]);
    }

    public function capturePaiement(Reservation $reservation, $paiement)
    {
        try {
            $response = CaptureAuthorization::captureAuth($this->session->get('authorizationID'));
            $captureId = $response->result->id;
            if ("COMPLETED" !== $response->result->status) {
                $this->manager->remove($paiment);
                $this->manager->remove($reservation);
                $this->manager->flush();
                $this->addFlash('danger', 'Un problème d\'approvissionement est survenu');

                return $this->redirectToRoute('before_reservation');
            }
            $paiement->setCapture(1);
            $paiement->setCaptureId($captureId);

            $this->manager->persist($paiement);
            $this->manager->flush();

            return true;
        } catch (Exception $e) {
            $e->getMessage();
            $this->manager->remove($paiment);
            $this->manager->remove($reservation);
            $this->manager->flush();
            $this->addFlash('danger', 'Un problème d\'approvissionement est survenu');

            return false;
        }
    }

    /**
     * @Route("/resume", name="resume")
     * @Security("is_granted('ROLE_USER')")
     */
    public function resume()
    {
        return $this->render('reservation/resume.html.twig');
    }
}
