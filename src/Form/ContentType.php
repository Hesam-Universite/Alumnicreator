<?php

namespace App\Form;

use App\Entity\Content;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('logoFile', VichImageType::class, [
                'label' => 'Logo',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('faviconFile', VichImageType::class, [
                'label' => 'Favicon',
                'required' => false,
                'delete_label' => 'Supprimer le favicon ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('heroImageFile', VichImageType::class, [
                'label' => 'Image hero (à droite du titre de la page)',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('studentsImageFile', VichImageType::class, [
                'label' => 'Image section étudiants / alumnis',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('companiesImageFile', VichImageType::class, [
                'label' => 'Image section entreprises',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('directoryImageFile', VichImageType::class, [
                'label' => 'Image section annuaire',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('careerImageFile', VichImageType::class, [
                'label' => 'Image section carrière',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('mainTitle', TextType::class, [
                'label' => 'Titre de la page',
                'required' => false,
            ])
            ->add('youtubeVideoLink', TextType::class, [
                'label' => 'Lien YouTube embed (doit commencer par https://www.youtube.com/embed/)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.youtube.com/embed/',
                ],
            ])
            ->add('paragraphOne', TextareaType::class, [
                'label' => 'Zone de texte au-dessus du titre',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphTwo', TextareaType::class, [
                'label' => 'Zone de texte en-dessous du titre',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphThree', TextareaType::class, [
                'label' => 'Zone de texte Etudiants & Diplômés',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphFour', TextareaType::class, [
                'label' => 'Zone de texte Entreprises & Recruteurs',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphFive', TextareaType::class, [
                'label' => 'Zone de texte Actualités',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphSix', TextareaType::class, [
                'label' => 'Zone de texte Témoignages',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphSeven', TextareaType::class, [
                'label' => 'Zone de texte Annuaire',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
            ->add('paragraphEight', TextareaType::class, [
                'label' => 'Zone de texte Espace carrière',
                'required' => false,
                'attr' => [
                    'data-tinymceclassic-target' => 'postContent',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Content::class,
        ]);
    }
}
