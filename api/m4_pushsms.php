<?php

/**
 * ENVOYER DES SMS A DES NUMEROS , MEME SANS ABONNEMENT
 * 23/02/2020
 * Fidelin KOUAME
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION MANUAL PUSH 4 SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s');

define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin_params = 'params.xml';

if (!file_exists($chemin_params)) {
    exit("<br/>\n chemin : $chemin_params introuvable");
}


//---------------------------------------------------
/**
 * Array
(
    [uti] => cgb
    [applic] => orion
    [statut] => 1
    [nbresms] => 0
    [smsenvoye] => 0
)
 pwd ==> 6RBA5WBH

 salt ==> a6074f1ee0f56ad29930d49e44842ce4

 crypte ==> PlLRd21BCSs5Td9O6L/+JnlK6xKm82RbZhvnbQ4jyiifYaM5IN9/uTTKm/u15wSGWend2kkPvyYc4VcJ5PQlug==
 */
// $user = 'bqenligne';
// $pass = 'EILRT4C4';

$user = 'cgb';
$pass = '6RBA5WBH';







$chemin = API_REP;

require_once($chemin . '/MyPDO.php');

$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();


$param = simplexml_load_file($chemin_params);

$file_alert_log = $param->files->file_alert_log;


$log_rep = $param->reps->log_rep;

$file_lock_push_sms = $param->files->file_lock_mpush_sms4;  ### 4 ieme diffusion
$file_lock_push_sms = $log_rep . $file_lock_push_sms;

$uti = $param->sms->user;
$mdp = $param->sms->mdp;
// var_dump("$uti $mdp");




if (file_exists($file_lock_push_sms)) {
    exit("<br/>\n exit! traitement encours...");
} else {
    $myfile = fopen("$file_lock_push_sms", "w");
}




$ftp_outgoing_rep = $param->reps->ftp_outgoing;
$push_rep = $ftp_outgoing_rep;


$arch_outgoing = $param->reps->arch_outgoing;




//---------------------------------------------------
$url = $fct->getApiManager_Url() . $fct->getApiManager_Sendsms();
$bearer = $fct->getApiManager_Bearer();
//---------------------------------------------------




if (!is_dir($arch_outgoing)) {
    mkdir($arch_outgoing);
}

$trouve = false;


