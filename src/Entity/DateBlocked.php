<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DateBlockedRepository")
 */
class DateBlocked
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
    private $raison;

    /**
     * @ORM\Column(type="date")
     */
    private $dateBlocked;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): self
    {
        $this->raison = $raison;

        return $this;
    }

    public function getDateBlocked(): ?\DateTimeInterface
    {
        return $this->dateBlocked;
    }

    public function setDateBlocked(\DateTimeInterface $dateBlocked): self
    {
        $this->dateBlocked = $dateBlocked;

        return $this;
    }
}
