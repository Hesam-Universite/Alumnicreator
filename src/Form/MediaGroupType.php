<?php

namespace App\Form;

use App\Entity\MediaGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MediaGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mediaFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'delete_label' => 'Supprimer la photo ?',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MediaGroup::class,
        ]);
    }
}
