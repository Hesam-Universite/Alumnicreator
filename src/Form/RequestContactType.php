<?php

namespace App\Form;

use App\Entity\RequestContact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'required' => true,
                'label' => 'Email',
            ])
            ->add('object', TextType::class, [
                'required' => true,
                'label' => 'Objet',
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => 'Message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RequestContact::class,
        ]);
    }
}
