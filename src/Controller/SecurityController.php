<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    private UserManager $userManager;

    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/deconnexion", name="logout")
     */
    public function logout(): void
    {
    }

    /**
     * Permet de modifier le mot de passe
     * @Route("/user/update-password", name="update_password")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $newPassword = new PasswordUpdate();

        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class, $newPassword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!password_verify($newPassword->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')
                    ->addError(
                        new FormError(
                            'Le mot de passe que vous avez tapé n\'est pas votre mot de passe actuel'
                        )
                    );
            } else {
                $new = $newPassword->getNewPasswordUpdate();
                $hash = $encoder->encodePassword($user, $new);

                $user->setHash($hash);
                $this->userManager->save($user);

                $this->addFlash('success', 'Votre mot de passe a bien été modifier !');
                return $this->redirectToRoute('new_reservation');
            }

        }
        return $this->render('user/update_password.html.twig', ['form' => $form->createView()]);
    }
}
