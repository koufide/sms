<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegistrationFormType extends AbstractType
{

    private $roleHierarchy;
    public function __construct(RoleHierarchyInterface $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ]
            ])
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm form-control-plaintext',
                    'readonly' => 'readonly'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control-sm'
                ],
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Saisir le mot de passe',
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Ton mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('confirm_password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'group_by' => 'brand',
                'attr' => [
                    'class' => 'form-control-sm'
                ],
                'mapped' => false,
                'choices' => [
                    $this->getRoles()
                    // 'Choix' => [$this->getRoles()]
                ],
            ])
            ->add('isActive', HiddenType::class, array(
                'attr' => [
                    'class' => 'form-control-sm'
                ],
                'mapped' => true,
                'constraints' => new IsTrue(),
            ))
            ->add('displayname', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ]
            ])
            ->add('employeeid', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ]
            ])
            ->add('telephone', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ]
            ])
            ->add('samaccountname', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ],
                'label' => 'Compte utilisateur'
            ])

            ->add('distinguishedname', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ],
                //'label' => 'Compte utilisateur'
            ])
            ->add('mail', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ],
                //'label' => 'Compte utilisateur'
            ])
            ->add('description', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ],
                //'label' => 'Compte utilisateur'
            ])
            ->add('manager', TextType::class, [
                'attr' => [
                    'class' => 'form-control-sm  form-control-plaintext',
                    'readonly' => 'readonly'
                ],
                //'label' => 'Compte utilisateur'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function getRoles()
    {
        // $roles = [];
        // $roles = ['ROLE_TEST' => null];

        $roles = array();
        array_walk_recursive($this->roleHierarchy, function ($role) use (&$roles) {
            // $roles[$role] = $role;
            //$roles[$role] = null;

            $tmp = str_replace('ROLE_', '', $role);
            // $roles[$role] = $role;
            $roles[$tmp] = $role;
        });

        // destroy a single element of an array
        unset($roles['ROLE_USER']);
        unset($roles['ROLE_ALLOWED_TO_SWITCH']);
        unset($roles['USER']);
        unset($roles['ALLOWED_TO_SWITCH']);



        // dump($roles);
        // exit("<br/>\n <br/>------quitter");


        return ['Selectionner' => $roles];
    }//getRoles
}
