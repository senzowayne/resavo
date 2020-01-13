<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="paiements")
 * @ORM\Entity(repositoryClass="App\Repository\PaypalRepository")
 */
class Paypal
{
    /**
      * @ORM\Id()
      * @ORM\GeneratedValue()
      * @ORM\Column(type="integer")
      */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $payment_id;

    /**
     * @ORM\Column(type="text")
     */
    protected $payment_status;

    /**
     * @ORM\Column(type="float")
     */
    protected $payment_amount;

    /**
     * @ORM\Column(type="text")
     */
    protected $payment_currency;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $payment_date;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $payer_email;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="paiements", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="Booking", mappedBy="paiement", cascade={"persist", "remove"})
     */
    private $booking;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $capture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $captureId;

    public function __toString()
    {
        return $this->payment_amount . $this->payment_currency /*. ' mail: ' . $this->payer_email*/;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getPaymentId()
    {
        return $this->payment_id;
    }

    /**
     * @param mixed $payment_id
     */
    public function setPaymentId($payment_id): self
    {
        $this->payment_id = $payment_id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentStatus()
    {
        return $this->payment_status;
    }

    /**
     * @param mixed $payment_status
     */
    public function setPaymentStatus($payment_status): self
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

    /**
     * @param mixed $payment_amount
     */
    public function setPaymentAmount($payment_amount): self
    {
        $this->payment_amount = $payment_amount;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentCurrency()
    {
        return $this->payment_currency;
    }

    /**
     * @param mixed $payment_currency
     */
    public function setPaymentCurrency($payment_currency): self
    {
        $this->payment_currency = $payment_currency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentDate()
    {
        return $this->payment_date;
    }

    /**
     * @ORM\PrePersist
     * @throws Exception
     */
    public function setPaymentDate(): void
    {
        $this->payment_date = new DateTime();
    }

    /**
     * @return mixed
     */
    public function getPayerEmail()
    {
        return $this->payer_email;
    }

    /**
     * @param mixed $payer_email
     */
    public function setPayerEmail($payer_email): void
    {
        $this->payer_email = $payer_email;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
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
