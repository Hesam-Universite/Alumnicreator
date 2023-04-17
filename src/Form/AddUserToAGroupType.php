<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddUserToAGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', TextType::class, [
                'label' => 'Email de l\'utilisateur à ajouter',
                'required' => true,
            ])
            ->add('roleInGroup', CheckboxType::class, [
                'label' => 'Ajouter en tant que modérateur ?',
                'required' => false,
            ])
        ;
    }
}
