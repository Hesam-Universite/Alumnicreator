<?php

namespace App\Form;

use App\Entity\ActivityArea;
use App\Enum\Department;
use App\Enum\Region;
use App\Repository\ActivityAreaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchJobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('activityArea', EntityType::class, [
                'label' => 'Secteur d\'activité',
                'required' => false,
                'multiple' => true,
                'class' => ActivityArea::class,
                'query_builder' => function (ActivityAreaRepository $ar) {
                    return $ar->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('department', EnumType::class, [
                'label' => 'Département',
                'required' => false,
                'class' => Department::class,
                'choice_label' => function ($choice, $key, $value) {
                    return Department::getLabel($choice);
                },
                'placeholder' => 'Choisissez un département',
            ])
            ->add('region', EnumType::class, [
                'label' => 'Région',
                'required' => false,
                'class' => Region::class,
                'choice_label' => function ($choice, $key, $value) {
                    return Region::getLabel($choice);
                },
                'placeholder' => 'Choisissez une région',
            ])
        ;
    }
}
