<?php

namespace App\Security\Voter;

use App\Entity\Booking;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BookingVoter extends Voter
{
    const BOOKING_EDIT = 'booking_edit';
    const BOOKING_VIEW = 'booking_view';

    protected function supports(string $attribute, $booking): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::BOOKING_EDIT, self::BOOKING_VIEW])
            && $booking instanceof \App\Entity\Booking;
    }

    protected function voteOnAttribute(string $attribute, $booking, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        //On verifie si la résa a un user
        if(null === $booking->getUser()) return false;

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::BOOKING_EDIT :
                return $this->canEdit($booking, $user);
                break;
            case self::BOOKING_VIEW :
                return $this->canView($booking, $user);
                break;
        }

        return false;
    }

    private function canEdit(Booking $booking, User $user)
    {
        return $user === $booking->getUser();
    }

    private function canView(Booking $booking, User $user)
    {
        return $user === $booking->getUser();
    }
}
