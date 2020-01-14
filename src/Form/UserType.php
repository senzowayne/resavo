<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name' ,null,['attr' => ['placeholder' => 'Entrez votre nom']])
            ->add('firstName' ,null,['attr' => ['placeholder' => 'Entrez votre prénom']])
            ->add('email',null,['attr' => ['placeholder' => 'Ex: Dupont@gmail.com']])
            ->add('confirm_email', null, ['attr' => ['placeholder' => 'Retapez votre e-mail'], 'label' => 'Confirmer votre e-mail'])
            ->add('hash', PasswordType::class, ['attr' => ['placeholder' => 'Entrez votre mot de passe'], 'label' => 'Mot de passe'])
            ->add('confirm_hash', PasswordType::class, ['attr' => ['placeholder' => 'Confirmez votre mot de passe'],'label' => 'Confirmer votre mot de passe'])
            ->add('number', null, ['attr' => ['placeholder' => 'ex: 0652....'],'label' => 'Numero de téléphone'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
