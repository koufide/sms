<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;

use App\Service\MyLdap;

use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    private $myLdap;

    public function __construct(MyLdap $myLdap)
    {
        $this->myLdap = $myLdap;
    }


    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository)
    {
        dump($userRepository->findAll());
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'in_habili' => 'show'
        ]);
    }


    // /**
    //  * @Route("/import", name="user_import")
    //  */
    // public function import(Request $request)
    // {
    //     $session = $request->getSession();
    //     $now_datetime = new \DateTime('NOW', new \DateTimeZone('UTC'));

    //     if ($request->isMethod('get')) {
    //         $ldap_dom = '@' . $this->getParameter('ldap_dom');

    //         // $email = $request->request->get('email');
    //         $email = $request->query->get('email');

    //         $email = str_replace($ldap_dom, '', $email);
    //         $email = strtolower($email);

    //         $ldap_filtre = $this->getParameter('ldap_filtre');
    //         $ldap_filtre = str_replace('{login}', $email, $ldap_filtre);

    //         $tab = $this->myLdap->findUser(
    //             $this->getParameter('ldap_host'),
    //             $this->getParameter('ldap_port'),
    //             $this->getParameter('ldap_dn'),
    //             $ldap_dom,
    //             $this->getParameter('ldap_user'),
    //             $this->getParameter('ldap_password'),
    //             $ldap_filtre
    //         );

    //         if (empty($tab)) {
    //             $message = "Aucun resultat";
    //             $session->getFlashBag()->add('alert-danger', $message);
    //             return $this->redirectToRoute('app_register');
    //         }

    //         if (array_key_exists('erreur', $tab)) {
    //             $message = $tab['erreur'];
    //             $session->getFlashBag()->add('alert-danger', $message);
    //             return $this->redirectToRoute('app_register');
    //         }

    //         // if (!empty($tab)) {

    //         // $entityManager = $this->getDoctrine()->getManager();

    //         $user = new User();



    //         $dateTime = new \DateTime($tab['accountexpires'], new \DateTimeZone('UTC'));

    //         $user->setEmail($tab['mail']);
    //         $user->setTelephone($tab['telephone']);
    //         $user->setDisplayname($tab['name']);
    //         $user->setEmployeeid($tab['employeeid']);
    //         $user->setSamaccountname($tab['samaccountname']);
    //         $user->setUsername($tab['samaccountname']);
    //         $user->setDistinguishedname($tab['distinguishedname']);
    //         $user->setMail($tab['mail']);
    //         $user->setDescription($tab['description']);
    //         $dateTime = new \DateTime($tab['lastlogon'], new \DateTimeZone('UTC'));
    //         $user->setManager($tab['manager']);
    //         $user->setDateAjout($now_datetime);
    //         $user->setRoles(['ROLE_USER']);


    //         $form = $this->createForm(RegistrationFormType::class, $user);
    //         $form->handleRequest($request);
            
            
    //         // $entityManager->persist($user);


    //             // $form_import = $this->createForm(userImportType::class, $user);
    //             // $form_import->handleRequest($request);


    //         $message = "Recherche effectué avec succès";
    //         // $session->getFlashBag()->add('alert-success', $message);

    //         // dump($user);
    //         // exit("<br/>\n--------------)");
    //         // }

    //     }//if ($request->isMethod('post')) {



    //     return $this->render('registration/register.html.twig', [
    //         'user' => $user,
    //         'in_admin' => 'show',
    //         'registrationForm' => $form->createView(),
    //     ]);
    // }


    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request) : Response
    {
        return $this->redirectToRoute('app_register');


        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/show", name="user_show", methods={"GET"})
     */
    public function show(User $user) : Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'in_habili' => 'show',
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user) : Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index', [
                'id' => $user->getId(),
            ]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/{id}/delete/ajax", name="user_delete_ajax", methods={"POST","GET"})
     */
    public function delete_ajax(Request $request, User $user) : Response
    {
        
        // if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
        if ($request->getMethod() == 'POST') {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $output = ['result' => 'SUPPRESSION EFFECTUEE AVEC SUCCES'];
                // dump($request->getMethod());
                // dump($user);
                // dump($output);
                // exit(("<br/>\n stoppppppppp"));
        } else {
            $output = [];

        }


        // return $this->redirectToRoute('user_index');
        return new JsonResponse($output);
    }


    /**
     * @Route("/{id}/active", name="user_active", methods={"POST"})
     */
    public function active(Request $request, User $user) : Response
    {
        if ($request->getMethod() == 'POST') {
            $user->setIsActive(!$user->getIsActive());
            $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
    }


}
