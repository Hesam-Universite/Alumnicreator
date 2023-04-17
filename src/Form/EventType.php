<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'évènement',
                'required' => true,
            ])
            ->add('start', DateTimeType::class, [
                'label' => 'Date et heure de début',
                'widget' => 'single_text',
                'data' => new \DateTime(date('d.m.Y', strtotime('-1 days'))),
                'required' => false,
            ])
            ->add('allDay', CheckboxType::class, [
                'label' => 'Journée(s) entière(s) ?',
                'required' => false,
                'attr' => ['data-event-target' => 'checkAllDay'],
            ])
            ->add('end', DateTimeType::class, [
                'label' => 'Date et heure de fin',
                'widget' => 'single_text',
                'data' => new \DateTime(date('d.m.Y', strtotime('+1 days'))),
                'required' => false,
            ])
            ->add('startFullday', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('endFullday', DateType::class, [
                'label' => 'Date de fin',
                'widget' => 'single_text',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
