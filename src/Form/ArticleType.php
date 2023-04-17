<?php

namespace App\Form;

use App\Entity\Article;
use App\Enum\StatusArticle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('author', TextType::class, [
                'label' => 'Auteur',
                'required' => true,
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => 'Date de publication',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => false,
                'attr' => [
                    'data-tinymce-target' => 'postContent',
                ],
            ])
            ->add('status', EnumType::class, [
                'label' => 'Statut',
                'required' => true,
                'class' => StatusArticle::class,
                'placeholder' => false,
                'choice_label' => function ($choice, $key, $value) {
                    return StatusArticle::getLabel($choice);
                },
                'constraints' => [new NotBlank()],
            ])
            ->add('featuredImageFile', VichImageType::class, [
                'label' => 'Image mise en avant',
                'required' => false,
                'delete_label' => 'Supprimer l\'image ?',
                'download_label' => 'Télécharger',
                'attr' => ['accept' => 'image/png, image/jpeg'],
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'Meta description',
                'required' => true,
            ])
            ->add('tag', TextType::class, [
                'label' => 'Tag',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
