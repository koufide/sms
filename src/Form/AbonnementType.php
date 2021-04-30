<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Entity\Compte;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Repository\CompteRepository;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('compte', EntityType::class, [
                'class' => Compte::class,
                'choice_label' => 'COMPTE',
                'placeholder' => '',
                'query_builder' => function (CompteRepository $er) {
                    $qb = $er->createQueryBuilder('c');
                    $qb
                    //->innerJoin('u.compte', 'c')
                        ->orderBy('c.COMPTE', 'ASC')
                        ->select('c');
                    //$qb->setMaxResults(1);
                    return $qb;
                },
                'attr' => [
                    // 'disabled' => 'disabled'
                ]
            ])
            // ->add('compte', TextType::class, [
            //     'attr' => [
            //         //'class' => 'form-control'
            //     ]
            // ])
            // ->add('compte', EntityType::class, [
            //     // looks for choices from this entity
            //     'class' => Compte::class,
            //     // uses the User.username property as the visible option string
            //     'choice_label' => 'client',
            //     // used to render a select box, check boxes or radios
            //     // 'multiple' => true,
            //     // 'expanded' => true,
            // ])
            ->add('datenais')
            ->add('phone');   
            // ->add('phone')
            // ->add('isActif')
            // ->add('motif');
            // ->add('creerPar')
            // ->add('desactiverPar')

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
        ]);
    }
}
