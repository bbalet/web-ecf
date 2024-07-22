<?php

namespace App\Form;

use App\Entity\Genre;
use App\Entity\Movie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class MovieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imdb_id', TextType::class, [
                'required' => true,
                'label' => 'Identifiant IMDB',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un identifiant IMDB',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un titre',
                    ])
                ],
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('year', null, [
                'label' => 'Année',
                'required' => false,
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('duration', null, [
                'label' => 'Durée (en minutes)',
                'required' => false,
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'class' => 'form-control w-25',
                    'rows' => '3',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('minimum_age', null, [
                'label' => 'Age minimum',
                'required' => false,
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('is_team_favorite', CheckboxType::class, [
                'label' => '  Film préféré de l\'équipe',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                'label_attr' => [
                    'class' => 'checkbox-inline',
                ],
            ])
            ->add('dateAdded', DateType::class, [
                'required' => true,
                'label' => 'Date d\'ajout',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => [
                    'class' => 'form-control w-25',
                ],
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
