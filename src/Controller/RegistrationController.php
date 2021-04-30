<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

use App\Service\MyLdap;

class RegistrationController extends AbstractController
{
    private $roleHierarchy;
    private $myLdap;

    public function __construct(RoleHierarchyInterface $roleHierarchy, MyLdap $myLdap)
    {
        $this->roleHierarchy = $roleHierarchy;
        $this->myLdap = $myLdap;
    }


    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator) : Response
    {
        $session = $request->getSession();
        $tab = [];

        if ($request->getMethod() == 'GET') {

        // $email = $request->request->get('email');
            $email = $request->query->get('email');

            if ($email != null) {
                $ldap_dom = '@' . $this->getParameter('ldap_dom');

                $email = str_replace($ldap_dom, '', $email);
                $email = strtolower($email);
                $ldap_filtre = $this->getParameter('ldap_filtre');
                $ldap_filtre = str_replace('{login}', $email, $ldap_filtre);

                $tab = $this->myLdap->findUser(
                    $this->getParameter('ldap_host'),
                    $this->getParameter('ldap_port'),
                    $this->getParameter('ldap_dn'),
                    $ldap_dom,
                    $this->getParameter('ldap_user'),
                    $this->getParameter('ldap_password'),
                    $ldap_filtre
                );

            // dump($tab);
            // exit("<br/>\n <br/>------quitter");
            }

            if (array_key_exists('erreur', $tab)) {
                $message = $tab['erreur'];
                $session->getFlashBag()->add('alert-danger', $message);
                return $this->redirectToRoute('app_register');
            }
            // dump($request->getMethod());
            // var_dump($tab);
            // exit("<br/>\n <br/>------quitter");


        } else {//POST

        }

        

        // dump($form->getErrors());

        $user = new User();

        if (!empty($tab)) {
            // $dateTime = new \DateTime($tab['accountexpires'], new \DateTimeZone('UTC'));

            $user->setEmail($tab['mail']);
            $user->setTelephone($tab['telephone']);
            $user->setDisplayname($tab['name']);
            $user->setEmployeeid($tab['employeeid']);
            $user->setSamaccountname($tab['samaccountname']);
            $user->setUsername($tab['samaccountname']);
            $user->setDistinguishedname($tab['distinguishedname']);
            $user->setMail($tab['mail']);
            $user->setDescription($tab['description']);
            $user->setManager($tab['manager']);
            // $user->setRoles(['ROLE_BOC']);
        }


        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // exit("<br/>\n <br/>------quitter");


            if ($form->isValid()) {
                // $dateTime = new \DateTime($tab['lastlogon'], new \DateTimeZone('UTC'));
                $now_datetime = new \DateTime('NOW', new \DateTimeZone('UTC'));

                $user->setDateAjout($now_datetime);
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );

            // Par defaut l'utilisateur aura toujours le rôle ROLE_USER
                //$user->setRoles(['ROLE_USER']);
                // $user->addRole("ROLE_ADMIN");

                $user->setIsActive(true); //activer par defaut

                // $roles = $request->query->get('roles');
                // dump($request->query->all());
                // dump($request->request->all());
                $registration_form = $request->request->get('registration_form');
                //dump($registration_form);
                // exit("<br/>\n <br/>------quitter");
                // dump($registration_form['roles']);
                //$role = $this->getRepository('Role')->findOneBy($registration_form['role']);

                $user->setRoles([
                    $registration_form['roles']
                ]);

                // dump($user);



                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

            // do anything else you need here, like send an email

                // return $guardHandler->authenticateUserAndHandleSuccess(
                //     $user,
                //     $request,
                //     $authenticator,
                //     'main' // firewall name in security.yaml
                // );

                $session = $request->getSession();
                $message = "CREATION EFFECTUEE AVEC SUCCES";
                $session->getFlashBag()->add('alert-info', $message);

                // return $this->redirectToRoute('app_register');
                return $this->redirectToRoute('user_show', ['id' => $user->getId()]);


            } else {
                //not valide
                // dump($form->getErrors());
                // exit("<br/>\n <br/>------quitter");

            }

        }

        // dump($form);
        // exit("<br/>\n <br/>------quitter");


        return $this->render('registration/register.html.twig', [
            'in_habili' => 'show',
            'registrationForm' => $form->createView(),
           // 'roles' => $roles,
        ]);
    }
}
