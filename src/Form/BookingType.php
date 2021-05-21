<?php

namespace App\Form;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Meeting;
use DateTime;
use App\Repository\MeetingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
                'label' => 'Salle',
                'choice_label' => 'name',
                'disabled' => true
            ])
  
            ->add('booking_date', DateType::class, [
                'widget' => 'single_text',
                'attr' => ['min' => (new DateTime())->format('Y-m-d')],
                'label' => 'Date de la réservation',
                'data' => $options['booking_date']
                ])

            ->add('notices', TextareaType::class, [
                'required' => false, 
                'attr' => ['placeholder' => 'Laissez vide si vous n\'avez rien de special à preciser'],
                'label' => 'Notes'
            ])
            
                ->add('meeting', EntityType::class, [
                    'class' => Meeting::class,
                    'choice_label' => 'label',
                    'query_builder' => function (MeetingRepository $mr) use ($options) {
                                       return $mr->findAllMeetingsByRoom($options['room_id']);},
                    'label' => 'Créneau par salle',
                ]);
            }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
            'booking_date' => '',
            'room_id' => ''
        ]);
    }
}