if ($dossier = opendir($push_rep)) {
    while (false !== ($fichier = readdir($dossier))) {
        if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
            $pathinfo = pathinfo($fichier);

            $ext = $pathinfo['extension'];
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];

            if (preg_match("/^M4PUSH/i", $filename)) {


                $now = new DateTime('NOW', new DateTimeZone(('UTC')));
                print "<br/><br/><br/>\n\n\n ========== EXECUTION MANUAL 4 PUSH SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s');


                $trouve = true;


                print "<br/>\n echo trouve ==> $filename";


                # 25102019 koufide / chercher une erreur dans le fichier
                if (strpos(file_get_contents("$push_rep/$fichier"), 'ORA-') !== false) {

                    #deplacer le fichier
                    rename($push_rep  . $fichier, $arch_outgoing . 'error2_' . $filename . '.' . $ext);
                    
                    ##############################################
                    $res_send = $fct->sendSMS($fct->getMyTel(), $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);


                    # ajoute le 23102019 koufide. permet de continuer les traitements apres une erreur
                    #supprimer le lock
                    // unlink($file_lock_charge_sms);
                } else {


                    $handle = fopen("$push_rep/$fichier", "r");
                    if ($handle) {

                        $user = 'cgb';
                        $pw = '6RBA5WBH';

                        # authentification
                        $sql = "select * from connexion where uti =:uti and statut = :statut";
                        $data = [
                            'uti' => $user,
                            'statut' => '1',
                        ];

                        $stmt = $dbh->prepare("$sql");
                        $stmt->execute($data);
                        $row = $stmt->fetch();
                        var_dump($row);
                        if (!empty($row)) {

                            $mdp = $row['mdp'];
                            $salt = $row['salt'];
                            $applic = $row['applic'];
                            $nbresms = $row['nbresms'];
                            $smsenvoye = $row['smsenvoye'];

                            $crypte = $fct->crypterPassword($pw, $salt);

                            if ($mdp !== $crypte) {
                                $retourErreur = '1';
                                $retourMessage = "Mot de passe [$pw] incorrect";
                            } else {

                                $i = 0;
                                while (($line = fgets($handle)) !== false) {

                                    if ($line != "." && $line != "..") {

                                        print "<br/>\n line dans le fichier : => ";
                                        var_dump("fichier $fichier line : " . $line);


                                        $tab_line = explode('#', $line);
                                        print "<br/>\n tab_line : => ";
                                        var_dump($tab_line);

                                        if (!empty($tab_line)) {

                                            $i++;

                                            $message = $tab_line[0];
                                            $tel = $tab_line[1];
                                            $message = $fct->replaceSpecialChar($message);

                                            print "<br/>\n  message:   $message";
                                            print "<br/>\n new tel:   $tel";

                                            $to = $tel;
                                            $from = $fct->getFrom();
                                            $applic = 'CGB';
                                            $text = $message;

                                            $res_flooding = $fct->flooding($to, trim($text), $now->format('d/m/Y'));
                                            print("res_flooding: $res_flooding");

                                            if ($res_flooding == 0) {

                                                $tab_messages = [
                                                    'bulkId' => $fct->getBulkId(strtoupper(substr($applic, 0, 3))),
                                                    'messages' => [
                                                        'from' =>  $from,
                                                        'destinations' => [
                                                            "to" => $to,
                                                            "messageId" => $fct->getMessageId(strtoupper(substr($applic, 0, 3)))
                                                        ],
                                                        'text' => $text,
                                                        'flash' => $fct->getFlash(),
                                                        'language' => [
                                                            "languageCode" => $fct->getLanguageCode()
                                                        ],
                                                        'transliteration' => $fct->getTransliteration(),
                                                    ],
                                                ];



                                                #send sms
                                                $res_send = $fct->sendMultiSMStoMultiDestV4($tab_messages);

                                                if ($res_send){
                                                    if (array_key_exists('requestError', json_decode($res_send, true))){
                                                        print("erreur requestError");
                                                        $tab_message = json_decode($res_send, true);
                                                        $messageId = $tab_message['requestError']['serviceException']['messageId'];
                                                        $text = $tab_message['requestError']['serviceException']['text'];
                                                        var_dump($messageId);
                                                        var_dump($text);
                                                        // koufide 25112020
                                                        #deplacer le fichier
                                                        rename($push_rep  . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext); 
                                                        $res_unlink = unlink($file_lock_push_sms);
                                                        // print("res_unlink: $res_unlink");
                                                        // exit("\n<br/>---STOP----");
                                                        
                                                        // koufide 25112020
                                                        $res_send = $fct->sendSMS($fct->getMyTel(), $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext.', '.$messageId.' '.$text);
                                                    }
                                                    else{
                                                        print("no erreur");
                                                        $tab_message = json_decode($res_send, true);

                                                        $bulkid = $tab_message['bulkId'];
                                                        $retour['bulkId'] = $bulkid;
                                                        $retour['from'] =   $from;
                                                        $retour['text'] = $text;

                                                        if (empty($tab_message['messages'])) {
                                                            $retourErreur = '1';
                                                            $retourMessage = 'Echec sendsms';
                                                        } else {

                                                            $colonnes = "smsenvoye = :smsenvoye";
                                                            $conditions = "uti = :uti and mdp = :mdp";
                                                            $data = [
                                                                'uti' => $user,
                                                                'mdp' => $mdp,
                                                                'smsenvoye' => $smsenvoye
                                                            ];

                                                            $sql = "update connexion set $colonnes where $conditions ";

                                                            $stmt = $dbh->prepare($sql);
                                                            $res_update = $stmt->execute($data);
                                                            $count = $stmt->rowCount();

                                                            if ($count == '0') {
                                                                $retourErreur = '1';
                                                                $retourMessage = 'Echec [update] smsenvoye';
                                                            } else {
                                                                // echo "Success !";
                                                            }



                                                            $messages = $tab_message['messages'];

                                                            foreach ($messages as $key => $r_message) {
                                                                $r_to = $r_message['to'];
                                                                $retour['to'] = $r_to;

                                                                $r_status = $r_message['status'];

                                                                $groupId = $r_status['groupId'];
                                                                $retour['statusGroupId'] = $groupId;

                                                                $groupName = $r_status['groupName'];
                                                                $retour['statusGroupName'] = $groupName;

                                                                $id = $r_status['id'];
                                                                $retour['statusId'] = $id;

                                                                $name = $r_status['name'];
                                                                $retour['statusName'] = $name;

                                                                $description = $r_status['description'];
                                                                $retour['statusDescription'] = $description;

                                                                $messageId = $r_message['messageId'];
                                                                $retour['messageId'] = $messageId;


                                                                $data2 = [];
                                                                $data2['bulkId'] = $bulkid;
                                                                $data2['de'] = $from;

                                                                $data2['message_id'] = $messageId;
                                                                $data2['a'] = $r_to;
                                                                $data2['text'] =  $text;

                                                                $data2['status_sendsms'] = $messageId;
                                                                $data2['sendsms_at'] = $now->format('Y-m-d H:i:s');
                                                                $data2['letest'] = $messageId;
                                                                $data2['send_groupid'] = $groupId;
                                                                $data2['send_groupname'] = $groupName;
                                                                $data2['send_id'] = $id;
                                                                $data2['send_name'] = $name;
                                                                $data2['send_description'] = $description;
                                                                $data2['tentative'] = 0;
                                                                $data2['applic'] = strtoupper($applic);



                                                                $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, 
                                                                send_groupname, send_id, send_name, send_description, bulk_id, tentative, applic)
                                                                VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , 
                                                                :send_id , :send_name , :send_description, :bulkId, :tentative, :applic)
                                                                ";

                                                                $stmt = $dbh->prepare($sql);

                                                                try {
                                                                    $res = $stmt->execute($data2);

                                                                    $count = $stmt->rowCount();

                                                                    if ($count == '0') {
                                                                        $retourErreur = '1';
                                                                        $retourMessage = 'Echec [insert] outgoing';
                                                                    } else {
                                                                        $retourErreur = '0';
                                                                        $retourMessage = '';
                                                                    } //if ($count == '0') {
                                                                } catch (PDOException $e) {
                                                                    echo 'Exception reçue : ',  $e->getMessage(), "\n";
                                                                    $res_send = $fct->sendSMS(
                                                                        $fct->getMyTel(),
                                                                        $e->getMessage() . ':' . $filename,
                                                                        true
                                                                    );
                                                                    $res_unlink = unlink($file_lock_push_sms);
                                                                    print("res_unlink: $res_unlink");
                                                                }
                                                            } // foreach ($r_messages as $key => $r_message) {

                                                        } // if(empty( $tab_message['messages'])){
                                                            
                                                    }//key exists
                                                }//if ($res_send){

                                                

                                            } else {
                                                $retourErreur = '1';
                                                $retourMessage = "Le message [$text] a deja ete envoye au numero [$to]";
                                            }

                                        } //if(!empty($tab_line)){
                                    } // if ($line != "." && $line != "..") {

                                } //while (($line = fgets($handle)) !== false) {

                                fclose($handle);

                                print "<br/>\n  traite: $i <br/>\n<br/>\n";
                            } //if ($mdp !== $crypte) {
                        }





                        
                    } else { // if ($handle) {
                        // error opening the file.
                        print "<br/>\n erreur ouverture du fichier ";
                    }


                    // koufide 25112020
                    // #deplacer le fichier
                    // rename($push_rep  . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext); 


                }  // if (strpos(file_get_contents("$push_rep/$fichier"), 'ORA-') !== false) {

                // koufide 25112020
                #deplacer le fichier
                rename($push_rep  . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext); 


            } else { // if (preg_match("/^MPUSH/i", $filename)) {
                print "<br/>\n echo fichier MPUSH non trouve";
            }
        } // if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {

    } //while (false !== ($fichier = readdir($dossier))) {
    closedir($dossier);
} else {
    echo 'Le dossier n\' a pas pu être ouvert';
}

//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


$res_unlink = unlink($file_lock_push_sms);

if ($trouve) {


    $out1 = ob_get_contents();
    $myfile = file_put_contents('log/m4_pushsms_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
}
 