<?php

namespace App\Controller;

use App\Service\Paypal\GetOrder;
use App\Service\Paypal\CaptureAuthorization;
use App\Entity\Paypal;
use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Meeting;
use App\Entity\DateBlocked;
use App\Form\BookingType;
use DateTime;
use Exception;
use LogicException;
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
    private $session;
    private $logger;

    public function __construct(
        EntityManagerInterface $manager,
        NotificationController $notification,
        CheckBookingController $check,
        SessionInterface $session,
        LoggerInterface $logger)
    {
        $this->notification = $notification;
        $this->check = $check;
        $this->manager = $manager;
        $this->session = $session;
        $this->logger = $logger;
    }

    /**
     * @Route("/reserve", name="new_reservation",  methods={"POST", "GET"})
     * @Route("/reserve/{salle}", name="new_reservation_salle",  methods={"POST", "GET"})
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request, Room $room = null): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        $repoDate = $this->manager->getRepository(DateBlocked::class);
        $blocked = $repoDate->myfindAll();
        $paypalClient = 'https://www.paypal.com/sdk/js?client-id=' .
            $this->getParameter('CLIENT_ID') .
            '&currency=EUR&debug=false&disable-card=amex&intent=authorize';

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'room' => $room,
            'blocked' => $blocked,
            'client' => $paypalClient
        ]);
    }

    /**
     * @Route("/before-reservation", name="before_reservation")
     */
    public function reservationPage(): Response
    {

        return $this->render('reservation/booking.html.twig', [
            'controller_name' => 'Controller',
            'reservation' => 'reservation'
        ]);
    }

    /**
     * @Route("/resa", name="resa_day")
     */
    public function bookingDay(): Response
    {
        $date = new DateTime();
        $manager = $this->getDoctrine()->getManager();
        $repo = $manager->getRepository(Booking::class);
        $roomRepository = $manager->getRepository(Meeting::class);
        $booking = $repo->findBy(['bookingDate' => $date], ['room' => 'ASC']);
        $meeting1 = $roomRepository->findBy(['room' => 1]);
        $meeting2 = $roomRepository->findBy(['room' => 2]);
        $meeting3 = $roomRepository->findBy(['room' => 3]);
        $datas = [];
        foreach ($booking as $key => $value) {
            $datas[] = $value;
        }
        return $this->render('reservation/booking-day.html.twig', [
            'booking' => $datas,
            'meeting1' => $meeting1,
            'meeting2' => $meeting2,
            'meeting3' => $meeting3
        ]);
    }

    private function createBooking(Request $request): Booking
    {
        $date = new DateTime($request->request->get('date'));
        if (!$this->check->verifyDate($date)) {
            throw new LogicException("Vous ne pouvez réserver que 2 jours après la date d'aujourd'hui !", 1);
        }
        $date->format('dd-mm-yyyy');
        $room = $request->request->get('room');

        $meeting = $this->manager
            ->getRepository(Meeting::class)
            ->findOneBy(['label' => htmlspecialchars($request->request->get('meeting'))]);
        $room = $this->manager->getRepository(Room::class)->findOneBy(['name' => $room]);

        return (new Booking())
            ->setNotices($request->request->get('notices'))
            ->setBookingDate($date)
            ->setRoom($room)
            ->setMeeting($meeting)
            ->setNbPerson($request->request->get('nbPerson'))
            ->setTotal($request->request->get('total'));
    }

    /**
     * @Route("/api-reserve", name="reserve", methods={"POST"})
     * @throws Exception
     */
    public function reserve(Request $request): JsonResponse
    {
        $booking = $this->createBooking($request);
        $user = $this->getUser();
        $pay = $this->session->get('pay');
        $this->logger->info('Création de la réservation -- ' . $this->getUser()->getEmail());

        if ($this->check->verifyPayment($pay, $booking)) {
            $payment = $this->manager->getRepository(Paypal::class)->find($pay);
            $this->logger->info(
                'Details => ' . $request->request->get('date') . '
                ' . $request->request->get('room') . '
                ' . $request->request->get('meeting'));
            
            $booking->setPayment($payment);
            $booking->setUser($user);
            $this->manager->persist($booking);
            $this->manager->flush();
            $this->logger->info(' Verification du paiement && Enregistrement de la réservation -- ' . $this->getUser()->getEmail());
            $booking->setName(
                'booking_' . substr($this->getUser()->getName(), 0, 3) .
                '&' . $booking->getId() . '&' . $this->getUser()->getId()
            );
            $this->manager->persist($booking);
            $this->manager->flush();
            $this->session->remove('pay');
            $this->session->set('booking', $booking);

            if ($this->capturePayment($booking, $payment)) {
                $this->notification->mailConfirmation($booking);
                $this->addFlash(
                    'success',
                    'Félicitations votre reservation à bien été enregistrée, un e-mail de confirmation vous a été envoyer sur ' . $this->getUser()->getEmail()
                );
            }

            return $this->json('Réservation ok');
        } else {
            $this->addFlash('danger', 'un problème est survenu pendant la réservation');
        }
        return $this->json('ok');
    }

    /**
     * Réponse de l'API PayPal & entré en bdd des informations d'auritsation du paiement
     * @Route("/paypal-transaction-complete", name="pay", methods={"POST", "GET"})
     * @throws Exception
     */
    public function authorizePayment(Request $request): JsonResponse
    {
        $this->logger->info('======== Procédure de paiement ========');
        $this->session->remove('pay');
        $data = $request->request->get('authorization');
        $authID = $request->request->get('authorizationID');
        $this->session->set('authorizationID', $authID);

        if (null == $authID) {
            $this->logger->error('Aucune authorizationID envoyé');
            throw new Exception('Aucune authorizationID envoyé');
        }
        $this->logger->info('authorizationID : ' . $data['id'] . ' User e-mail : ' . $this->getUser()->getEmail());

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
     * @return bool|RedirectResponse
     */
    public function capturePayment(Booking $booking, Paypal $payment)
    {
        try {
            $this->logger->info('Capture du paiement -- User e-mail : ' . $this->getUser()->getEmail());
            $response = CaptureAuthorization::captureAuth($this->session->get('authorizationID'));
            $captureId = $response->result->id;
            if ('COMPLETED' !== $response->result->status) {
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
