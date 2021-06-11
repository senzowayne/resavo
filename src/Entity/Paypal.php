<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * <<ORM\HasLifecycleCallbacks()>>
 * <<ORM\Table("paiements")>>
 * <<ORM\Entity("App\Repository\PaypalRepository")>>git
 */
class Paypal
{
    /**
      * <<ORM\Id()>>
      * <<ORM\GeneratedValue()>>
      * <<ORM\Column("integer")>>
      */
    private ?int $id = null;

    /**
     * <<ORM\Column("string", 255)>>
     */
    protected ?string $payment_id;

    /**
     * <<ORM\Column("text")>>
     */
    protected ?string $payment_status;

    /**
     * <<ORM\Column("float")>>
     */
    protected ?float $payment_amount;

    /**
     * <<ORM\Column("text")>>
     */
    protected ?string $payment_currency;

    /**
     * <<ORM\Column("datetime")>>
     */
    protected ?DateTime $payment_date;

    /**
     * <<ORM\Column("text", true)>>
     */
    protected ?string $payer_email;

    /**
     * <<ORM\ManyToOne("App\Entity\User", "payments")>>
     * <<ORM\JoinColumn(false)>>
     */
    private ?UserInterface $user;

    /**
     * <<ORM\OneToOne("App\Entity\Booking", "payment")>>
     */
    private ?Booking $booking;

    /**
     * <<ORM\Column("boolean", true)>>
     */
    private ?bool $capture;

    /**
     * <<ORM\Column("string", 255, true)>>
     */
    private ?string $captureId;

    public function __toString()
    {
        return $this->payment_amount . $this->payment_currency /*. ' mail: ' . $this->payer_email*/;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPaymentId(): ?string
    {
        return $this->payment_id;
    }

    public function setPaymentId(string $payment_id): self
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->payment_status;
    }

    public function setPaymentStatus(string $payment_status): self
    {
        $this->payment_status = $payment_status;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentAmount()
    {
        return $this->payment_amount;
    }

    public function setPaymentAmount(float $payment_amount): self
    {
        $this->payment_amount = $payment_amount;

        return $this;
    }

    public function getPaymentCurrency(): ?string
    {
        return $this->payment_currency;
    }

    public function setPaymentCurrency(string $payment_currency): self
    {
        $this->payment_currency = $payment_currency;

        return $this;
    }

    public function getPaymentDate(): ?DateTime
    {
        return $this->payment_date;
    }

    /**
     * <<ORM\PrePersist>>
     * @throws Exception
     */
    public function setPaymentDate(): self
    {
        $this->payment_date = new DateTime();

        return $this;
    }

    public function getPayerEmail(): ?string
    {
        return $this->payer_email;
    }

    public function setPayerEmail(string $payer_email): self
    {
        $this->payer_email = $payer_email;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBooking(): ?Booking
    {
        return $this->booking;
    }

    public function setBooking(Booking $booking): self
    {
        $this->booking = $booking;

        // set the owning side of the relation if necessary
        if ($this !== $booking->getPayment()) {
            $booking->setPayment($this);
        }

        return $this;
    }

    public function getCapture(): ?bool
    {
        return $this->capture;
    }

    public function setCapture(?bool $capture): self
    {
        $this->capture = $capture;

        return $this;
    }

    public function getCaptureId(): ?string
    {
        return $this->captureId;
    }

    public function setCaptureId(?string $captureId): self
    {
        $this->captureId = $captureId;

        return $this;
    }
}
