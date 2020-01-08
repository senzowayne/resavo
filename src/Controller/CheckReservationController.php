<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Paypal;

class CheckReservationController
{
    /**
     * Verifie que l'on ne reserve pas dans le passé ni dans les 2 jours suivant la date du jour
     */
    public function verifDate(\DateTime $date): bool
    {
        $today = new \DateTime('now');
        $today->modify('+1 day');

        return $date >= $today;
    }

    /**
     * Verifie que le total est bien le montant attendu
     */
    public function verifTotal($salle, $nbPersonne, $total)
    {
        //TODO A REDEFINIR
    }

    /**
     * Permet de verifier que le paiment envoyer est bien celui attendu
     * @param Paypal $paiment
     * @param Reservation $reservation
     * @return bool
     */
    public function verifPaiment(Paypal $paiment, Reservation $reservation)
    {
        //TODO A REDEFINIR
        return true;
    }
}
