<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Form\MessageFileType;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Filesystem\Filesystem;

use App\Service\MySpreadsheet;
use App\Service\MyFonction;



/**
 * @Route("/message")
 *
 * Require ROLE_ADMIN for *every* controller method in this class.
 * @IsGranted("ROLE_MARKETING")
 *
 */

class MessageController extends AbstractController
{

    private $security;
    public $fileSystem;
    private $mySpreadsheet;
    private $myFonction;

    public function __construct(Security $security, Filesystem $fileSystem, MySpreadsheet $mySpreadsheet, MyFonction $myFonction)
    {
        $this->security = $security;
        $this->fileSystem = $fileSystem;
        $this->mySpreadsheet = $mySpreadsheet;
        $this->myFonction = $myFonction;

        // if ($this->security->isGranted('ROLE_SALES_ADMIN')) {
        //     $salesData['top_secret_numbers'] = rand();
        // }
    }

    /**
     * @Route("/", name="message_index", methods={"GET"})
     */
    public function index(MessageRepository $messageRepository): Response
    {
        #$this->denyAccessUnlessGranted('ROLE_MARKETING');
        $this->denyAccessUnlessGranted('ROLE_MARKETING', null, 'ACCES REFUSE AU PROFIL');

        return $this->render('message/index.html.twig', [
            'messages' => $messageRepository->findAll(),
            'in_message' => 'show'
        ]);
    }

    /**
     * @Route("/new", name="message_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $now = new \DateTime('NOW', new \DateTimeZone('UTC'));

            // dump($message->getIsDiffere());

            if ($message->getIsDiffere()) {
                // dump($message->getDateDiffu());
                // exit("<br/>\n ------- differe---------");
                // 2019 - 01 - 20 17  : 30  :00.0
                // $str = $message->getDateDiffu()->format('Y-m-d H:i:s');
                $maDate = new \DateTime(
                    $message->getDateDiffu()->format('Y-m-d H:i:s'),
                    new \DateTimeZone('UTC')
                );
            } else {
                $maDate = $now;
            }

            $message->setEditepar($this->getUser());
            $message->setValidepar(null);

            $message->setDatediffu($maDate);

            $message->setDatedit($now);

            $message->setDatevalid1(null);
            $message->setDatevalid2(null);

            $message->setIsValid1(false);
            $message->setIsValid2(false);

            $message->setIsDiffu(false);

            // dump($message);
            // dump($maDate);
            // exit("<br/>\n ------- differe---------");


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('message_index');
        }

        return $this->render('message/new.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
            'in_message' => 'show'
        ]);
    }


    /**
     * @Route("/new/file", name="message_new_file", methods={"GET","POST"})
     */
    public function newFile(Request $request): Response
    {
        $session = $request->getSession();

        $message = new Message();
        $form = $this->createForm(MessageFileType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $now = new \DateTime('NOW', new \DateTimeZone('UTC'));
            $now_str = $now->format('YmdHis');

            $uploads_directory = $this->getParameter('uploads_directory');

            if (!$this->fileSystem->exists($uploads_directory)) {
                try {
                    $this->fileSystem->mkdir($uploads_directory, 0777);
                } catch (IOExceptionInterface $exception) {
                    //echo "Une erreur s'est produite en creant le dossier ". $exception->getPath();
                    $msg = $exception->getMessage();
                    $session->getFlashBag()->add('alert-danger', $msg);
                }
            } //if


            $file = $form['attachment']->getData();

            $guessExtension = $file->guessExtension();
            var_dump($guessExtension);
            $clientOriginalExtension = $file->getClientOriginalExtension();
            var_dump($clientOriginalExtension);
            $mimeType = $file->getMimeType();
            var_dump($mimeType);
            $clientMimeType = $file->getClientMimeType();
            var_dump($clientMimeType);
            $clientOriginalName = $file->getClientOriginalName();
            var_dump($clientOriginalName);
            $clientSize = $file->getClientSize();
            var_dump($clientSize);
            $maxFileSize = $file->getMaxFilesize();
            var_dump($maxFileSize);
            $error = $file->getError();
            var_dump($error);
            $isValid = $file->isValid();
            var_dump($isValid);

            //renommer le fichier en ajoutant de la date
            $clientOriginalNameRename = pathinfo($clientOriginalName, PATHINFO_FILENAME) . '_' . $now_str . '.' . $clientOriginalExtension;
            var_dump($clientOriginalNameRename);

            // dump($file);
            // exit("<br/>\n--------------quitter");

            if (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                // print"<br/>\n format autorisé: $mimeType";

                $entityManager = $this->getDoctrine()->getManager();

                $tab_info = $this->mySpreadsheet->readExcelFileSMS($file);

                // dump($tab_info);
                // exit("<br/>\n ---------quitter--------");

                $details = $tab_info['detail'];


                foreach ($details as $key => $d) {

                    $messageFile = new Message();


                    $nom = $d['numSeq'];
                    $tel = $d['nomBenef'];
                    $tel = $this->myFonction->getTel($tel);
                    $contenu = $d['libeBnq'];

                    if(strlen($contenu) >='160'){
                        $msg = "Un message contient ".strlen($contenu) ." caractères";
                        $session->getFlashBag()->add('alert-danger', $msg);
                        break 1;
                    }

                    $messageFile->setTel($tel);
                    $messageFile->setContenu($contenu);

                    $messageFile->setEditepar($this->getUser());
                    $messageFile->setValidepar(null);

                    // $messageFile->setTitre( substr($contenu,0,10) );
                    $messageFile->setTitre( $message->getTitre() );

                    $messageFile->setDatedit($now);

                    $messageFile->setDatevalid1(null);
                    $messageFile->setDatevalid2(null);

                    $messageFile->setIsValid1(false);
                    $messageFile->setIsValid2(false);

                    $messageFile->setIsDiffu(false);

                    if ($message->getIsDiffere()) {
                        // dump($message->getDateDiffu());
                        // exit("<br/>\n ------- differe---------");
                        // 2019 - 01 - 20 17  : 30  :00.0
                        // $str = $message->getDateDiffu()->format('Y-m-d H:i:s');
                        $maDate = new \DateTime(
                            $message->getDateDiffu()->format('Y-m-d H:i:s'),
                            new \DateTimeZone('UTC')
                        );
                    } else {
                        $maDate = $now;
                    }
                    $messageFile->setDatediffu($maDate);

                    $messageFile->setIsDiffere($message->getIsDiffere());

                    $entityManager->persist($messageFile);


                }//for details

                // foreach

                // dump($message);
                // exit("<br/>\n ---------quitter--------");


                    // $entityManager->persist($message);
                    $entityManager->flush();

                    return $this->redirectToRoute('message_index');

                } //mimeTpe



                $file->move($uploads_directory, $clientOriginalNameRename);
                var_dump($file);
                exit("<br/>\n ------- differe---------");


            }

                return $this->render('message/new_file.html.twig', [
                    'message' => $message,
                    'form' => $form->createView(),
                    'in_message' => 'show'
                    ]);
                }


