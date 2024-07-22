<?php

namespace App\Form;

use App\Entity\Movie;
use App\Entity\MovieSession;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;

class MovieSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startdate', null, [
                'required' => true,
                'label' => 'Date et heure de début',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('enddate', null, [
                'required' => true,
                'label' => 'Date et heure de fin',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('movie', EntityType::class, [
                'required' => true,
                'label' => 'Film projeté',
                'class' => Movie::class,
                'choice_label' => 'title',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('room', EntityType::class, [
                'required' => true,
                'label' => 'Salle',
                'class' => Room::class,
                'choice_label' => 'number',
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
            'data_class' => MovieSession::class,
        ]);
    }
}
