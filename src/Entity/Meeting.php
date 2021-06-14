<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    attributes: ["normalization_context" => ["groups" => ["meeting:read"]]],
    collectionOperations: ['get'],
    itemOperations: ['get'],
)]
#[ApiFilter(SearchFilter::class, properties: ['room' => 'exact'])]
/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetingRepository")
 */
class Meeting
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"meeting:read"})
     */
    private ?int $id = null;

    public function __toString(): string
    {
        return $this->label;
    }

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"meeting:read"})
     */
    private ?string $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="meetings")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"meeting:read"})
     */
    private ?Room $room;

    /**
     * @ORM\Column(type="boolean", options={"default" = 1})
     */
    private ?bool $isActive;

    public function getId()
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
}
