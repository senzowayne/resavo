<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\NotificationController;
use App\Entity\Booking;
use App\Entity\Meeting;
use App\Entity\Paypal;
use App\Entity\Room;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class NotificationControllerTest extends TestCase
{
    final public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    final public function testSendConfirmationMessage(): void
    {
        // Arrange
        $symfonyMailer = $this->createMock(MailerInterface::class);
        $symfonyMailer->expects($this->once())
            ->method('send');

        $user = (new User())
            ->setName('Resavo')
            ->setFirstName('Jean')
            ->setEmail('test@resavo.fr')
        ;

        $meeting = (new Meeting())->setLabel('14h 16h');

        $room = (new Room())->setName('Salle Miami');

        $payment = (new Paypal())->setUser($user);
        $payment->setPaymentCurrency('Eur');
        $payment->setPaymentAmount(125.00);

        $booking = (new Booking())
            ->setUser($user)
            ->setBookingDate(new DateTime('2020/01/10'))
            ->setMeeting($meeting)
            ->setRoom($room)
            ->setNbPerson(3)
            ->setName(null)
            ->setPayment($payment)
        ;
        $booking->setTotal(3 * 125.00);

        // Act
        $mailer = new NotificationController($symfonyMailer);
        $email = $mailer->mailConfirmation($booking);

        // Assert
        $this->assertSame('Votre réservation', $email->getSubject());$this->assertCount(1, $email->getTo());
        /** @var Address[] $addresses */
        $addresses = $email->getTo();
        $this->assertInstanceOf(Address::class, $addresses[0]);
        $this->assertSame('Resavo Jean', $addresses[0]->getName());
        $this->assertSame('test@resavo.fr', $addresses[0]->getAddress());
    }
}
