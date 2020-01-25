<?php

namespace App\Controller;

use App\Entity\Booking;
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

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    final public function mailConfirmation(Booking $booking): TemplatedEmail
    {
        $user = $booking->getUser();

        $email = (new TemplatedEmail())
            ->from('resa@resavo.fr')
            ->to(new Address($user->getEmail(), $user->getName().' '.$user->getFristName()))
            ->subject('Votre réservation')
            ->htmlTemplate('reservation/_confirmation.html.twig')
            ->context(['resa' => $booking])
        ;

        $this->mailer->send($email);

        return $email;
    }
}
