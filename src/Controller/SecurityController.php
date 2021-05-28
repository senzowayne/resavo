<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\UserManager;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Repository\UserRepository;
use App\Controller\TokenGeneratorInterface;
use App\Form\ResetPassType;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;

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

    /**
     * @Route("/user/connect/", name="connect_social")
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponse
     */
    public function connect(ClientRegistry $clientRegistry): RedirectResponse
    {
        /** @var GoogleClient $client */
        $client = $clientRegistry->getClient('google');

        return $client->redirect(['profile', 'email']);
    }

    /**
     * @Route("/user/connect/google/check", name="connect_google_check")
     */
    public function connectCheckAction(): RedirectResponse
    {
        return $this->redirectToRoute('your_homepage_route');
    }


    /**
     * Permet de modifier le mot de passe
     * @Route("/forgotten-password", name="forgotten_password")
     */
    public function forgotPassword(Request $request, UserRepository $users, UserPasswordEncoderInterface $encoder, NotificationController $notification): Response
    {

        $form = $this->createForm(ResetPassType::class);

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            dd($data);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/forgotten_password.html.twig', ['emailForm' => $form->createView()]);

        #return $this->redirectToRoute('app_login');


        /*

 // On récupère les données
            $data = $form->getData();

            dd($data);

            // On cherche un utilisateur ayant cet e-mail
            $user = $users->findOneByEmail($data['email']);

            dd($user);
            if ($user === null) {
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');

                return $this->redirectToRoute('app_login');
            }


            $token = 'iibbbbbiiibibibi';

            dd($user->getFirstName);
            try {
                $user->setResetToken($token);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }


            // On génère l'URL de réinitialisation de mot de passe
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);


            $subject = 'Réinitialisation mot de Passe';
            # $body = "Bonjour,<br><br>Une demande de réinitialisation de mot de passe a été effectuée pour le site Nouvelle-Techno.fr. Veuillez cliquer sur le lien suivant : " . $url,
            'text/html';

            $email = $notification->sendEmail($user, $subject, 'Test');
            $this->addFlash('message', 'E-mail de réinitialisation du mot de passe envoyé !');





        $user = $booking->getUser();
        $userMail = $user->getEmail();
        $email = (new TemplatedEmail())
            ->from('resa@resavo.fr')
            ->to(new Address($userMail, $user->getName() . ' ' . $user->getFirstName()))
            ->subject('Votre réservation')
            ->htmlTemplate('reservation/_confirmation.html.twig')
            ->context(['resa' => $booking]);
            //->html($this->render('reservation/_confirmation.html.twig', ['resa' => $booking])); @TODO : à tester


        $this->logger->info(self::SVC_NAME . ' SEND MAIL ' . $userMail);
        $this->mailer->send($email);
        $this->logger->info(self::SVC_NAME . ' SEND MAIL OK' . $userMail);

        return $email;
        */
    }
}
