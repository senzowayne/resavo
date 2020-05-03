<?php

namespace App\Form;

use App\Entity\ConfigMerchant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigMerchantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nameMerchant')
            ->add('description')
            ->add('paymentService', ChoiceType::class, array(
                    'choices' => [
                        'Service disponible' => [
                            'Paypal' => 'Paypal',
                            'Stripe' => 'Stripe',
                        ]
                    ]
                )
            )
            ->add('patternColor', ChoiceType::class, array(
                    'choices' => [
                        'Thèmes disponible' => [
                            'color1' => 'palette 1',
                            'color2' => 'pallete 2',
                        ]
                    ]
                )
            )
            ->add('maintenance')
            ->add('Validé', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConfigMerchant::class
        ]);
    }
}
