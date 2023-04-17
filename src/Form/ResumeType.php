<?php

namespace App\Form;

use App\Entity\ActivityArea;
use App\Entity\Resume;
use App\Entity\Skill;
use App\Enum\StatusResume;
use App\Repository\ActivityAreaRepository;
use App\Repository\SkillRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ResumeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', StudentType::class, [
                'resume' => true,
            ])
            ->add('presentation', TextareaType::class, [
                'label' => 'Présentation',
                'required' => true,
            ])
            ->add('skill', EntityType::class, [
                'label' => 'Compétence',
                'required' => true,
                'class' => Skill::class,
                'query_builder' => function (SkillRepository $sr) {
                    return $sr->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'choice_label' => 'name',
                'placeholder' => 'Choisissez votre compétence',
                'constraints' => [new NotBlank()],
            ])
            ->add('resume', VichFileType::class, [
                'label' => 'CV',
                'download_label' => 'Télécharger',
                'required' => false,
            ])
            ->add('additionalFile', VichFileType::class, [
                'label' => 'Document complémentaire',
                'delete_label' => 'Supprimer le document complémentaire ?',
                'download_label' => 'Télécharger',
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
            ->add('activityAreaOther', TextType::class, [
                'label' => 'Autre',
                'required' => false,
            ])
            ->add('status', EnumType::class, [
                'label' => 'Statut',
                'required' => true,
                'class' => StatusResume::class,
                'choice_label' => function ($choice, $key, $value) {
                    return StatusResume::getLabel($choice);
                },
                'placeholder' => 'Choisissez votre statut',
                'constraints' => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Resume::class,
        ]);
    }
}
