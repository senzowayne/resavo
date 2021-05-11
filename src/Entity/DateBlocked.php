<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={}
 * )
 * <<ORM\Entity(repositoryClass="App\Repository\DateBlockedRepository")>>
 */
class DateBlocked
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
    
    private ?string $cause;

    /**
     * <<ORM\Column("date")>>
     */
    
    private ?DateTimeInterface $start;

    /**
     * <<ORM\Column("date")>>
     */
    
    private ?DateTimeInterface $end;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCause(): ?string
    {
        return $this->cause;
    }

    public function setCause(string $cause): self
    {
        $this->cause = $cause;

        return $this;
    }

    public function getStart(): ?DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }
}
