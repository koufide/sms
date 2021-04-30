<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class DefautController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        // $this->denyAccessUnlessGranted('ROLE_USER');

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('defaut/index.html.twig', [
            'controller_name' => 'DefautController_index',
            'mainNavHome' => true, 'title' => 'Accueil',
        ]);
    }

    // /**
    //  * @Route("/defaut", name="defaut")
    //  */
    // public function defaut()
    // {
    //     return $this->render('defaut/index.html.twig', [
    //         'controller_name' => 'DefautController_defaut',
    //     ]);
    // }

}
