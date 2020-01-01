<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints\DateTime;

/**
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
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salle", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $salle;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Seance")
     * @ORM\JoinColumn(nullable=false)
     */
    private $seance;

    /**
     * @ORM\Column(type="date")
     *
     */
    private $dateReservation;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     */
    private $nbPersonne;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
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
     */
    private $Remarques;


    /**
     * @ORM\Column(type="string")
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
     * @param \DateTimeInterface $createAt
     * @return Reservation
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


    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total): void
    {
        $this->total = $total;
    }
}
