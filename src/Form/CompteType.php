<?php

namespace App\Form;

use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('AGENCE')
            ->add('AGENCELIB')
            ->add('NOMGES')
            ->add('CLIENT')
            ->add('NOMCLIENT')
            ->add('TYPECLI')
            ->add('DATOUVCLI')
            ->add('DATFRMCLI')
            ->add('COMPTE')
            ->add('NCG')
            ->add('TYP')
            ->add('TEL')
            ->add('CATEGORIE');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
