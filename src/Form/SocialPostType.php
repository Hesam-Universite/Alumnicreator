<?php

namespace App\Form;

use App\Entity\SocialPost;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SocialPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
            ])
            ->add('timeToPublished', DateTimeType::class, [
                'label' => 'Date et heure de publication',
                'required' => true,
                'widget' => 'single_text',
            ])
            ->add('postImageFile', VichImageType::class, [
                'label' => 'Image du post',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SocialPost::class,
        ]);
    }
}
