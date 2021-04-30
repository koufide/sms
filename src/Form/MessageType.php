<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre')
            ->add('contenu')
            // ->add('datedit')
            // ->add('datevalid1')
            // ->add('isValid1')
            // ->add('isDiffu')
            // ->add('isValid2')
            ->add('isDiffere', CheckboxType::class, [
                'label' => 'Differé',
                'attr' => [
                    'v-model' => 'form.isdiffere'
                ]
            ])
            // ->add('datevalid2')
            // ->add('editepar')
            // ->add('validepar')
            ->add('datediffu', DateTimeType::class, [
                'label' => false,
                // 'date_label' => 'Starts On',
                // 'date_widget' => 'single_text',
                'date_widget' => 'choice',
                'attr' => [
                    // 'v-show' => 'form.isdiffere'
                    'v-if' => 'form.isdiffere'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
