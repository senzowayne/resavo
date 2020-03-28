<?php

namespace App\Controller;

use App\Entity\Booking;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class NotificationController extends AbstractController
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    final public function mailConfirmation(Booking $booking): TemplatedEmail
    {
        $user = $booking->getUser();

        $email = (new TemplatedEmail())
            ->from('resa@resavo.fr')
            ->to(new Address($user->getEmail(), $user->getName().' '.$user->getFirstName()))
            ->subject('Votre réservation')
            ->htmlTemplate('reservation/_confirmation.html.twig')
            ->context(['resa' => $booking]);

        $this->logger->info(' SEND MAIL ' . $this->getUser()->getEmail());
        $this->mailer->send($email);
        $this->logger->info(' SEND MAIL OK' . $this->getUser()->getEmail());


        return $email;
    }
}
