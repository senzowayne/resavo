<?php

namespace App\Controller;

use App\Entity\ConfigMerchant;
use App\Manager\BookingManager;
use App\Manager\PaypalManager;
use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Meeting;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/reservation")
 */
class BookingController extends AbstractController
{
    private $notification;
    private $check;
    private $manager;
    private $bookingManager;
    private $paypalManager;
    private $session;
    private $logger;

    public function __construct(
        EntityManagerInterface $manager,
        NotificationController $notification,
        CheckBookingController $check,
        BookingManager $bookingManager,
        PaypalManager $paypalManager,
        SessionInterface $session,
        LoggerInterface $logger)
    {
        $this->notification = $notification;
        $this->check = $check;
        $this->manager = $manager;
        $this->bookingManager = $bookingManager;
        $this->paypalManager = $paypalManager;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @Route("/reserve", name="new_reservation",  methods={"POST", "GET"})
     * @Route("/reserve/{salle}", name="new_reservation_salle",  methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $paypalClient = $this->paypalManager->generateSandbox();

        return $this->render('reservation/index.html.twig', ['client' => $paypalClient]);
    }

    /**
     * @Route("/before-reservation", name="before_reservation")
     */
    public function reservationPage(): Response
    {
        return $this->render('reservation/booking.html.twig', ['reservation' => 'reservation']);
    }

    /**
     * Réponse de l'API PayPal & entré en bdd des informations d'autorisation du paiement
     * @Route("/paypal-transaction-complete", name="pay", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function authorizePayment(Request $request): JsonResponse
    {
        $data = $this->paypalManager->requestAutorize($request->getContent());

        if ($data['status'] === 'COMPLETED') {
            $this->paypalManager->createPaiement($data);

            return $this->json(['success' => 'ok', 'booking' => true]);
        }
        return $this->json(['error' => 'problème de paiement', 'booking' => false,]);
    }

    /**
     * @Route("/api-reserve", name="reserve", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function reserve(Request $request): JsonResponse
    {
        $booking = $this->bookingManager->createBooking($request);
        $user = $this->getUser();
        $pay = $this->session->get('pay');
        $this->logger->info(sprintf('Création de la réservation -- %s', $user->getEmail()));
        $configMerchantRepo = $this->manager->getRepository(ConfigMerchant::class);
        $configMerchant = $configMerchantRepo->findOneBy([]);

        $this->check->verifyDate($booking->getBookingDate());

        if ($configMerchant->getMaintenance() === false) {

            $payment = $this->paypalManager->findOnePaiement($pay);

            $this->logger->info(
                sprintf('Détails: [date: %s - salle: %s - séance: %s',
                        $request->request->get('date'),
                        $request->request->get('room'),
                        $request->request->get('meeting'))
            );

            $booking->setPayment($payment);
            $booking->setUser($user);
            $this->bookingManager->save($booking);
            $this->logger->info(
                sprintf('Vérification du paiement && Enregistrement de la réservation -- %s', $this->getUser()->getEmail())
            );
            $this->session->remove('pay');
            $this->session->set('booking', $booking);

            if ($this->paypalManager->capturePayment($booking, $payment)) {
                //  $this->notification->mailConfirmation($booking);
                $this->addFlash(
                    'success',
                    sprintf('Félicitations votre reservation à bien été enregistrée, un e-mail de confirmation vous a été envoyer sur %s',
                                    $this->getUser()->getEmail()
                    )
                );
            }

            return $this->json([
                        'msg' => 'Réservation ok',
                        'error' => ''
            ]);
        }
        $msg = 'Un problème est survenu pendant la réservation, veuillez nous contacter ou réessayer plus tard.';
        $this->addFlash('danger', $msg);
        return $this->json($msg);
    }


    /**
     * @Route("/resa", name="resa_day")
     * @param Request $request
     * @return Response
     */
    public function bookingDay(Request $request): Response
    {
        try {
            $date = new \DateTime($request->query->get('d'));
        } catch (\Exception $e) {
            $date = new \DateTime();
        }

        $bookingRepo = $this->manager->getRepository(Booking::class);
        $meetingRepo = $this->manager->getRepository(Meeting::class);
        $roomRepo = $this->manager->getRepository(Room::class);
        $booking = $bookingRepo->findBy(['bookingDate' => $date], ['room' => 'ASC']);
        $rooms = $roomRepo->findAll();

        if (!(count($rooms) >= 3)) {
            return $this->render('reservation/booking-day.html.twig', compact('date'));
        }
        $meeting1 = $meetingRepo->findBy(['room' => $rooms[0]]);
        $meeting2 = $meetingRepo->findBy(['room' => $rooms[1]]);
        $meeting3 = $meetingRepo->findBy(['room' => $rooms[2]]);
        $data = [];
        foreach ($booking as $key => $value) {
            $data[] = $value;
        }

        return $this->render('reservation/booking-day.html.twig', [
            'booking' => $data,
            'meeting1' => $meeting1,
            'meeting2' => $meeting2,
            'meeting3' => $meeting3,
            'date' => $date
        ]);
    }

    /**
     * @Route("/resume", name="resume")
     * @Security("is_granted('ROLE_USER')")
     */
    public function resume(): Response
    {
        return $this->render('reservation/resume.html.twig');
    }
}
