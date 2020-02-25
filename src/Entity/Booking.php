<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={"normalization_context"={"groups"={"resa:read"}}},
 *     collectionOperations={
 *         "get",
 *         "post"
 *     },
 *     itemOperations={"get"}
 * )
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields={"bookingDate", "meeting", "room"},
 * message= "Cette réservation est pas disponible choisissez une autre séance ou autre date")
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read"})
     */
    private $room;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"resa:read"})
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Meeting")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read"})
     */
    private $meeting;

    /**
     * @ORM\Column(type="date")
     * @Groups({"resa:read"})
     */
    private $bookingDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"resa:read"})
     */
    private $nbPerson;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"resa:read"})
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Paypal", inversedBy="booking", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $payment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex("/^\w+/")
     * @Groups({"resa:read"})
     */
    private $notices;


    /**
     * @ORM\Column(type="string")
     * @Groups({"resa:read"})
     */
    private $total;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getCreateAt(): ?DateTimeInterface
    {
        return $this->createAt;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * @ORM\PrePersist
     *
     * @throws Exception
     */
    public function setCreateAt(): self
    {
        $this->createAt =  new DateTime();

        return $this;
    }

    public function getMeeting(): ?Meeting
    {
        return $this->meeting;
    }

    public function setMeeting(?Meeting $meeting): self
    {
        $this->meeting = $meeting;

        return $this;
    }

    public function getBookingDate(): ?DateTimeInterface
    {
        return $this->bookingDate;
    }

    public function setBookingDate(DateTimeInterface $bookingDate): self
    {
        $this->bookingDate = $bookingDate;

        return $this;
    }

    public function getNbPerson(): ?int
    {
        return $this->nbPerson;
    }

    public function setNbPerson(?int $nbPerson): self
    {
        $this->nbPerson = $nbPerson;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getPayment(): ?Paypal
    {
        return $this->payment;
    }

    public function setPayment(Paypal $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getNotices(): ?string
    {
        return $this->notices;
    }

    public function setNotices(?string $notices): self
    {
        $this->notices = $notices;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total): self
    {
        $this->total = $total;

        return $this;
    }
}
