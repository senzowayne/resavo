<?php

namespace App\Entity;

//use Doctrine\DBAL\Types\Types;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

//#[ORM\Entity(repositoryClass: DateBlockedRepository::class)]
/**
 *
 * @ORM\Entity(repositoryClass="App\Repository\DateBlockedRepository")
 */

#[ApiResource(
    collectionOperations: [
        'get' => ['method' => 'get'],
    ],
    itemOperations: [],
)]
class DateBlocked
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    // #[ORM\Id, ORM\GeneratedValue]
    // #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;
//#[ORM\Column(type: Types::STRING, length: 255)]
    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $cause;
//#[ORM\Column(type: Types::DATE)]
    /**
     * @ORM\Column(type="date")
     */
    private ?DateTimeInterface $start;
//#[ORM\Column(type: Types::DATE)]
    /**
     * @ORM\Column(type="date")
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
