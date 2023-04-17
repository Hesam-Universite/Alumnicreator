<?php

namespace App\Form;

use App\Entity\ActivityArea;
use App\Entity\Group;
use App\Enum\VisibilityGroup;
use App\Repository\ActivityAreaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('visibility', EnumType::class, [
                'label' => 'Visibilité',
                'required' => true,
                'class' => VisibilityGroup::class,
                'placeholder' => 'Choisissez la visibilité du groupe',
                'choice_label' => function ($choice, $key, $value) {
                    return VisibilityGroup::getLabel($choice);
                },
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation (ville, région, pays...)',
                'required' => false,
            ])
            ->add('activityArea', EntityType::class, [
                'label' => 'Secteur d\'activité',
                'required' => true,
                'class' => ActivityArea::class,
                'query_builder' => function (ActivityAreaRepository $ar) {
                    return $ar->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choisissez votre secteur d\'activité',
                'constraints' => [new NotBlank()],
                'attr' => ['data-activityarea-target' => 'activityarea'],
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'Groupe actif ?',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
