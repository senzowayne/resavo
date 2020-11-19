<?php

namespace App\MessageHandler;

use App\Entity\Booking;
use App\Message\NotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use App\Controller\NotificationController;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class NotificationHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $manager;
    private NotificationController $notification;

    public function __construct(EntityManagerInterface $manager, NotificationController $notification)
    {
        $this->manager = $manager;
        $this->notification = $notification;
    }

    public function __invoke(NotificationMessage $message)
    {
        /** @var Booking $booking */
       $booking = $this->manager->find(Booking::class, $message->getId());
       $this->notification->mailConfirmation($booking);
    }
}
