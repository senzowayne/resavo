<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{


    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);
            $em->persist($user);
            $em->flush();

            $repo = $em->getRepository(User::class);
            $user = $repo->findOneBy([
                'name' => $user->getName(),
                 'firstName' => $user->getFirstName(),
                 'email' => $user->getEmail()]
            );


            $this->addFlash(
                'success',
                'Félicitations ' . $user->getName() . ' votre compte à bien été créer, vous pouvez desormais reservez.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     * @Security(
     *     "is_granted('ROLE_USER') and user == user.getEmail()",
     *     message="Vous n'avez pas le droit d'accéder à cette partie du site sans etre authentifier"
     * )
     */
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     * @Security(
     *     "is_granted('ROLE_USER') and user == user.getEmail()",
     *     message="Vous n'avez pas le droit d'accéder à cette partie du site"
     * )
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/historique", name="historique")
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function history(EntityManagerInterface $manager)
    {
        $user = $this->getUser();

        $repo = $manager->getRepository(Booking::class);
        $data = $repo->findBy(['user' => $user], ['bookingDate' => 'DESC'], 10);

        return $this->render('user/historique.html.twig', ['data' => $data ]);
    }


}
