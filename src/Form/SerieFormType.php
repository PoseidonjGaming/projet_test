<?php

namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class SerieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('date_diff', DateType::class, [
                'input'  => 'datetime',
                'widget' => 'single_text',
            ])
            ->add('photo',   FileType::class, [
                'label' => false,
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'attr' => ['onchange' => 'previewPicture(this)']

            ])
            ->add('summary', TextAreaType::class, ['attr' => ['cols' => 45, 'rows' => 7]])
            ->add('url_ba')
            ->add('Valider', SubmitType::class, ['attr' => ['class' => 'btn btn-primary']])
            ->add('reset', ResetType::class, ['attr' => ['class' => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Serie::class,
        ]);
    }
}
