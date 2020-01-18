<?php

namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SentMessage;
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

    final public function mailConfirmation(Reservation $booking): TemplatedEmail
    {
        $user = $booking->getUser();
        //setup transport mail

        $email = (new TemplatedEmail())
            ->from('resa@resavo.fr')
            ->to(new Address($user->getEmail(), $user->getNom().' '.$user->getPrenom()))
            ->subject('Votre réservation')
            ->htmlTemplate('reservation/_confirmNotification.html.twig')
            ->context(['resa' => $booking])
        ;

//        /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
//        $sentEmail = $mailer->send($email);
        $this->mailer->send($email);

        return $email;
    }
}
