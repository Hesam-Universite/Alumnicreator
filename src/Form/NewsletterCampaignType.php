<?php

namespace App\Form;

use App\Entity\NewsletterCampaign;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsletterCampaignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('subject', TextType::class, [
                'label' => 'Sujet',
                'required' => true,
            ])
            ->add('sendingTime', DateTimeType::class, [
                'label' => 'Date et heure d\'envoi',
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
            ->add('sendingEmail', EmailType::class, [
                'label' => 'Email d\'envoi',
                'required' => true,
            ])
            ->add('recipientEmail', EmailType::class, [
                'label' => 'Destinataire',
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NewsletterCampaign::class,
        ]);
    }
}
