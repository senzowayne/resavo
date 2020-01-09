<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="paiements", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Reservation", mappedBy="paiement", cascade={"persist", "remove"})
     */
    private $reservation;

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
    public function setPaymentId($payment_id): void
    {
        $this->payment_id = $payment_id;
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
    public function setPaymentStatus($payment_status): void
    {
        $this->payment_status = $payment_status;
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
    public function setPaymentAmount($payment_amount): void
    {
        $this->payment_amount = $payment_amount;
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
    public function setPaymentCurrency($payment_currency): void
    {
        $this->payment_currency = $payment_currency;
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
     * @param \DateTimeInterface $createAt
     * @return Reservation
     * @throws \Exception
     */
    public function setPaymentDate(): void
    {
        $this->payment_date = new \DateTime();
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

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(Reservation $reservation): self
    {
        $this->reservation = $reservation;

        // set the owning side of the relation if necessary
        if ($this !== $reservation->getPaiement()) {
            $reservation->setPaiement($this);
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
