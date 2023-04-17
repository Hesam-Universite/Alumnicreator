<?php

namespace App\Form;

use App\Entity\Instance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\UuidType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InstanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('instanceUrl', UrlType::class, [
                'label' => 'URL de l\'instance à connecter',
                'required' => true,
            ])
            ->add('externalId', UuidType::class, [
                'label' => 'Code de l\'instance à connecter',
                'required' => false,
            ])
            ->add('allowShareJobs', CheckboxType::class, [
                'label' => 'Autoriser le partage des offres d\'emploi ?',
                'required' => false,
            ])
            ->add('allowShareResumes', CheckboxType::class, [
                'label' => 'Autoriser le partage des CV ?',
                'required' => false,
            ])
            ->add('allowShareStudents', CheckboxType::class, [
                'label' => 'Autoriser le partage de l\'annuaire des étudiants ?',
                'required' => false,
            ])
            ->add('allowShareCompanies', CheckboxType::class, [
                'label' => 'Autoriser le partage de l\'annuaire des entreprises ?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Instance::class,
        ]);
    }
}
