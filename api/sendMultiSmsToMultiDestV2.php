<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


$chemin = getcwd();
print "<br/>\n chemin: $chemin <br/>\n";

require_once($chemin . '/MyPDO.php');

$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));


$push_rep = '/home/ftpuser3/outgoing';
$arch_outgoing = '/home/ftpuser3/outgoing/archive';





$tab_messages = [];
$final_messages = [];
$gblobal_messages = [];


if ($dossier = opendir($push_rep)) {
    while (false !== ($fichier = readdir($dossier))) {
        if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
            var_dump($fichier);
            $pathinfo = pathinfo($fichier);
            var_dump($pathinfo);
            $ext = $pathinfo['extension'];
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];


            if (preg_match("/^PUSH/i", $filename)) {
                print "<br/>\n echo trouve ==> $filename";


                $handle = fopen("$push_rep/$fichier", "r");
                if ($handle) {


                    while (($line = fgets($handle)) !== false) {


                        if ($line != "." && $line != "..") {


                            $tab_message = [];

                            // process the line read.
                            var_dump("fichier $fichier line : " . $line);


                            $tab_line = explode('#', $line);
                            var_dump($tab_line);

                            if (!empty($tab_line)) {
                                //var_dump($tab_line);

                                $code = $tab_line[0];
                                $compte = $tab_line[1];
                            #$tel = $tab_line[2];
                                $message = $tab_line[2]; 
    
                            ## recuperer l abonnement  => telephone
                            // $sql = "SELECT c.compte, a.* FROM abonnement a JOIN compte c WHERE c.id=a.compte_id AND c.compte = :compte";
                                $sql = "SELECT a.phone FROM abonnement a JOIN compte c WHERE c.id=a.compte_id AND c.compte = :compte";
                                $stmt = $dbh->prepare($sql);
                                $stmt->execute(
                                    [':compte' => $compte]
                                );
                            // $abonnements = $stmt->fetchAll();
                                $abonnements = $stmt->fetch();
                                print "<pre>";
                                var_dump($abonnements);
                                $tel = $abonnements['phone'];


                                if (!$tel) {
                                    print "<br/>\n------PAS D ABONNEMENT POUR CE NUMERO tel: $tel";
                                } else {
                                    print "<br/>\n------ tel: $tel";
                                    $tel = $fct->getTel($tel);

                                    //par default prendre mon numero pour test
                                // $tel = '03612783';
                                // $message = uniqid();


                                    $destinations = [];

                                    $destinations['to'] = $tel;
                                    $destinations['messageId'] = $fct->getMessageId();


                                   

                                    //-----------enregistrer le message------------
                                    $data = [];

                                        //$data['bulkId'] = $json_response->bulkId;

                                    $data['a'] = $tel;
                                    $data['de'] = $fct->getFrom();
                                    $data['text'] = $message;
                                    $data['messageId'] = $fct->getMessageId();

                                    //$data['messageId'] = $json_response->messageId;
                        
                                        //------------------ insert dans outgoing
                                    $sth = $dbh->prepare('INSERT INTO outgoing (de, a, text, message_id)  
                                        VALUES (:de, :a, :text, :messageId )');
                                    $res = $sth->execute($data);
                                    //-----------------------------------------------------------------


                                    //$data['messageId'] = 'BBG-MESSAGE-ID-' . $res;


                                    $tab_message['text'] = $message;
                                    $tab_message['destinations'] = $destinations;
                                    $tab_message['from'] = $fct->getFrom();
                                    $tab_message['flash'] = false;
                                    $tab_message['languageCode'] = "FR";
                                    $tab_message['transliteration'] = "French";

                                    $tab_messages[] = $tab_message;

                                    $final_messages[] = $tab_messages;

                                    // https ://dev.infobip.com/send-sms/fully-featured-text-message

                                }//tel


                            }//if(!empty($tab_line)){

                        }// if ($line != "." && $line != "..") {


                    }//1ere boucle // pour une ligne

                    fclose($handle);
                } else {
                        // error opening the file.
                }


            } else {
                //print"<br/>\n echo non trouve";
            }
            
            
             #deplacer lz fichier
            ### rename($push_rep . '/' . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);

        }

    }
    closedir($dossier);
} else {
    echo 'Le dossier n\' a pas pu être ouvert';
}





$global_messages['messages'] = $final_messages;
$global_messages['bulkId'] = $fct->getBulkId();


echo "<br/>\n";
print_r($global_messages);
echo "<br/>\n----------";


$json = json_encode($global_messages);
echo "<br/>\n";
print_r($json);
echo "<br/>\n----------";


//exit("<br/>\n--quitter--------");
$fct->sendMultiSMStoMultiDest($global_messages);


//$final_messages['from'] = $fct->getFrom();
$fct->sendMultiSMStoMultiDestV2($global_messages);



