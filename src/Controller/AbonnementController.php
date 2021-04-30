<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use App\Repository\CompteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Security;

use App\Service\MyFonction;

use Psr\Log\LoggerInterface;



/**
 * @Route("/abonnement")
 * @IsGranted("ROLE_SOS_DRP")
 */
class AbonnementController extends AbstractController
{
    private $compteRepository;
    private $fct;

    public function __construct(CompteRepository $compteRepository, MyFonction $fct)
    //public function __construct(CompteRepository $compteRepository)
    {
        $this->compteRepository = $compteRepository;
        $this->fct = $fct;
    }



    /**
     * @Route("/", name="abonnement_index", methods={"GET"})
     */
    public function index(Request $request, AbonnementRepository $abonnementRepository): Response
    {

        # liste des comptes clients
        $comptes = $this->compteRepository->findAll();

        $abonnement = new Abonnement();

        $form = $this->createForm(AbonnementType::class, $abonnement, [
            'action' => $this->generateUrl('abonnement_new_ajax'),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $abonnement->setIsActif(true);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('abonnement_index');
        }


        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
            'in_abonnement' => 'show',
            'form' => $form->createView(),
            'comptes' => $comptes,

        ]);
    }


    /**
     * @Route("/list", name="abonnement_list", methods={"GET"})  
     */
    public function list(Request $request, AbonnementRepository $abonnementRepository): Response
    {
        # liste des abonnements
        #$abonnements = $abonnementRepository->findAll();



        return $this->render('abonnement/list.html.twig', [
            'in_abonnement' => 'show'
            #'abonnements' => $abonnements
            // 'abonnements' => $abonnementRepository->findAll(),
            // 'in_abonnement' => 'show',
            // 'form' => $form->createView(),
            // 'comptes' => $comptes,
        ]);
    } //list




    //Route("/list/ajax", name="abonnement_list_ajax", methods={"POST","GET"})   
    /**
     * @Route("/list/ajax", name="abonnement_list_ajax", methods={"POST"})   
     */
    public function listAjax(Request $request, AbonnementRepository $abonnementRepository): JsonResponse
    {
        //http://growingcookies.com/datatables-server-side-processing-in-symfony/

        // $output=[];
        // $output = ['1','2','3'];
        // return new JsonResponse($output);


        if (
            $request->getMethod() == 'POST'
            // or $request->getMethod() == 'GET'
        ) {
            // $draw = intval($request->request->get('draw'));
            // $start = $request->query->get('start');
            // $length = $request->query->get('length');
            // $search = $request->query->get('search');
            // $orders = $request->query->get('order');
            // $columns = $request->query->get('columns');
            $draw = intval($request->request->get('draw'));
            $start = $request->request->get('start');
            $length = $request->request->get('length');
            $search = $request->request->get('search');
            $orders = $request->request->get('order');
            $columns = $request->request->get('columns');
        } else // If the request is not a POST one, die hard
            die;

        // dump($draw);
        // dump($start);
        // dump($length);
        //  dump($search);
        // dump($orders);
        //  dump($columns);
        // var_dump($columns);

        //  dump($request->request->all());
        //  dump($request->query->all());
        //  exit("exit");


        // Process Parameters

        // Orders
        // if($orders)
        foreach ($orders as $key => $order) {
            // dump($order);
            // dump($columns[$order['column']]['name']);

            // Orders does not contain the name of the column, but its number,
            // so add the name so we can handle it just like the $columns array
            $orders[$key]['name'] = $columns[$order['column']]['name'];
        }
        // exit("exit");



        // Further filtering can be done in the Repository by passing necessary arguments
        $otherConditions = "array or whatever is needed";

        // Get results from the Repository
        $results = $abonnementRepository->getRequiredDTData($start, $length, $orders, $search, $columns, $otherConditions = null);
        // dump($results);
        // exit("exit");


        // Returned objects are of type Town
        $objects = $results["results"];
        // Get total number of objects
        $total_objects_count = $abonnementRepository->nbre();
        // Get total number of results
        $selected_objects_count = count($objects);
        // Get total number of filtered data
        $filtered_objects_count = $results["countResult"];

        // Construct response
        $response = '{
            "draw": ' . $draw . ',
            "recordsTotal": ' . $total_objects_count . ',
            "recordsFiltered": ' . $filtered_objects_count . ',
            "data": [';

        $i = 0;

        //var_dump($objects);
        // var_dump($columns);

        foreach ($objects as $key => $abonne) {
            $response .= '["';

            $j = 0;
            $nbColumn = count($columns);
            foreach ($columns as $key => $column) {
                // var_dump($key);
                // var_dump($column);
                //var_dump($column['name']);

                // In all cases where something does not exist or went wrong, return -
                $responseTemp = "-";



                switch ($column['name']) {
                    case 'phone': {
                            $phone = $abonne->getPHONE();

                            // Do this kind of treatments if you suspect that the string is not JS compatible
                            $phone = htmlentities(str_replace(array("\r\n", "\n", "\r"), ' ', $phone));

                            // View permission ?
                            if ($this->get('security.authorization_checker')->isGranted('view_town', $abonne)) {
                                // Get the ID
                                $id = $abonne->getId();
                                // Construct the route
                                $url = $this->generateUrl('playground_town_view', array('id' => $id));
                                // Construct the html code to send back to datatables
                                $responseTemp = "<a href='" . $url . "' target='_self'>" . $ref . "</a>";
                            } else {

                                // $data = $phone;

                                // if(  preg_match( '/^\d(\d{3})(\d{9})$/', $data,  $matches ) )
                                // {
                                //     $result = $matches[1] . '-' .$matches[2] . '-' . $matches[3];
                                //     //return $result;
                                //     $responseTemp = $result;
                                // }

                                $from = $phone;
                                $to = sprintf(
                                    "%s %s %s %s %s",
                                    substr($from, 0, 3),
                                    substr($from, 3, 2),
                                    substr($from, 5, 2),
                                    substr($from, 7, 2),
                                    substr($from, 9, 2)
                                    //substr($from, 8)
                                );


                                // $responseTemp = $phone;
                                $responseTemp = $to;
                            }
                            break;
                        }

                    case 'client': {
                            // We know from the class definition that the postal code cannot be null
                            // But if that werent't the case, its value should have been tested
                            // before assigning it to $responseTemp
                            $responseTemp = $abonne->getCLIENT();
                            break;
                        }

                    case 'agence': {
                            $agence = $abonne->getAGENCE();
                            // This cannot happen if inner join is used
                            // However it can happen if left or right joins are used
                            // if ($agence !== null)
                            // {
                            // $responseTemp = $department->getName();
                            // }
                            $responseTemp = $agence;
                            break;
                        }
                    case 'compte': {
                            $compte = $abonne->getCOMPTE();
                            // if ($department !== null)
                            // {
                            //     $region = $department->getRegion();
                            //     if ($region !== null)
                            //     {
                            //         $responseTemp = $region->getName();
                            //     }
                            // }
                            $responseTemp = $compte;
                            break;
                        }
                    case 'actif': {
                            $compte = $abonne->getACTIF();
                            if ($compte == '1') {
                                $compte = 'A';
                            } else {
                                $compte = 'D';
                            }
                            // if ($department !== null)
                            // {
                            //     $region = $department->getRegion();
                            //     if ($region !== null)
                            //     {
                            //         $responseTemp = $region->getName();
                            //     }
                            // }
                            $responseTemp = $compte;
                            break;
                        }
                }

                // Add the found data to the json
                $response .= $responseTemp;

                if (++$j !== $nbColumn)
                    $response .= '","';
            }

            $response .= '"]';

            // Not on the last item
            if (++$i !== $selected_objects_count)
                $response .= ',';
        }

        $response .= ']}';

        // Send all this stuff back to DataTables
        $returnResponse = new JsonResponse();
        $returnResponse->setJson($response);

        return $returnResponse;




        // return $this->render('abonnement/list.html.twig', [
        // ]);

    } //listAjax



    /**
     * @Route("/list/ajax2", name="abonnement_list_ajax2", methods={"POST","GET"})   
     */
    public function listAjax2(Request $request, AbonnementRepository $abonnementRepository): Response
    {

        $get = $request->query->all();

        $columns = array('username', 'tel', 'dateSouscription');
        $columns = array('username', 'tel', 'dateSouscription', 'id');
        $columns = array('u.username', 'u.tel', 'u.dateSouscription', 'u.id');
        $columns = array('u.username', 'u.tel', 'u.dateSouscription', 's.dateReception');
        $columns = array('u.username', 'u.tel', 's.dateDebut', 's.dateReception');
        $columns = array('u.PHONE', 'u.CLIENT');

        $get['columns'] = &$columns;


        $rResult = $abonnementRepository->ajaxTable_v2($get, true)->getArrayResult();


        $output = array(
            "sEcho" => intval($get['sEcho']),
            "iTotalRecords" => $abonnementRepository->nbre(),
            "iTotalDisplayRecords" => $abonnementRepository->getFilteredCount_v2($get),
            "aaData" => array()
        );

        foreach ($rResult as $aRow) {


            $row = array();
            //$nv_colums = array();
            for ($i = 0; $i < count($columns); $i++) {

                // $nv_colums[$i]=substr($columns[$i],2, strlen($columns[$i]));
                $posPint = strripos($columns[$i], '.');
                $nv_colums[$i] = substr($columns[$i], $posPint + 1, strlen($columns[$i]));


                if ($nv_colums[$i] == "version") {
                    $row[] = ($aRow[$nv_colums[$i]] == "0") ? '-' : $aRow[$nv_colums[$i]];
                } elseif ($nv_colums[$i] == "dateSouscription" || $nv_colums[$i] == "dateReception" || $nv_colums[$i] == "dateDebut") {
                    if ($aRow[$nv_colums[$i]] != null) {
                        $row[] = $aRow[$nv_colums[$i]]->format('d-m-Y H:i');
                    } else {
                        $row[] = null;
                    }
                } elseif ($nv_colums[$i] != ' ') {
                    $row[] = $aRow[$nv_colums[$i]];
                }
            }

            $output['aaData'][] = $row;
        } //for

        unset($rResult);

        return new Response(
            json_encode($output)
        );
    } //listAjax2


    /**
     * @Route("/list/load/ajax", name="abonnement_load_ajax", methods={"POST","GET"})   
     */
    public function loadAjax(Request $request, AbonnementRepository $abonnementRepository): Response
    {

        $api_directory = $this->getParameter('api_directory');
        // dump($api_directory);


        // dossier courant
        // dump(getcwd());

        chdir($api_directory);

        // dossier courant
        // dump(getcwd());

        $api_load_abonne = $this->getParameter('api_load_abonne');
        // dump($api_load_abonne);
        $response = exec('php -f ' . $api_load_abonne);



        // $ch = curl_init();

        // // curl_setopt($ch, CURLOPT_URL, "http://192.168.5.22/vireprel/public/api/loadCompte.php");
        // curl_setopt($ch, CURLOPT_URL, "http://loadAbonnements.php");
        // curl_setopt($ch, CURLOPT_HEADER, 0);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // // Récupération de l'URL et affichage sur le navigateur
        // $response = curl_exec($ch);

        // // Fermeture de la session cURL
        // curl_close($ch);

        // var_dump($response);
        // exit("<br/>\n-------quitter");


        // return new JsonResponse(
        //     $response
        // );

        return new Response(
            $response
        );


        // $returnResponse = new JsonResponse();
        // $returnResponse->setJson($response);
        // return $returnResponse;


    } //loadAjax

    /**
     * @Route("/list/nbre/ajax", name="abonnement_nbre_ajax", methods={"POST","GET"})   
     */
    public function nbreAjax(Request $request, AbonnementRepository $abonnementRepository): Response
    {
        $nbre = $abonnementRepository->nbre();
        // print($nbre);
        $response = $nbre;

        // exit("<br/>\n----quitter-----");

        return new Response(
            $response
        );


        // $returnResponse = new JsonResponse();
        // $returnResponse->setJson($response);
        // return $returnResponse;

    } //nbreAjax

    /**
     * @Route("/list/activer/all/ajax", name="abonnement_activer_all_ajax", methods={"POST","GET"})   
     */
    public function activerAllAjax(Request $request, AbonnementRepository $abonnementRepository): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        
        $abonnements = $abonnementRepository->findAll();

        foreach($abonnements as $a){
            $a->setACTIF(1);
        }
         $entityManager->flush();


        //  $queryBuilder = $entityManager->createQueryBuilder();
        // $queryBuilder
        // ->update('Abonnement', 'u')
        // ->set('u.ACTIF', '0')
        // //->where($queryBuilder->expr()->eq('u.id', ':userId'))
        // ;


        // $nbre = 
        // $abonnementRepository->activer();
        // print($nbre);
        // $response = $nbre;
        $response ='OK';

        // exit("<br/>\n----quitter-----");

        return new Response(
            $response
        );


        // $returnResponse = new JsonResponse();
        // $returnResponse->setJson($response);
        // return $returnResponse;

    } //activerAllAjax


    /**
     * @Route("/list/desactiver/all/ajax", name="abonnement_desactiver_all_ajax", methods={"POST","GET"})   
     */
    public function desactiverAllAjax(Request $request, AbonnementRepository $abonnementRepository): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        
        $abonnements = $abonnementRepository->findAll();

        foreach($abonnements as $a){
            $a->setACTIF(0);
        }
         $entityManager->flush();

        $response ='OK';


        return new Response(
            $response
        );

        // $returnResponse = new JsonResponse();
        // $returnResponse->setJson($response);
        // return $returnResponse;

    } //desactiverAllAjax



    /**
     * @Route("/new", name="abonnement_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // $compte = null;

        // if ($request->getMethod() == 'POST') {
        //     // dump($request->request->all());
        //     // dump($request->request->get('abonnement')['compte']);
        //     // dump($request->query->get('abonnement[compte]'));
        //     // exit("<br/>\n------quitter");
        //     // $str = $request->request->get('abonnement[compte]');

        //     $str = $request->request->get('abonnement')['compte'];
        //     $compte = $compteRepository->findOneBy($str);
        // }

        $abonnement = new Abonnement();

        // $str = $request->request->get('abonnement')['compte'];
        // $compte = $this->compteRepository->findOneBy([
        //     'COMPTE' => $str
        // ]);
        // // var_dump($compte);
        // // dump($compte);
        // // exit("<br/>\n------quitter");

        // $abonnement->setCompte($compte);
        // $abonnement->setCompte(null);


        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $abonnement->setIsActif(true);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('abonnement_index');
        }

        # liste des comptes clients
        $comptes = $this->compteRepository->findAll();


        // dump($form);
        // exit("<br/>\n------quitter");

        return $this->render('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
            'in_abonnement' => 'show',
            'comptes' => $comptes,
        ]);
    }


    // public function newAjaxOl(Request $request) : Response
    /**
     * @Route("/new/ajax", name="abonnement_new_ajax", methods={"GET","POST"})
     */
    // public function newAjaxOLd(Request $request, LoggerInterface $logger, MyFonction $fct) : Response
    public function newAjax(Request $request, LoggerInterface $logger): Response
    {




        $output = array(
            'status' => 'NOK',
            'id' => 0,
            'phone' => 0,
            'statut' => 0,
            'detail' => 0,
            'modif' => 0,
            'suppr' => 0
        );


        // dump($request->query->all());
        // dump($request->request->all());

        $abonnement = new Abonnement();

        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        // if ($form->isValid()) {
        if ($request->isXmlHttpRequest()) {


            $abonnement->setIsActif(true);
            $abonnement->setActivePar($this->getUser());
            $abonnement->setCreePar($this->getUser());

            //$phone = $this->fct->getTel($abonnement->getPHONE());
            //$abonnement->setPhone($phone);



            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($abonnement);

            try {
                //code...
                $entityManager->flush();

                $output = array(
                    'status' => 'OK',
                    'id' => $abonnement->getId(),
                    'phone' => $abonnement->getPHONE(),
                    'statut' => $abonnement->getIsActif(),
                    'detail' => $abonnement->getId(),
                    'modif' => $abonnement->getId(),
                    'suppr' => $abonnement->getId()
                );
            } catch (\Throwable $th) {
                //throw $th;
                $output = array(
                    'status' => 'NOK',
                    'id' => 0,
                    'phone' => 0,
                    'statut' => 0,
                    'detail' => 0,
                    'modif' => 0,
                    'suppr' => 0,
                    'message' => $th->getMessage()
                );
            }

            // return $this->redirectToRoute('abonnement_index');


            //### envoyer un message de confirmation à l'abonné


            return new Response(json_encode($output));
        }

        return new Response(json_encode(array('status' => 'NOK')));
    }

    /**
     * @Route("/{id}/show", name="abonnement_show", methods={"GET"})
     */
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
            'in_abonnement' => 'show'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="abonnement_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Abonnement $abonnement): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('abonnement_index', [
                'id' => $abonnement->getId(),
            ]);
        }

        return $this->render('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form->createView(),
            'in_abonnement' => 'show'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="abonnement_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Abonnement $abonnement): Response
    {
        if ($this->isCsrfTokenValid('delete' . $abonnement->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('abonnement_index');
    } //delete

    /**
     * @Route("/{id}/delete/ajax", name="abonnement_delete_ajax", methods={"POST"}) 
     */
    public function deleteAjax(Request $request, Abonnement $abonnement): Response
    {
        $output = [
            'result' => 'NOK',
            'message' => 'Suppression echouée'
        ];
        if ($request->getMethod() == 'POST') {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->remove($abonnement);
                $entityManager->flush();
                $output['result'] = 'OK';
                $output['message'] = 'Suppression effectuée avec succès';
            } catch (\Throwable $th) {
                $output['message'] = $th->getMessage();
            }
        }

        return new JsonResponse($output);
        //$this->redirectToRoute('abonnement_index');
    } //deleteAjax


    /**
     * @Route("/{id}/desactiver/ajax", name="abonnement_desactiver_ajax", methods={"POST"}) 
     */
    public function desactiverAjax(Request $request, Abonnement $abonnement): Response
    {
        $output = [
            'result' => 'NOK',
            'message' => 'Désactivation echouée'
        ];

        if ($request->getMethod() == 'POST') {
            $entityManager = $this->getDoctrine()->getManager();

            if ($abonnement->getIsActif()) {
                $abonnement->setDesactivePar($this->getUser());
            } else {
                $abonnement->setActivePar($this->getUser());
            }

            $abonnement->setIsActif(!$abonnement->getIsActif());


            try {
                //$entityManager->remove($abonnement);


                $entityManager->flush();

                $output['result'] = 'OK';
                $output['message'] = 'Désactivation effectuée avec succès';
                $output['actif'] = ($abonnement->getIsActif());
            } catch (\Throwable $th) {
                $output['message'] = $th->getMessage();
            }
        }

        return new JsonResponse($output);
        //$this->redirectToRoute('abonnement_index');
    } //desactiverAjax

}//class
