<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
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
 * @UniqueEntity(fields={"dateReservation", "seance", "salle"},
 * message= "Cette réservation est pas disponible choisissez une autre séance ou autre date")
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salle", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read"})
     */
    private $salle;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"resa:read"})
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seance")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"resa:read"})
     */
    private $seance;

    /**
     * @ORM\Column(type="date")
     * @Groups({"resa:read"})
     */
    private $dateReservation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"resa:read"})
     */
    private $nbPersonne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"resa:read"})
     */
    private $nom;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Paypal", inversedBy="reservation", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $paiement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Regex("/^\w+/")
     * @Groups({"resa:read"})
     */
    private $Remarques;


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

    public function setUser(?User $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getSalle(): ?Salle
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): self
    {
        $this->salle = $salle;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreateAt(): self
    {
        $this->createAt =  new \DateTime();

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): self
    {
        $this->seance = $seance;

        return $this;
    }

    public function getDateReservation(): ?\DateTimeInterface
    {
        return $this->dateReservation;
    }

    public function setDateReservation(\DateTimeInterface $dateReservation): self
    {
        $this->dateReservation = $dateReservation;

        return $this;
    }

    public function getNbPersonne(): ?int
    {
        return $this->nbPersonne;
    }

    public function setNbPersonne(?int $nbPersonne): self
    {
        $this->nbPersonne = $nbPersonne;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPaiement(): ?Paypal
    {
        return $this->paiement;
    }

    public function setPaiement(Paypal $paiement): self
    {
        $this->paiement = $paiement;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->Remarques;
    }

    public function setRemarques(?string $Remarques): self
    {
        $this->Remarques = $Remarques;

        return $this;
    }
    
    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total): void
    {
        $this->total = $total;
    }
}
