<?php

namespace App\Controller;

use App\Entity\Compte;
use App\Form\CompteType;
use App\Repository\CompteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/compte")
 */
class CompteController extends AbstractController
{
    /**
     * @Route("/", name="compte_index", methods={"GET"})
     */
    public function index(CompteRepository $compteRepository) : Response
    {
        return $this->render('compte/index.html.twig', [
            'comptes' => $compteRepository->findAll(),
            'in_param' => 'show'
        ]);
    }

    /**
     * @Route("/new", name="compte_new", methods={"GET","POST"})
     */
    public function new(Request $request) : Response
    {
        $compte = new Compte();
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($compte);
            $entityManager->flush();

            return $this->redirectToRoute('compte_index');
        }

        return $this->render('compte/new.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
            'in_param' => 'show'
        ]);
    }

    /**
     * @Route("/{id}", name="compte_show", methods={"GET"})
     */
    public function show(Compte $compte) : Response
    {
        return $this->render('compte/show.html.twig', [
            'compte' => $compte,
            'in_param' => 'show'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="compte_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Compte $compte) : Response
    {
        $form = $this->createForm(CompteType::class, $compte);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('compte_index', [
                'id' => $compte->getId(),
            ]);
        }

        return $this->render('compte/edit.html.twig', [
            'compte' => $compte,
            'form' => $form->createView(),
            'in_param' => 'show'
        ]);
    }

    /**
     * @Route("/{id}", name="compte_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Compte $compte) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $compte->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($compte);
            $entityManager->flush();
        }

        return $this->redirectToRoute('compte_index');
    }

    /**
     * @Route("/recharger/ajax", name="compte_recharger_ajax", methods={"POST"})
     */
    public function rechargerAjax(Request $request) : Response
    {

        $api = $this->getParameter('api_load_compte');

        $ch = curl_init();
        
        // Configuration de l'URL et d'autres options
        // curl_setopt($ch, CURLOPT_URL, "http://192.168.5.22/vireprel/public/api/loadCompte.php");
        curl_setopt($ch, CURLOPT_URL, "$api");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // Récupération de l'URL et affichage sur le navigateur
        $response = curl_exec($ch);
        
        // Fermeture de la session cURL
        curl_close($ch);
        
        //var_dump($response);
        // return new JsonResponse(
        //     $response
        // );

        return new Response(
            $response
        );

    }//rechargerAjax

    /**
     * @Route("/{id}/select/ajax", name="compte_select_ajax", methods={"GET"})
     */
    public function selectAjax(Compte $compte) : Response
    {

        // return $this->render('compte/show.html.twig', [
        //     'compte' => $compte,
        //     'in_param' => 'show'
        // ]);

        // $array = (array)$compte;
        // // dump($array);

        // foreach ($array as $key => $value) {
        //     dump($key);
        //     dump($value);
        // }

        $serializer = $this->container->get('serializer');
        $reports = $serializer->serialize($compte, 'json');
        // dump($reports);

        // exit("------------------");

        // $response = array();
        // // $response[] = (array)$compte->getCompte();
        // $response[] = (array)$compte;

        return new Response($reports);
        // return new JsonResponse(
        //     // $response
            
        // );


    }
}
