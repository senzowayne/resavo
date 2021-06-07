<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\AvailableBookingController;
use App\Controller\MeetingController;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(collectionOperations: [
    'get' => [
        'room' => [
            'denormalization_context' => ['groups' => ['room:read']],
            'path' => '/room/read',
            'get' => ['method' => 'get'],
            'deserialize' => 'false',
            'validate' => 'false',
            'controller' => CheckBookingController::class,
            'status' => '200',
        ],
    ],
],
    itemOperations: ['get'],
    attributes: ["normalization_context" => ["groups" => ["room:read"]]],
)]
/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 */
//#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"meeting:read", "room:read"})
     */
    // #[ORM\Id, ORM\GeneratedValue]
    // #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"room:read"})
     */
    //#[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $name;

    /**
     * @ORM\Column(type="text")
     */
    //#[ORM\Column(type: Types::TEXT)]
    private ?string $description;
    /**
     * @ORM\Column(type="integer")
     */
    //#[ORM\Column(type: Types::INTEGER)]
    private ?int $price;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Booking", mappedBy="room")
     */
    // #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: "room")]
    private Collection $bookings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Meeting", mappedBy="room")
     */
    // #[ORM\OneToMany(targetEntity: Meeting::class, mappedBy: "room")]
    private Collection $meetings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->meetings = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addReservation(Booking $reservation): self
    {
        if (!$this->bookings->contains($reservation)) {
            $this->bookings[] = $reservation;
            $reservation->setRoom($this);
        }

        return $this;
    }

    public function removeReservation(Booking $reservation): self
    {
        if ($this->bookings->contains($reservation)) {
            $this->bookings->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getRoom() === $this) {
                $reservation->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Meeting[]
     */
    public function getMeetings(): Collection
    {
        return $this->meetings;
    }

    public function addMeeting(Meeting $meeting): self
    {
        if (!$this->meetings->contains($meeting)) {
            $this->meetings[] = $meeting;
            $meeting->setRoom($this);
        }

        return $this;
    }

    public function removeSeance(Meeting $meeting): self
    {
        if ($this->meetings->contains($meeting)) {
            $this->meetings->removeElement($meeting);
            // set the owning side to null (unless already changed)
            if ($meeting->getRoom() === $this) {
                $meeting->setRoom(null);
            }
        }

        return $this;
    }
}
