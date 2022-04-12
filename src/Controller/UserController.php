<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Booking;
use App\Form\UserType;
use App\Form\BookingType;
use App\Manager\UserManager;
use App\Manager\BookingManager;
use App\Controller\CheckBookingController;
use App\Entity\Meeting;
use App\Entity\Room;
use App\Repository\MeetingRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/user_edit_booking/{booking}", name="user_edit_booking", methods={"GET", "POST"})
     * @return Response
     */
    public function editForm(Booking $booking, EntityManagerInterface $entityManager, Request $request): Response
    {
            $this->denyAccessUnlessGranted('booking_edit', $booking);
            $bookingDate = $booking->getBookingDate();

            $roomId = $booking->getRoom()->getId();
            
            $form = $this->createForm(BookingType::class, $booking, ['booking_date' => $bookingDate, 'room_id' => $roomId]);
            $form->handleRequest($request);

        if(CheckBookingController::verifyDate($bookingDate)) {
                if($form->isSubmitted() && $form->isValid()) {   
                    $entityManager->flush();
                    $this->addFlash('success', "Votre réservation a bien été modifié !");    
                        return $this->redirectToRoute('historique');
                }
        } else {
                $this->addFlash('danger', "Veuillez saisir une date supérieur à aujourd'hui");    
        }
            return $this->render('user/edit.html.twig', ['booking' => $booking, 'form' => $form->createView()]);
    }

    public function existsAction(Request $request): JsonResponse
	{
    	if(isset($request->request))
    	{
            // Get data from ajax
            $data = $request->request->get('booking');

            // Check if a Folder with the given name already exists
			$booking = $this
			    ->getDoctrine()
			    ->getManager()
			    ->getRepository('Booking')
	
		    ->findOneBy($data);
			    
            if ($booking === null)
            {
            	// Booking does not exist
            	return new JsonResponse(array(
            		'status' => 'OK',
            		'message' => 0),
            	200);
            }
            else
            {
            	// Booking exists
            	return new JsonResponse(array(
            		'status' => 'OK',
            		'message' => 1),
            	200);
            }
        }

        // If we reach this point, it means that something went wrong
        return new JsonResponse(array(
            'status' => 'Error',
            'message' => 'Error'), 400);

    }
}
