<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Chaton;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class ChatonType extends AbstractType
{
        public function buildForm(FormBuilderInterface $builder, array $options): void
        {
            $builder
                ->add('Nom')
                ->add('DateDeNaissance', null, [
                    'widget' => 'single_text',
                ])
                //Choix d'une photo en local
                ->add('Photo', FileType::class, [
                    'label' => 'Photo (fichier image)',
                    'mapped' => false, // Ce champ n'est pas directement lié à l'entité
                    'required' => false,
                ])
                ->add('Categorie', EntityType::class, [
                    'class' => Categorie::class,
                    'choice_label' => 'Titre',
                ])
            ;
        }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chaton::class,
        ]);
    }
}
