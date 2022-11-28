<?php

namespace App\Form;

use App\Entity\Advert;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false,
                'required'=>false, 
                'attr' => ['class' => 'form-control']
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Advert::class,
        ]);
    }
}
