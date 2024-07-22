<?php

namespace App\Form;

use App\Entity\Quality;
use App\Entity\Room;
use App\Entity\Theater;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number')
            ->add('capacity')
            ->add('columns')
            ->add('theater', EntityType::class, [
                'class' => Theater::class,
                'choice_label' => 'id',
            ])
            ->add('quality', EntityType::class, [
                'class' => Quality::class,
                'choice_label' => 'id',
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
