<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\StatusAlumni;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
            ])
            ->add('birthday', DateType::class, [
                'years' => range(1900, date('Y') - 15),
                'label' => 'Anniversaire',
                'widget' => 'single_text',
                'data' => new \DateTime('01-01-1980'),
                'required' => true,
            ])
            ->add('personalLink', TextType::class, [
                'label' => 'Lien personnel',
                'required' => false,
            ])
            ->add('pictureFile', VichImageType::class, [
                'label' => 'Photo',
                'required' => false,
                'delete_label' => 'Supprimer la photo ?',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code postal',
                'attr' => ['data-cities-target' => 'postalcode'],
                'required' => true,
            ])
            ->add('linkedinLink', TextType::class, [
                'label' => 'Lien LinkedIn',
                'required' => false,
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
            ->add('status', EnumType::class, [
                'label' => 'Statut',
                'required' => false,
                'class' => StatusAlumni::class,
                'placeholder' => false,
                'choice_label' => function ($choice, $key, $value) {
                    return StatusAlumni::getLabel($choice);
                },
                'constraints' => [new NotBlank()],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
            ])
            ->add('city', HiddenType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => ['data-cities-target' => 'realfield'],
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
