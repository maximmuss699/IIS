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


use Symfony\Component\Validator\Constraints as Assert;
class KPIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('value', null, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Value is required',
                    ]),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'Value must be numeric',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter a numeric value *',
                ],
            ])
            ->add('function', ChoiceType::class, [
                'constraints' => [
                    new Assert\NotNull([
                        'message' => 'Function is required',
                    ]),
                ],
                'choices' => [
                    'Greater then' => 'gt',
                    'Less then' => 'lt',
                    'Equal to' => 'eq',
                    'Not equal to' => 'neq',
                ],
                'attr' => [
                    'placeholder' => 'Select a function *',
                ],
            ])
            ->add('parameter', EntityType::class, [
                'class' => Parameters::class,
                'choices' => $options['parameters'],
                'choice_label' => 'name',
                'placeholder' => 'Select a Parameter *',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Parameter is required',
                    ]),
                    new Assert\NotNull([
                        'message' => 'Parameter is required',
                    ]),
                ],
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
