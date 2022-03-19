<?php

namespace App\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class AdminController extends EasyAdminController
{
    protected function prePersistUserEntity(User $user): void
    {
        $encodedPassword = $this->encodePassword($user, $user->getHash());
        $user->setHash($encodedPassword);
    }

    protected function preUpdateUserEntity(User $user): void
    {
        if (!$user->getHash()) {
            return;
        }
        $encodedPassword = $this->encodePassword($user, $user->getHash());
        $user->setHash($encodedPassword);
    }

    private function encodePassword(User $user, string $hash): string
    {
        /** @var EncoderFactoryInterface $passwordEncoderFactory * */
        $passwordEncoderFactory = $this->get('security.encoder_factory');

        $encoder = $passwordEncoderFactory->getEncoder($user);
        return $encoder->encodePassword($hash, $user->getSalt());
    }
}