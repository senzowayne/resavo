<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 * )
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
    private $cause;

    /**
     * @ORM\Column(type="date")
     */
    private $blockedDate;

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

    public function getBlockedDate(): ?DateTimeInterface
    {
        return $this->blockedDate;
    }

    public function setBlockedDate(DateTimeInterface $blockedDate): self
    {
        $this->blockedDate = $blockedDate;

        return $this;
    }
}