    /**
     * @Route("/{id}/show", name="message_show", methods={"GET"})
     */
    public function show(Message $message): Response
    {
        return $this->render('message/show.html.twig', [
            'message' => $message,
            'in_message' => 'show'
        ]);
    }

    /**
     * @Route("/{id}/edit", name="message_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Message $message): Response
    {
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('message_index', [
                'id' => $message->getId(),
            ]);
        }

        return $this->render('message/edit.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
            'in_message' => 'show'
        ]);
    }

    /**
     * @Route("/{id}/delete", name="message_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Message $message): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_index');
    }

    /**
     * @Route("/{id}/delete/ajax", name="message_delete_ajax", methods={"POST"})
     */
    public function delete_ajax(Request $request, Message $message): Response
    {
        $output = [];

        // if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
        if ($request->getMethod() == 'POST') {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($message);
            $entityManager->flush();

            $output = ['result' => 'SUPPRESSION EFFECTUEE AVEC SUCCES'];
        }

        // return $this->redirectToRoute('message_index');
        return new JsonResponse($output);
    }


    /**
     * @Route("/{id}/valide", name="message_valide", methods={"DELETE"})
     */
    public function valide(Request $request, Message $message): Response
    {
        $session = $request->getSession();
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {

            // $this->denyAccessUnlessGranted('ROLE_MARKETING', null, 'ACCES REFUSE AU PROFIL');
            $now = new \DateTime('NOw', new \DateTimeZone('UTC'));

            if ($message->getIsValid1()) {

                // exit("<br/>\n---quitter------------");

                if ($message->getIsValid2()) { } else {

                    if ($message->getEditepar() == $this->getUser()) {
                        $messageText = "LA VALIDATION DOIT ETRE FAITE PAR UNE AUTRE PERSONNE";
                        $session->getFlashBag()->add('alert-danger', $messageText);
                        return $this->redirectToRoute('message_show', ['id' => $message->getId()]);
                    }

                    $message->setIsValid2(true);
                    $message->setDateValid2($now);
                    $message->setValidepar($this->getUser());
                }
            } else {
                $message->setIsValid1(true);
                $message->setDateValid1($now);
            }

            // $message->setIsValid1(true);
            // $message->setDateValid1($now);



            $entityManager = $this->getDoctrine()->getManager();
            //$entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('message_index');
    }//valider

    /**
    * @Route("/tousvalidermessage/ajax", name="tousvalidermessage", methods={"POST"})
    */
    public function tousvalidermessage_ajax(Request $request, MessageRepository $messageRepository): Response
    {
         $output = [];

         $session = $request->getSession();

        // if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->request->get('_token'))) {
        if ($request->getMethod() == 'POST') {

            //liste des messages
            $messages = $messageRepository->findBy([
                'isValid2' => false,
                // 'editepar' => $this->getUser(),
                ],[
                    'id'=>'ASC'
                ]
            );

            $now = new \DateTime('NOw', new \DateTimeZone('UTC'));
            $entityManager = $this->getDoctrine()->getManager();

            foreach($messages as $message){

                if ($message->getIsValid1()) {

                    // exit("<br/>\n---quitter------------");

                    if ($message->getIsValid2()) { } else {

                        if ($message->getEditepar() == $this->getUser()) {
                            $messageText = "LA VALIDATION DOIT ETRE FAITE PAR UNE AUTRE PERSONNE";
                            $session->getFlashBag()->add('alert-danger', $messageText);
                            // return $this->redirectToRoute('message_show', ['id' => $message->getId()]);
                             $output = ['result' => $messageText];
                        }

                        $message->setIsValid2(true);
                        $message->setDateValid2($now);
                        $message->setValidepar($this->getUser());
                    }
                } else {
                    $message->setIsValid1(true);
                    $message->setDateValid1($now);
                }
                $output = ['result' => 'VALIDATION EFFECTUEE'];

            }//for

            // $messages = $messageRepository->findAll();

            // var_dump($messages); 
            

            // $entityManager->remove($message);
            $entityManager->flush();

        }

        // return $this->redirectToRoute('message_index');
        return new JsonResponse($output);

    }//tousvalidermessage


}//class
