<?php

namespace App\Controller;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class NotificationController extends AbstractController
{
    public function mailConfirmation()
    {
        $session = new Session();
        $resa = $session->get('resa');
        $user = $resa->getUser();
        $date = $resa->getDateReservation();
        //setup transport mail

        $message = (new Swift_Message('Votre reservation'))
        //->setFrom(['xxx' => 'Reservation'])
          ->setTo([$user->getEmail() => $user->getNom() . ' ' . $user->getPrenom()])
          ->setBody('
      Nous vous confirmons la reservation de votre séance :

      <br>
      Nom:' . $user->getNom() . '<br>
      Prénom:' . $user->getPrenom() . '<br>
      ID reservation:' . $resa->getNom() . ' <br>
      Date: ' . $date->format('d-m-y') . '<br>
      Séance: ' . $resa->getSeance() . '<br>
      Salle: ' . $resa->getSalle() . '<br>
      Nombre de personnes: ' . $resa->getNbPersonne() . '<br>
      Votre remarque : ' . $resa->getRemarques() .' <br>
      Acompte: ' . $resa->getPaiement()->getPaymentAmount() . $resa->getPaiement()->getPaymentCurrency() . '<br>
      Reste à payer (sur place): ' . ($resa->getTotal() - $resa->getPaiement()->getPaymentAmount()) . $resa->getPaiement()->getPaymentCurrency() . '<br>


          ', 'text/html');

        $mailer->send($message);
    }
}
