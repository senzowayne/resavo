<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Controller\NotificationController;
use App\Entity\Paypal;
use App\Entity\Reservation;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;;
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
            ->setNom('Resavo')
            ->setPrenom('Jean')
            ->setEmail('test@resavo.fr')
        ;

        $meeting = (new Seance())->setLibelle('14h 16h');

        $room = (new Salle())->setNom('Salle Miami');

        $payment = (new Paypal())->setUser($user);
        $payment->setPaymentCurrency('Eur');
        $payment->setPaymentAmount(125.00);

        $booking = (new Reservation())
            ->setUser($user)
            ->setDateReservation(new DateTime('2020/01/10'))
            ->setSeance($meeting)
            ->setSalle($room)
            ->setNbPersonne(3)
            ->setRemarques(null)
            ->setPaiement($payment)
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
