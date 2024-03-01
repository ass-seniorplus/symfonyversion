<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomComplet', TextType::class, [
                'label' => 'Nom Complet'
            ])
            ->add('email', EmailType::class)
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone (Format français uniquement)'
            ])
            ->add('typeAide', ChoiceType::class, [
                'label' => 'Type d\'Aide',
                'choices' => [
                    'Aide Ménagère' => 'aideMenagere',
                    'Transport et Accompagnement' => 'transportAccompagnement'
                    // Ajoutez d'autres options si nécessaire
                ]
            ])
            ->add('message', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
