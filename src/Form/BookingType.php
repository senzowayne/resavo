<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Meeting;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormTypeInterface;


class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'name',
            ])
  
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event){
                $form = $event->getForm();
                $booking = $event->getData();
                $bookingDate = $booking->getBookingDate();
                if ($bookingDate != null)  {
                    $form->add('booking_date', DateType::class, [
                'widget' => 'choice',
                'html5' => false, 
                'format' => 'dd-MM-yyyy',
                'data' => $bookingDate]);
                    }
            })

            ->add('notices', TextareaType::class, [
                'required' => false, 
                'attr' => ['placeholder' => 'Laissez vide si vous n\'avez rien de special à preciser']
            ])
            
            ->add('meeting', EntityType::class, [
                'class' => Meeting::class,
                'choice_label' => 'label',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
