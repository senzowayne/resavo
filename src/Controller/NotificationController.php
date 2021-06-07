<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class NotificationController extends AbstractController
{
    private const SVC_NAME = '[NOTIFICATION_CONTROLLER] :: ';

    private MailerInterface $mailer;

    private LoggerInterface $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    final public function mailConfirmation(Booking $booking): TemplatedEmail
    {
        /** @var User $user */
        $user = $booking->getUser();
        $userMail = $user->getEmail();

        $email = (new TemplatedEmail())
            ->from('resa@resavo.fr')
            ->to(new Address($userMail, $user->getName() . ' ' . $user->getFirstName()))
            ->subject('Votre réservation')
            ->htmlTemplate('reservation/_confirmation.html.twig')
            ->context(['resa' => $booking]);

        $this->logger->info(self::SVC_NAME . ' SEND MAIL ' . $userMail);
        $this->mailer->send($email);
        $this->logger->info(self::SVC_NAME . ' SEND MAIL OK' . $userMail);

        return $email;
    }

    final public function resetPassEmail(User $user, string $url): TemplatedEmail
    {
        /** @var User $user */
        $userMail = $user->getEmail();

        $email = (new TemplatedEmail())
            ->from('resa@resavo.fr')
            ->to(new Address($userMail, $user->getName() . ' ' . $user->getFirstName()))
            ->subject('Réinitialisation mot de passe')
            ->htmlTemplate('security/_reset_email.html.twig')
            ->context(['resetUrl' => $url]);

        $this->logger->info(self::SVC_NAME . ' SEND MAIL ' . $userMail);
        $this->mailer->send($email);
        $this->logger->info(self::SVC_NAME . ' SEND MAIL OK' . $userMail);

        return $email;
    }
}
