<?php

namespace App\Form;

use App\Entity\UserCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author mrosser
 */
class UserCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Vorname',
            ])
            ->add('familyName', TextType::class, [
                'label' => 'Nachname',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-Mail',
                'mapped' => false,
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'Rolle',
                'choices' => [
                    'Anwender' => 'Anwender',
                    'Administrator' => 'Admin',
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Speichern'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserCollection::class,
        ));
    }
}
