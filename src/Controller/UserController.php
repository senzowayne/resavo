<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Booking;
use App\Form\UserType;
use App\Form\BookingType;
use App\Manager\UserManager;
use App\Manager\BookingManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    private UserManager $userManager;
    private BookingManager $bookingManager;

    public function __construct(UserManager $userManager, BookingManager $bookingManager)
    {
        $this->userManager = $userManager;
        $this->bookingManager = $bookingManager;
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $this->userManager->save($user);

            $this->addFlash(
                'success',
                sprintf(
                    'Félicitations %s votre compte à bien été créer, vous pouvez desormais réservez.',
                    $user->getName()
                )
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/historique", name="historique")
     * @return Response
     */
    public function history(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $data = $this->bookingManager->getLatestBooking($user);

        return $this->render('user/historique.html.twig', compact('data'));
    }

    /**
     * @Route("/edit/{booking}", name="edit", methods={"GET", "POST"})
     */
    public function editForm(Booking $booking, EntityManagerInterface $entityManager, Request $request)
    {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $booking = $form->getData();
            $entityManager->flush();
            return $this->redirectToRoute('historique');
        }

        return $this->render('user/edit.html.twig', ['booking' => $booking, 'form' => $form->createView()]);
    }
}
