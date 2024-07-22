<?php

namespace App\Form;

use App\Entity\Quality;
use App\Entity\Room;
use App\Entity\Theater;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', TextType::class, [
                'required' => true,
                'label' => 'Nom de la salle',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('capacity', null, [
                'required' => true,
                'label' => 'Nombre de sièges',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('columns', null, [
                'required' => true,
                'label' => 'Nombre de rengées (e.g. 1,2 ou 3)',
                'attr' => [
                    'class' => 'form-control w-25',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
            ])
            ->add('theater', EntityType::class, [
                'class' => Theater::class,
                'choice_label' => 'city',
                'attr' => [
                    'class' => 'form-control w-25',
                ]
            ])
            ->add('quality', EntityType::class, [
                'class' => Quality::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control w-25',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
