<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\Ldap\Ldap;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils) : Response
    {

        // // Si le visiteur est déjà identifié, on le redirige vers l'accueil
        // if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        //     return $this->redirectToRoute('bbg');
        // }

        
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    //https://blog.dev-web.io/2017/12/16/symfony-4-gestion-des-utilisateurs-sans-fosuserbundle-chapitre-2/

    /**
     * @Route("/logout", name="app_logout")
     */
    // public function logout(AuthenticationUtils $authenticationUtils)
    public function logout()
    {

        throw new \Exception('This should never be reached!');


        // get the login error if there is one
        // $error = $authenticationUtils->getLastAuthenticationError();
        // // last username entered by the user
        // $lastUsername = $authenticationUtils->getLastUsername();

        // return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);


    }
}
