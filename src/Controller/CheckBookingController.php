<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Paypal;

class CheckBookingController
{
    /**
     * Vérifie que l'on ne reserve pas dans le passé ni dans les 2 jours suivant la date du jour
     * @param \DateTime $date
     * @return bool
     */
    public function verifyDate(\DateTime $date): bool
    {

        $date = $date->format('Y-m-d');
        $today = (new \DateTime('now'))->format('Y-m-d');

        return $date > $today;
    }

    private function checkIsWeekEnd(string $date): bool
    {
        $timestamp = strtotime($date);
        if (!$timestamp) {
            throw new \RuntimeException('The date given is wrong');
        }

        $indexDay = date('w', $timestamp);
        return ($indexDay == 0 || $indexDay == 6);
    }

    /**
     * Permet de vérifier que le paiement envoyer est bien celui attendu
     *
     * @param Paypal  $payment
     * @param Booking $booking
     *
     * @return bool
     */
    public function verifyPayment(Paypal $payment, Booking $booking)
    {
        //TODO A REDÉFINIR
        return true;
    }
}
