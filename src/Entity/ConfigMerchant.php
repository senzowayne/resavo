<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ConfigRepository")
 * @ApiResource(attributes={"normalization_context"={"groups"={"config:read"}}},
 *     collectionOperations={"get"},
 *     itemOperations={"get"}
 *     )
 */
class ConfigMerchant
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @Groups("config:read")
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nameMerchant;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $paymentService;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $patternColor;

    /**
     * @Groups("config:read")
     * @ORM\Column(type="boolean", options={"default"="0"})
     */
    private bool $maintenance = false;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @Groups("config:read")
     * @ORM\Column(type="text", length=755, nullable=true)
     */
    private ?string $description;

    public function __construct()
    {
        $this->createdAt = (new \DateTime('now'))->setTimezone(new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameMerchant(): ?string
    {
        return $this->nameMerchant;
    }

    public function setNameMerchant(string $nameMerchant): self
    {
        $this->nameMerchant = $nameMerchant;

        return $this;
    }

    public function getPaymentService(): ?string
    {
        return $this->paymentService;
    }

    public function setPaymentService(string $paymentService): self
    {
        $this->paymentService = $paymentService;

        return $this;
    }

    public function getPatternColor(): ?string
    {
        return $this->patternColor;
    }

    public function setPatternColor(string $patternColor): self
    {
        $this->patternColor = $patternColor;

        return $this;
    }

    public function getMaintenance(): ?bool
    {
        return $this->maintenance;
    }

    public function setMaintenance(bool $maintenance): self
    {
        $this->maintenance = $maintenance;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
