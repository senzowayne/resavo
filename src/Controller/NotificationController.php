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
        $booking = $session->get('booking');
        $user = $booking->getUser();
        $date = $booking->getBookingDate();
        //setup transport mail

        $message = (new Swift_Message('Votre reservation'))
        //->setFrom(['xxx' => 'Booking'])
          ->setTo([$user->getEmail() => $user->getName() . ' ' . $user->getFirstName()])
          ->setBody('
      Nous vous confirmons la reservation de votre séance :

      <br>
      Nom:' . $user->getName() . '<br>
      Prénom:' . $user->getFirstName() . '<br>
      ID reservation:' . $booking->getName() . ' <br>
      Date: ' . $date->format('d-m-y') . '<br>
      Séance: ' . $booking->getMeeting() . '<br>
      Room: ' . $booking->getRoom() . '<br>
      Nombre de personnes: ' . $booking->getNbPerson() . '<br>
      Votre remarque : ' . $booking->getNotices() .' <br>
      Acompte: ' . $booking->getPayement()->getPaymentAmount() . $booking->getPayement()->getPaymentCurrency() . '<br>
      Reste à payer (sur place): ' . ($booking->getTotal() - $booking->getPayement()->getPaymentAmount()) . $booking->getPayement()->getPaymentCurrency() . '<br>


          ', 'text/html');

        $mailer->send($message);
    }
}
