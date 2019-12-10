<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout() {

    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/user/update-password", name="update_password")
     * @param Request $request
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager) {
        $newPassword = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $newPassword);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if  (!password_verify($newPassword->getOldPassword(), $user->getHash())){
                $form->get('oldPassword')->addError(new FormError('Le mot de passe que vous avez tapé n\'est pas votre mot de passe actuel'));
            } else {
                $new = $newPassword->getNewPasswordUpdate();
                $hash = $encoder->encodePassword($user, $new);

                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifier !');
                return $this->redirectToRoute('new_reservation');
            }

        }
        return $this->render('user/update_password.html.twig', ['form' => $form->createView()]);
    }
}
