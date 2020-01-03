<?php

namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Salle;
use App\Entity\Seance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => 'nom',
            ])
            
           /* ->add('date_reservation', DateType::class, ['attr' => ['placeholder' => 'Cliquez ici pour selectionner une date'], 'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy', 'data' => new \DateTime()])*/

            ->add('remarques', TextareaType::class, ['required' => false, 'attr' => ['placeholder' => 'Laissez vide si vous n\'avez rien de special à preciser']])

            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
