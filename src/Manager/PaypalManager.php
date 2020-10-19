<?php

namespace App\Manager;

use App\Entity\Paypal;
use App\Entity\Booking;
use Psr\Log\LoggerInterface;
use App\Service\Paypal\GetOrder;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Paypal\CaptureAuthorization;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PaypalManager extends AbstractManager
{
    private const SVC_NAME = '[PaypalManager] ::';

    private $em;
    private $logger;
    private $session;
    private $security;

    /**
     * @param EntityManagerInterface $em
     * @param LoggerInterface $logger
     * @param SessionInterface $session
     * @param Security $security
     */
    public function __construct(
        EntityManagerInterface $em,
        LoggerInterface $logger,
        SessionInterface $session,
        Security $security
    )
    {
        $this->em = $em;
        $this->logger = $logger;
        $this->session = $session;
        $this->security = $security;
    }

    public function generateSandboxLink(): string
    {
        $link = "https://www.paypal.com/sdk/js?client-id=%s&currency=EUR&debug=false&disable-card=amex&intent=authorize";
        return sprintf($link, $this->getParameter('CLIENT_ID'));
    }

    public function createPaiement(array $data): void
    {
        $payment = (new Paypal())
            ->setPaymentId($data['orderID'])
            ->setPaymentCurrency($data['currency'])
            ->setPaymentAmount($data['value'])
            ->setPaymentStatus($data['status'])
            ->setPayerEmail($data['mail'])
            ->setUser($this->security->getUser())
            ->setCapture(false);

        $this->em->persist($payment);
        $this->em->flush();
        $this->session->set('pay', $payment->getId());
    }

    public function findOnePaiement(int $id): Paypal
    {
        /** @var Paypal $paypal */
        $paypal = $this->em
                       ->getRepository(Paypal::class)
                       ->find($id);

        if (is_null($paypal)) {
            throw new \RuntimeException('No paiement found');
        }

        return $paypal;
    }

    public function requestAutorize(Request $request): array
    {
        $this->logger->info('======== Procédure de paiement ========');
        $this->session->remove('pay');
        $data = json_decode($request->getContent(), true);
        $this->session->set('authorizationID', $data['authorizationID']);

        $this->logger->info(
            sprintf('%s authorizationID : %s - User e-mail : %s',
                self::SVC_NAME,
                $data['id'],
                $this->security->getUser()->getEmail())
        );

        return GetOrder::getOrder($data['id']);
    }

    /**
     * @param Booking $booking
     * @param Paypal $payment
     * @return bool|RedirectResponse
     */
    public function capturePayment(Booking $booking, Paypal $payment)
    {
        try {
            $this->logger->info(
                sprintf('%s Capture du paiement -- User e-mail : %s',
                    self::SVC_NAME,
                    $this->security->getUser()->getEmail())
            );
            $response = CaptureAuthorization::captureAuth($this->session->get('authorizationID'));
            $this->logger->info(self::SVC_NAME . $response['orderID'] . ' -- ' . $response['status']);

            $captureId = $response['orderID'];

            if ('COMPLETED' !== $response['status'] && 'PENDING' !== $response['status']) {
                $this->logger->error(
                    sprintf('%s Paiement non capturé -- suppression de la réservation User e-mail : %s',
                        self::SVC_NAME,
                        $this->security->getUser()->getEmail())
                );
                $this->em->remove($payment);
                $this->em->remove($booking);
                $this->em->flush();
                $this->session->remove('authorizationID');
                $this->addFlash('danger', 'Un problème d\'approvisionnement est survenu');

                return $this->redirectToRoute('before_reservation');
            }
            $this->setCapturePaiement($payment, $captureId);

            return true;
        } catch (\Exception $e) {
            $e->getMessage();
            $this->em->remove($payment);
            $this->em->remove($booking);
            $this->em->flush();
            $this->addFlash('danger', 'Un problème d\'approvisionnement est survenu');

            return false;
        }
    }

    public function setCapturePaiement(Paypal $payment, string $captureId): void
    {
        $payment->setCapture(true);
        $payment->setCaptureId($captureId);

        $this->em->persist($payment);
        $this->em->flush();
    }
}
