<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\ConfigMerchant;
use App\Entity\User;
use App\Manager\BookingManager;
use App\Manager\PaypalManager;
use App\Message\NotificationMessage;
use App\Service\GoogleCalendar;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\GoogleCalendarService;
use DateTime;

/**
 * @Route("/reservation")
 */
class BookingController extends AbstractController
{
    private EntityManagerInterface $manager;
    private BookingManager $bookingManager;
    private PaypalManager $paypalManager;
    private SessionInterface $session;

    private const SVC_NAME = '[NOTIFICATION_CONTROLLER] :: ';

    public function __construct(
        EntityManagerInterface $manager,
        BookingManager $bookingManager,
        PaypalManager $paypalManager,
        SessionInterface $session
    ) {
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
    public function index(GoogleCalendarService $googleCalendarService): Response
    {
        $book = $this->getDoctrine()->getManager()->getRepository(Booking::class)->find(2);
        $tomorrow  = mktime(0, 0, 0, date("m"), date("d") + 1, date("Y"));
        $lastmonth = mktime(0, 0, 0, date("m") - 1, date("d"),   date("Y"));
        $googleCalendarService->addEvent("booking", new DateTime(), new DateTime(), null, "test", "test", "Paris", null, true);

        dd($googleCalendarService->listCalendars());
        $calendar =  $googleCalendarService->getCalendarService()->calendarList;



        $paypalClient = $this->paypalManager->generateSandboxLink();


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
     */
    public function authorizePayment(Request $request): JsonResponse
    {
        $data = $this->paypalManager->requestAutorize($request);

        if ($data['status'] === 'COMPLETED') {
            $this->paypalManager->createPaiement($data);

            return $this->json(['success' => 'ok', 'booking' => true]);
        }
        return $this->json(['error' => 'problème de paiement', 'booking' => false,]);
    }

    /**
     * @Route("/api-reserve", name="reserve", methods={"POST"})
     */
    public function reserve(Request $request, MessageBusInterface $bus): JsonResponse
    {
        $booking = $this->bookingManager->createBooking($request);
        /** @var ?ConfigMerchant $configMerchant */
        $configMerchant = $this->manager
            ->getRepository(ConfigMerchant::class)
            ->findOneBy([]);

        if (is_null($configMerchant)) {
            throw new \LogicException('the configMerchant not found');
        }

        if (!$configMerchant->getMaintenance()) {
            $payment = $this->paypalManager






                ->findOnePaiement($this->session->get('pay'));

            $booking->setPayment($payment);
            $this->bookingManager->save($booking);

            $this->session->set('bookingId', $booking->getId());

            if ($this->paypalManager->capturePayment($booking, $payment)) {
                $bus->dispatch(new NotificationMessage($booking->getId()));

                /** @var User $user */
                $user = $this->getUser();
                $this->addFlash(
                    'success',
                    sprintf(
                        'Félicitations votre réservation à bien été enregistrée, un e-mail de confirmation vous a été envoyer sur %s',
                        $user->getEmail()
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
        if (is_null($this->session->get('bookingId'))) {
            return $this->redirectToRoute('new_reservation');
        }

        $booking = $this->manager->getRepository(Booking::class)
            ->find($this->session->get('bookingId'));

        return $this->render('reservation/resume.html.twig', compact('booking'));
    }









    /**
     * @Route("/test", name="test")
     * 
     */
    public function testEmailCatch(NotificationController $notification): Response
    {

        $book = $this->getDoctrine()->getManager()->getRepository(Booking::class)->find(2);

        $email = $notification->mailConfirmation($book);

        return $this->redirectToRoute('new_reservation');
    }
}
