<?php

namespace App\Form;

use App\Entity\Job;
use App\Enum\StudyLevel;
use App\Enum\TypeOfContract;
use App\Form\DataTransformer\DesiredLevelToEnumTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class JobType extends AbstractType
{
    public function __construct(
        private DesiredLevelToEnumTransformer $desiredLevelToEnumTransformer,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'offre',
                'required' => true,
            ])
            ->add('companyPresentation', TextareaType::class, [
                'label' => 'Présentation de l\'entreprise',
                'required' => false,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
            ])
            ->add('activityArea', EntityType::class, [
                'label' => 'Secteur d\'activité',
                'required' => true,
                'class' => \App\Entity\ActivityArea::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez votre secteur d\'activité',
                'constraints' => [new NotBlank()],
                'attr' => ['data-activityarea-target' => 'activityarea'],
            ])
            ->add('desiredLevel', EnumType::class, [
                'label' => 'Niveaux recherchés',
                'required' => true,
                'class' => StudyLevel::class,
                'placeholder' => 'Choisissez les niveaux d\'études recherchés',
                'multiple' => true,
                'expanded' => true,
                'choice_label' => function ($choice, $key, $value) {
                    return StudyLevel::getLabel($choice);
                },
            ])
            ->add('typeOfContract', EnumType::class, [
                'label' => 'Type de contract',
                'required' => true,
                'class' => TypeOfContract::class,
                'placeholder' => 'Choisissez les niveaux d\'études recherchés',
                'choice_label' => function ($choice, $key, $value) {
                    return TypeOfContract::getLabel($choice);
                },
                'expanded' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
            ])
            ->add('remuneration', NumberType::class, [
                'label' => 'Rémunération',
                'required' => false,
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('linkToTheJobOffer', UrlType::class, [
                'label' => 'Lien de l\'offre d\'emploi',
                'required' => true,
            ])
            ->add('startDate', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('deadlineJobOffer', DateType::class, [
                'label' => 'Date de fin de l\'annonce',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('companyLogoFile', VichImageType::class, [
                'label' => 'Logo d\'entreprise',
                'required' => false,
                'delete_label' => 'Supprimer le logo ?',
            ])
            ->add('attachmentFile', VichFileType::class, [
                'label' => 'Pièce jointe',
                'required' => false,
                'delete_label' => 'Supprimer la pièce jointe ?',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
            ])
        ;

        $builder->get('desiredLevel')
            ->addModelTransformer($this->desiredLevelToEnumTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
