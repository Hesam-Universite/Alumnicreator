<?php

namespace App\Form;

use App\Entity\ActivityArea;
use App\Entity\User;
use App\Repository\ActivityAreaRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class CompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                ],
                'second_options' => [
                    'label' => 'Répéter le mot de passe',
                ],
                'invalid_message' => 'Les deux mots de passes ne correspondent pas',
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
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
            ->add('siret', TextType::class, [
                'label' => 'SIRET',
                'required' => false,
            ])
            ->add('companyName', TextType::class, [
                'label' => 'Nom de l\'entreprise',
                'required' => true,
            ])
            ->add('roleInTheCompany', TextType::class, [
                'label' => 'Fonction dans l\'entreprise',
                'required' => false,
            ])
            ->add('companyAddress', TextType::class, [
                'label' => 'Adresse de l\'entreprise',
                'required' => false,
            ])
            ->add('acceptedCandidacyNotification', CheckboxType::class, [
                'label' => 'J\'accepte de recevoir un e-mail lorsqu\'un profil postule à l\'une de mes offres.',
                'required' => false,
            ])
            ->add('receiveMessageNotificationsByEmail', CheckboxType::class, [
                'label' => 'Je souhaite recevoir les notifications de la messagerie privée par mail.',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
