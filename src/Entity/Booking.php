<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Controller\AvailableBookingController;

#[ApiResource(collectionOperations: [
    'get' => [
        'available' => [
            'denormalization_context' => ['groups' => ['available:write']],
            'path' => '/booking/available',
            'post' => ['method' => 'post'],
            'deserialize' => 'false',
            'validate' => 'false',
            'controller' => AvailableBookingController::class,
            'status' => '200',
        ],
    ],
],
    itemOperations: [
        'get',
        'mercure' => 'true',
    ],
    attributes: ["normalization_context" => ["groups" => ["resa:read"]]],
)]
#[ApiFilter(DateFilter::class, properties: ["bookingDate"])]
#[ApiFilter(SearchFilter::class, properties: ["room" => "exact"])]
/**
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
    // #[ORM\Id, ORM\GeneratedValue]
    // #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;
    // #[ORM\ManyToOne]
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?UserInterface $user;
// #[ORM\ManyToOne]
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read","available:write"})
     */
    private ?Room $room;
    // #[ORM\Column(type: Types::DATETIME)]
    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTimeInterface $createAt;
    // #[ORM\ManyToOne]
    // #[ORM\JoinColumn(nullable: false)]
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Meeting")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read","available:write"})
     */
    private ?Meeting $meeting;
    // #[ORM\Column(type: Types::DATE)]
    /**
     * @ORM\Column(type="date")
     * @Groups({"resa:read","available:write"})
     */
    private ?DateTimeInterface $bookingDate;
    // #[ORM\Column(type: Types::INTEGER, nullable: true)]
    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private ?int $nbPerson;
    // #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private ?string $name;
    // #[ORM\OneToOne]
    // #[ORM\JoinColumn(nullable: true)]
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Paypal", inversedBy="booking", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private ?Paypal $payment;
    // #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex("/^\w+/")
     */
    private ?string $notices;
    // #[ORM\Column(type: Types::STRING)]
    /**
     * @ORM\Column(type="string")
     */
    private ?string $total;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    public function setUser(?UserInterface $user): Booking
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
        return $this->name ?? '';
    }
    // #[ORM\PrePersist]

    /**
     * @ORM\PrePersist
     */
    public function setCreateAt(): self
    {
        $this->createAt = new DateTime();

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

    public function setName(?string $name): Booking
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

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;

        return $this;
    }
}
