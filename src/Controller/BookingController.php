<?php

namespace App\Controller;

use App\Entity\ConfigMerchant;
use App\Manager\BookingManager;
use App\Service\Paypal\GetOrder;
use App\Service\Paypal\CaptureAuthorization;
use App\Entity\Paypal;
use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Meeting;
use Exception;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
    private $session;
    private $logger;

    public function __construct(
        EntityManagerInterface $manager,
        NotificationController $notification,
        CheckBookingController $check,
        BookingManager $bookingManager,
        SessionInterface $session,
        LoggerInterface $logger)
    {
        $this->notification = $notification;
        $this->check = $check;
        $this->manager = $manager;
        $this->bookingManager = $bookingManager;
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
        $link = "https://www.paypal.com/sdk/js?client-id=%s&currency=EUR&debug=false&disable-card=amex&intent=authorize";
        $paypalClient = sprintf($link, $this->getParameter('CLIENT_ID'));

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

        $em = $this->getDoctrine()->getManager();
        $bookingRepo = $em->getRepository(Booking::class);
        $meetingRepo = $em->getRepository(Meeting::class);
        $roomRepo = $em->getRepository(Room::class);
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
     * @Route("/api-reserve", name="reserve", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function reserve(Request $request): JsonResponse
    {
        $booking = $this->bookingManager->createBooking($request);
        $user = $this->getUser();
        $pay = $this->session->get('pay');
        $this->logger->info(
            sprintf('Création de la réservation -- %s',
                $user->getEmail()
            )
        );
        $configMerchantRepo = $this->manager->getRepository(ConfigMerchant::class);
        $configMerchant = $configMerchantRepo->findOneBy([]);

        $this->check->verifyDate($booking->getBookingDate());

        if ($this->check->verifyPayment($pay, $booking) && $configMerchant->getMaintenance() === false) {
            $payment = $this->manager->getRepository(Paypal::class)->find($pay);

            $this->logger->info(
                sprintf('Détails: [date: %s - salle: %s - séance: %s',
                        $request->request->get('date'),
                        $request->request->get('room'),
                        $request->request->get('meeting')
                )
            );

            $booking->setPayment($payment);
            $booking->setUser($user);
            $this->manager->persist($booking);
            $this->manager->flush();
            $this->logger->info(
                sprintf('Vérification du paiement && Enregistrement de la réservation -- %s',
                                $this->getUser()->getEmail()
                )
            );
            $this->session->remove('pay');
            $this->session->set('booking', $booking);

            if ($this->capturePayment($booking, $payment)) {
                //  $this->notification->mailConfirmation($booking);
                $this->addFlash(
                    'success',
                    sprintf('Félicitations votre reservation à bien été enregistrée, un e-mail de confirmation vous a été envoyer sur %s',
                                    $this->getUser()->getEmail()
                    )
                );
            }
            $json = [
                'msg' => 'Réservation ok',
                'error' => null
            ];
            return $this->json($json);
        }
        $msg = 'Un problème est survenu pendant la réservation, veuillez nous contacter ou réessayer plus tard.';
        $this->addFlash('danger', $msg);
        return $this->json($msg);
    }

    /**
     * Réponse de l'API PayPal & entré en bdd des informations d'autorisation du paiement
     * @Route("/paypal-transaction-complete", name="pay", methods={"POST", "GET"})
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function authorizePayment(Request $request): JsonResponse
    {
        $this->logger->info('======== Procédure de paiement ========');
        $this->session->remove('pay');
        $data = $request->getContent();
        $data = json_decode($data, true);
        $this->session->set('authorizationID', $data['authorizationID']);

        $this->logger->info(
            sprintf('authorizationID : %s - User e-mail : %s',
                $data['id'],
                $this->getUser()->getEmail())
        );

        $data = GetOrder::getOrder($data['id']);
        if ($data['status'] === 'COMPLETED') {
            $payment = (new Paypal())
                        ->setPaymentId($data['orderID'])
                        ->setPaymentCurrency($data['currency'])
                        ->setPaymentAmount($data['value'])
                        ->setPaymentDate()
                        ->setPaymentStatus($data['status'])
                        ->setPayerEmail($data['mail'])
                        ->setUser($this->getUser())
                        ->setCapture(0);

            $this->manager->persist($payment);
            $this->manager->flush();

            $this->session->set('pay', $payment);

            return $this->json(['success' => 'ok', 'booking' => true]);
        }

        return $this->json(['error' => 'problème de paiement', 'booking' => false,]);
    }

    /**
     * @param Booking $booking
     * @param Paypal $payment
     * @return bool|RedirectResponse
     */
    public function capturePayment(Booking $booking, Paypal $payment)
    {
        try {
            $this->logger->info('Capture du paiement -- User e-mail : ' . $this->getUser()->getEmail());
            $response = CaptureAuthorization::captureAuth($this->session->get('authorizationID'), true);

            $captureId = $response['orderID'];
            $this->logger->info($response['orderID'] . ' -- ' . $response['status']);

            if ('COMPLETED' !== $response['status'] && 'PENDING' !== $response['status']) {
                $this->manager->remove($payment);
                $this->manager->remove($booking);
                $this->manager->flush();
                $this->logger->error('Paiement non capturé -- suppression de la réservation User e-mail : ' . $this->getUser()->getEmail());
                $this->addFlash('danger', 'Un problème d\'approvisionnement est survenu');

                return $this->redirectToRoute('before_reservation');
            }
            $payment->setCapture(1);
            $payment->setCaptureId($captureId);

            $this->manager->persist($payment);
            $this->manager->flush();

            return true;
        } catch (Exception $e) {
            $e->getMessage();
            $this->manager->remove($payment);
            $this->manager->remove($booking);
            $this->manager->flush();
            $this->addFlash('danger', 'Un problème d\'approvisionnement est survenu');

            return false;
        }
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
