<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Type;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

 #[ORM\Entity(repositoryClass: UserRepository::class)]
/**
 * @UniqueEntity(
 *     fields="email",
 *     message="Cette adresse e-mail existe déjà, essayer de vous connecter via la page identifier"
 * )
 */
class User implements UserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;
    #[ORM\Column(type: Types::STRING, length: 255)]
    /**
     * @Assert\NotBlank
     * @Groups({"resa:read"})
     */

    private ?string $name;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    /**
     * @Assert\NotBlank
     * @Groups({"resa:read"})
     */

    private ?string $firstName;
     #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    /**
     * @Assert\EqualTo(propertyPath="email", message="Vous n'avez pas tapé le meme e-mail")
     * @Assert\NotBlank
     * @Assert\Email()
     * @Groups({"resa:read"})
     */

    private ?string $email;

    /**
     * @Assert\EqualTo(propertyPath="email", message="Vous n'avez pas tapé le meme e-mail")
     */
    public ?string $confirm_email;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]

    private ?string $avatar;
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    /**
     * @Assert\Length(min="4", minMessage="Votre mot de passe doit faire au minimum 4 caractères")
     * @Assert\EqualTo(propertyPath="confirm_hash", message="Vous n'avez pas tapé le meme mot de passe")
     */

    private ?string $hash = null;

    /**
     * @Assert\EqualTo(propertyPath="hash", message="Vous n'avez pas tapé le meme mot de passe")
     */
    public ?string $confirm_hash;

    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: "user")]
    private Collection $bookings;

    #[ORM\OneToMany(targetEntity: Paypal::class, mappedBy: "user", cascade: ["persist"])]
    private Collection $payments;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: "users")]

    private Collection $userRoles;
    #[ORM\Column(type: Types::STRING, nullable: true)]
    /**
     * @Groups({"resa:read"})
     */
    private ?string $number;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $googleId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private  $resetToken;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->userRoles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function getResetToken()
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->contains($booking)) {
            $this->bookings->removeElement($booking);
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     */
    public function getRoles(): array
    {
        $roles = $this->userRoles->map(static function (Role $role) {
            return $role->getTitle();
        })->toArray();
        $roles[] = 'ROLE_USER';

        return $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     */
    public function getPassword(): ?string
    {
        return $this->hash;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     */
    public function getSalt(): ?string
    {
        return null;
    }

    public function __toString()
    {
        return $this->getFirstName();
    }

    /**
     * Returns the username used to authenticate the user.
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Collection|Paypal[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Paypal $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setUser($this);
        }

        return $this;
    }

    public function removePayment(Paypal $payment): self
    {
        if ($this->payments->contains($payment)) {
            $this->payments->removeElement($payment);
            // set the owning side to null (unless already changed)
            if ($payment->getUser() === $this) {
                $payment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Role[]
     */
    public function getUserRoles(): Collection
    {
        return $this->userRoles;
    }

    public function addUserRole(Role $userRole): self
    {
        if (!$this->userRoles->contains($userRole)) {
            $this->userRoles[] = $userRole;
            $userRole->addUser($this);
        }

        return $this;
    }

    public function removeUserRole(Role $userRole): self
    {
        if ($this->userRoles->contains($userRole)) {
            $this->userRoles->removeElement($userRole);
            $userRole->removeUser($this);
        }

        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): User
    {
        $this->googleId = $googleId;
        return $this;
    }
}
