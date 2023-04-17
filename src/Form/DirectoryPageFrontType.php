<?php

namespace App\Form;

use App\Entity\DirectoryPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectoryPageFrontType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $classes = [];
        for ($i = 1980; $i <= idate('Y'); $i++) {
            $classes[$i.' / '.($i + 1)] = $i + 1;
        }

        $builder
            ->add('class', ChoiceType::class, [
                'label' => 'Promotion',
                'required' => true,
                'choices' => $classes,
                'data' => date('Y', strtotime('+1 year')),
            ])
            ->add('email', TextType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('linkedinLink', TextType::class, [
                'label' => 'Lien LinkedIn',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DirectoryPage::class,
        ]);
    }
}
