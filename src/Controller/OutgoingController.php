<?php

namespace App\Controller;

use App\Entity\Outgoing;
use App\Form\OutgoingType;
use App\Repository\OutgoingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/outgoing")
 */
class OutgoingController extends AbstractController
{
    /**
     * @Route("/", name="outgoing_index", methods={"GET"})
     */
    public function index(OutgoingRepository $outgoingRepository) : Response
    {
        return $this->render('outgoing/index.html.twig', [
            'outgoings' => $outgoingRepository->findAll(),
            'in_message' => 'show'
        ]);
    }

    /**
     * @Route("/new", name="outgoing_new", methods={"GET","POST"})
     */
    public function new(Request $request) : Response
    {
        $outgoing = new Outgoing();
        $form = $this->createForm(OutgoingType::class, $outgoing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($outgoing);
            $entityManager->flush();

            return $this->redirectToRoute('outgoing_index');
        }

        return $this->render('outgoing/new.html.twig', [
            'outgoing' => $outgoing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="outgoing_show", methods={"GET"})
     */
    public function show(Outgoing $outgoing) : Response
    {
        return $this->render('outgoing/show.html.twig', [
            'outgoing' => $outgoing,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="outgoing_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Outgoing $outgoing) : Response
    {
        $form = $this->createForm(OutgoingType::class, $outgoing);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('outgoing_index', [
                'id' => $outgoing->getId(),
            ]);
        }

        return $this->render('outgoing/edit.html.twig', [
            'outgoing' => $outgoing,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="outgoing_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Outgoing $outgoing) : Response
    {
        if ($this->isCsrfTokenValid('delete' . $outgoing->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($outgoing);
            $entityManager->flush();
        }

        return $this->redirectToRoute('outgoing_index');
    }
}
