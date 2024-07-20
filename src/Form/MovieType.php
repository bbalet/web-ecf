<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imdb_id', TextType::class, [
                'label' => 'Identifiant IMDB',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un identifiant IMDB',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('year', null, [
                'label' => 'Année',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('duration', null, [
                'label' => 'Durée (en minutes)',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('minimum_age', null, [
                'label' => 'Age minimum',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('is_team_favorite', null, [
                'label' => 'Film préféré de l\'équipe',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('dateAdded', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Movie::class,
        ]);
    }
}
