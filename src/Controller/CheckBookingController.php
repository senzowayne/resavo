<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Paypal;
use DateTime;

class CheckBookingController
{
    /**
     * Verifie que l'on ne reserve pas dans le passé ni dans les 2 jours suivant la date du jour
     */
    public function verifyDate(DateTime $date): bool
    {
        $today = new DateTime('now');
        $today->modify('+1 day');

        return ($date < $today) ? false : true;
    }

    /**
     * Verifie que le total est bien le montant attendu
     */
    public function verifyTotal($room, $nbPerson, $total)
    {
        //TODO A REDEFINIR
    }

    /**
     * Permet de verifier que le paiment envoyer est bien celui attendu
     *
     * @param Paypal  $payment
     * @param Booking $booking
     *
     * @return bool
     */
    public function verifyPayment(Paypal $payment, Booking $booking)
    {
        //TODO A REDEFINIR
        return true;
    }
}
