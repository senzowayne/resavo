<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(attributes={"normalization_context"={"groups"={"meeting:read"}}},
 *     collectionOperations={
 *         "get",
 *     },
 *     itemOperations={"get"}
 * )
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
    private $id;

    public function __toString(): string
    {
        return $this->label;
    }

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"meeting:read"})
     */
    private $label;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="meetings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * @return mixed
     */
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
}
