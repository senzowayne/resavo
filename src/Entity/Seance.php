<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SeanceRepository")
 */
class Seance
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    public function __toString()
    {
        return $this->libelle;
    }

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Salle", inversedBy="seances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $salle;

    /**
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $weekEnd;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }


    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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


    public function getWeekEnd(): ?bool
    {
        return $this->weekEnd;
    }

    public function setWeekEnd(?bool $weekEnd): self
    {
        $this->weekEnd = $weekEnd;

        return $this;
    }
}
