<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Session\Session;

class NotificationController extends AbstractController
{
    final public function mailConfirmation(MailerInterface $mailer): void
    {
        $session = new Session();
        $resa = $session->get('resa');
        $user = $resa->getUser();
        $date = $resa->getDateReservation();
        //setup transport mail

        $email = (new Email())
            ->from('resa@resavo.fr')
            ->to(new Address($user->getEmail(), $user->getNom().' '.$user->getPrenom()))
            ->subject('Votre réservation')
            ->html('<p>Nous vous confirmons la reservation de votre séance :</p>
<p>Nom:' . $user->getNom() . '</p>
<p>Prénom:' . $user->getPrenom() . '</p>
<p>ID reservation:' . $resa->getNom() . ' </p>
<p>Date: ' . $date->format('d-m-y') . '</p>
<p>Séance: ' . $resa->getSeance() . '</p>
<p>Salle: ' . $resa->getSalle() . '</p>
<p>Nombre de personnes: ' . $resa->getNbPersonne() . '</p>
<p>Votre remarque : ' . $resa->getRemarques() .' </p>
<p>Acompte : ' . $resa->getPaiement()->getPaymentAmount() . $resa->getPaiement()->getPaymentCurrency() .'</p>
<p>Reste à payer (sur place) : '.($resa->getTotal() - $resa->getPaiement()->getPaymentAmount()).
                   $resa->getPaiement()->getPaymentCurrency() . '</p>')
        ;

//        /** @var Symfony\Component\Mailer\SentMessage $sentEmail */
//        $sentEmail = $mailer->send($email);
        $mailer->send($email);
    }
}
