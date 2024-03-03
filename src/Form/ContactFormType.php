<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('title', null, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre pour votre demande de contact',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('description', null, [
                'label' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }
}
