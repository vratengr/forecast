<?php

namespace App\Form;

use App\Entity\Api;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ApiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['placeholder' => 'eg: Weather API'],
                ]
            )
            ->add(
                'url',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['placeholder' => 'eg: http://api.weatherapi.com/v1/current.json?key={API_KEY}&q={CITY}'],
                ]
            )
            ->add(
                'dataIndex',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['placeholder' => 'eg: current,temp_c'],
                ]
            )
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Api::class,
        ]);
    }
}
