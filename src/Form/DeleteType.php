<?php

namespace App\Form;

use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Titre', null, [
                'label' => 'Êtes-vous sûr de vouloir supprimer cette catégorie ?',
                'disabled' => true
            ])
            ->add('oui', SubmitType::class, [
                'label' => 'Oui',
                'attr' => ['class' => 'btn btn-danger']
            ])
            ->add('non', SubmitType::class, [
                'label' => 'Non',
                'attr' => ['class' => 'btn btn-secondary']
            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
