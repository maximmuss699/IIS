<?php

namespace App\Form;

use App\Entity\Device;
use App\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class DeviceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

                $builder
                    ->add('description', TextType::class, [
                        'label' => 'Device Description',
                    ])
                    ->add('user_alias', TextType::class, [
                        'label' => 'User Alias',
                    ])
                       ->add('type', EntityType::class, [
                                     'class' => Type::class,
                                     'label' => 'Device Type',
                                     'choice_label' => 'name',
                                 ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Device::class,
        ]);
    }
}
