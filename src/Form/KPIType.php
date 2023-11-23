<?php

namespace App\Form;

use App\Entity\KPI;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Parameters;
class KPIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value')
            ->add('function', ChoiceType::class, [
                'choices' => [
                    // Populate functions here, like:
                    'greater then' => 'gt',
                    'less then' => 'lt',
                    'equal to' => 'eq',
                    'not equal to' => 'neq',
                    // Add more as needed
                ],
            ])
            ->add('parameter', EntityType::class, [
                'class' => Parameters::class,
                'choices' => $options['parameters'], // Use passed parameters here
                'choice_label' => 'name', // Assuming Parameter entity has a 'name' property
                'placeholder' => 'Select a Parameter',
                // Additional options can be added based on your requirements
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => KPI::class,
            'parameters' => [], // Define parameters option as an empty array by default
        ]);
    }
}
