<?php
/**
 * Created by IntelliJ IDEA.
 * User: senzowayne
 * Date: 2019-05-29
 * Time: 00:16
 */

namespace App\Controller;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use App\Entity\User;


class AdminController extends EasyAdminController
{
    protected function prePersistUserEntity(User $user)
    {
        $encodedPassword = $this->encodePassword($user, $user->getHash());
        $user->setPassword($encodedPassword);
    }

    protected function preUpdateUserEntity(User $user)
    {
        if (!$user->getHash()) {
            return;
        }
        $encodedPassword = $this->encodePassword($user, $user->getHash());
        $user->setHash($encodedPassword);
    }

    private function encodePassword($user, $hash)
    {
        $passwordEncoderFactory = $this->get('security.encoder_factory');
        $encoder = $passwordEncoderFactory->getEncoder($user);
        return $encoder->encodePassword($hash, $user->getSalt());
    }
}