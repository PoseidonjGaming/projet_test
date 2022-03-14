<?php

namespace App\Form;

use App\Entity\Personnage;
use App\Entity\Serie;
use App\Entity\Acteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PersonnageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('serie', EntityType::class, [            
                'class' => Serie::class,            
                'choice_label' => 'nom',
                'choice_value' => 'id',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nom', 'ASC');},
                'multiple' => false,
                'expanded' => false,
                'mapped'=>true,
                'required'=>true,
                'attr' => 
                    [
                        'class' =>'form-select'
                    ]
                
            ])
            ->add('acteur', EntityType::class, [            
                'class' => Acteur::class,            
                'choice_label' => function ($allChoices, $currentChoiceKey)
                {
                    return $allChoices->getPrenom(). " " . $allChoices->getNom();
                },
                'choice_value' => 'id',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nom', 'ASC');},
                'multiple' => false,
                'expanded' => false,
                'mapped'=>true,
                'required'=>true,
                'attr' => 
                    [
                        'class' =>'form-select'
                    ]
                
            ])
            ->add('Valider', SubmitType::class,['attr' => ['class' =>'btn btn-primary']])
            ->add('reset', ResetType::class,['attr' => ['class' =>'btn btn-primary']])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personnage::class,
        ]);
    }
}
