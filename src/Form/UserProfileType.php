<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class UserProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'required' => true,
                'label' => 'Email*', // Add asterisk to the label
                'constraints' => [
                    new Assert\NotBlank(), // Ensure the field is not blank
                    new Assert\Email([
                        'message' => 'The email "{{ value }}" is not a valid email.', // Custom error message
                    ]),
                ],
                // Add more options or constraints if needed
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'autocomplete' => 'off', // Optional: Disable autocomplete
                    'placeholder' => '●●●●●●', // Placeholder for the password field
                ],
                'label' => 'Password*', // Add asterisk to the label
            ]);

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'active_user' => null, // Set default value for active_user option
        ]);
    }
}
