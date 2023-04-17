<?php

namespace App\Form;

use App\Entity\FooterColumn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FooterColumnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la colonne',
                'required' => true,
            ])

            ->add('position', IntegerType::class, [
                'label' => 'Position de la colonne',
                'required' => true,
                'attr' => [
                    'min' => 1,
                    'max' => 4,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FooterColumn::class,
        ]);
    }
}
