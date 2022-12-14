<?php

namespace App\Form;

use App\Entity\AdminUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null,['attr' => ['class' => 'form-control'], 'label' => 'Pseudo'])
            ->add('email', null,['attr' => ['class' => 'form-control'], 'label' => 'Adresse email'])
            ->add('plainpassword', PasswordType::class, ['required'=>false, 'attr' => ['class' => 'form-control'], 'label' => 'Mot de passe'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AdminUser::class,
        ]);
    }
}
