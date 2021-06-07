<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    private ?string $oldPassword;

    /**
     * @Assert\Length(
     *     min="4",
     *     minMessage="Votre mot de passe doit faire au moins 4 caractères"
     * )
     */
    private ?string $newPasswordUpdate;

    /**
     * @Assert\EqualTo(
     *     propertyPath="newPasswordUpdate",
     *     message="Vous n'avez pas correctemment confirmé votre mot de passe"
     * )
     */
    private ?string $confirmPassword;

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPasswordUpdate(): ?string
    {
        return $this->newPasswordUpdate;
    }

    public function setNewPasswordUpdate(string $newPasswordUpdate): self
    {
        $this->newPasswordUpdate = $newPasswordUpdate;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
