<?php

namespace App\Controller;

use App\Entity\ConfigMerchant;
use App\Manager\BookingManager;
use App\Manager\PaypalManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/reservation")
 */
class BookingController extends AbstractController
{
    private $notification;
    private $manager;
    private $bookingManager;
    private $paypalManager;
    private $session;

    public function __construct(
        EntityManagerInterface $manager,
        NotificationController $notification,
        BookingManager $bookingManager,
        PaypalManager $paypalManager,
        SessionInterface $session
    )
    {
        $this->notification = $notification;
        $this->manager = $manager;
        $this->bookingManager = $bookingManager;
        $this->paypalManager = $paypalManager;
        $this->session = $session;
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

        $configMerchant = $this->manager
                               ->getRepository(ConfigMerchant::class)
                               ->findOneBy([]);

        if (!$configMerchant->getMaintenance()) {

            $payment = $this->paypalManager
                            ->findOnePaiement($this->session->get('pay'));

            $booking->setPayment($payment);
            $booking->setUser($this->getUser());
            $this->bookingManager->save($booking);

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
        $result = $this->bookingManager
                       ->getAllMeetingPerRoom($request->query->get('d'));

        return $this->render('reservation/booking-day.html.twig', [
            'booking' => $result['booking'],
            'meeting1' => $result['meeting1'],
            'meeting2' => $result['meeting2'],
            'meeting3' => $result['meeting3'],
            'date' => $result['date']
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
