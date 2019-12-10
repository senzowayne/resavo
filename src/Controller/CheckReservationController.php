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

        return ($date < $today) ? false : true;
    }

    /**
     * Verifie que le total est bien le montant attendu
     */
    public function verifTotal($salle, $nbPersonne, $soirWeek, $total): bool
    {
        /**
         * Verif pour la salle 1 si plus de 2 personnes ajout 35 par personnes
        **/
        if (($salle->getId() == 1) && ($nbPersonne > 2)) {
            if ($total != (($salle->getPrix() + (30 * ($nbPersonne - 2))) + $plus) / 2) {
                return false;
            }
        }

        /**
         * Verif pour la salle 2 si entre 3 a 8 personnes rentre dans cette condition
         **/
        if ($salle->getId() == 2 && $nbPersonne > 2 && $nbPersonne <= 8) {
            // Si entre 3 et 5 personnes rentre dans cette condition
            if ($nbPersonne > 2 && $nbPersonne <= 5) {
                //ajout 35€ par personnes
                if ($total != (($salle->getPrix() + (35 * ($nbPersonne - 2))) + $plus) / 2) {
                    return false;
                }
            }
            // Si entre 5 et 8 personnes rentre dans cette condition
            if ($nbPersonne > 5 && $nbPersonne <= 8) {
                //ajout 20€ par personnes
                if ($total != (($salle->getPrix() + (20 * ($nbPersonne - 2)))+ $plus) / 2) {
                    return false;
                }
            }
        }

        /**
         * Verif pour la salle 3 si entre 3 a 8 personnes rentre dans cette condition
         **/
        if ($salle->getId() == 3 && $nbPersonne > 2 && $nbPersonne <= 8) {
            // Si entre 2 et 5 personnes rentre dans cette condition
            if ($nbPersonne > 2 && $nbPersonne <= 5) {
                //ajout 35€ par personnes
                if ($total != (($salle->getPrix() + (35 * ($nbPersonne - 2)))+ $plus) / 2) {
                    return false;
                }
            }
            // Si entre 5 et 7 personnes rentre dans cette condition
            if ($nbPersonne > 5 && $nbPersonne <= 8) {
                //ajout 20€ par personnes
                if ($total != (($salle->getPrix() + (20 * ($nbPersonne - 2)))+ $plus) / 2) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Permet de verifier que le paiment envoyer est bien celui attendu
     * @param Paypal $paiment
     * @param Reservation $reservation
     * @return bool
     */
    public function verifPaiment(Paypal $paiment, Reservation $reservation)
    {
        $salle = $reservation->getSalle();
        $nbPersonne = $reservation->getNbPersonne();
        $soirWeek = $reservation->getSoirWeek();
        $total = $paiment->getPaymentAmount();

        ($soirWeek == 1) ? $plus = 30 : $plus = 0;

        // Si Salle 1 total différent de 38€
        if (($salle->getId() == 1) && !(($salle->getPrix() / 2) + 0.5)) {
            return false;
        }

        // Si Salle 2 total différent de 45€
        if (($salle->getId() == 2) && !($salle->getPrix() / 2)) {
            return false;
        }

        // Si Salle 3 total différent de 45€
        if (($salle->getId() == 3) && !($salle->getPrix() / 2)) {
            return false;
        }

        if (!$this->verifTotal($salle, $nbPersonne, $soirWeek, $total)) {
            throw new \LogicException("Nous avons detecté un problème sur le total à payer !", 1);
        }

        return true;
    }

    public function checkSalleValue($salle)
    {
        if (!is_numeric($salle)) {
            throw new \LogicException('La valeur de la salle doit etre un entier');
        }

        return $salle;
    }
}
