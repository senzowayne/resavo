<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Meeting;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'nom',
            ])

           /* ->add('bookingDate', DateType::class, ['attr' => ['placeholder' => 'Cliquez ici pour selectionner une date'], 'widget' => 'single_text', 'html5' => false, 'format' => 'dd-MM-yyyy', 'data' => new \DateTime()])*/

            ->add('notices', TextareaType::class, ['required' => false, 'attr' => ['placeholder' => 'Laissez vide si vous n\'avez rien de special à preciser']])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
