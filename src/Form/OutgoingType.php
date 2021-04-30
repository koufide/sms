<?php

namespace App\Form;

use App\Entity\Outgoing;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OutgoingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('de')
            ->add('a')
            ->add('messageId')
            ->add('statusSendsms')
            ->add('text')
            ->add('sendsmsAt')
            ->add('resultsReports')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Outgoing::class,
        ]);
    }
}
