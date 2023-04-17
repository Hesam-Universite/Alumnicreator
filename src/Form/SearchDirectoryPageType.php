<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchDirectoryPageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $classes = [];
        for ($i = idate('Y'); $i >= 1950; $i--) {
            $classes[$i.' / '.($i + 1)] = $i + 1;
        }

        $builder
            ->add('keyword', TextType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Nom ou prénom',
                ],
            ])
            ->add('class', ChoiceType::class, [
                'required' => false,
                'choices' => $classes,
                'placeholder' => 'Promotion',
            ])
        ;
    }
}
