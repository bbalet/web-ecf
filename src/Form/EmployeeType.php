<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Email (login)',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'Mot de passe',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
                'label' => 'Prénom',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
