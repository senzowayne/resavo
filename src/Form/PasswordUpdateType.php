<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, ['attr' => ['placeholder' => 'Ancien mot de passe'], 'label' => 'Ancien mot de passe'])
            ->add('newPasswordUpdate', PasswordType::class, ['attr' => ['placeholder' => 'Votre nouveau mot de passe'], 'label' => 'nouveau mot de passe'])
            ->add('confirmPassword', PasswordType::class, ['attr' => ['placeholder' => 'Confirmez votre nouveau mot de passe'], 'label' => 'Confirmez votre nouveau mot de passe'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
