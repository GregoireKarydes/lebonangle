<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null,['attr' => ['class' => 'form-control']])
            ->add('content', null,['attr' => ['class' => 'form-control']])
            ->add('author', null,['attr' => ['class' => 'form-control']])
            ->add('email', null,['attr' => ['class' => 'form-control']])
            ->add('price', null,['attr' => ['class' => 'form-control']])
            ->add('category', null,['attr' => ['class' => 'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
