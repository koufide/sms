<?php

/**
 * ENVOYER DES SMS  DIFFERES A DES NUMEROS , MEME SANS ABONNEMENT.
 * LE FORMAT DU FICHIER ET SON NOM FAIT TOUTE LA DIFFERENCE
 * 
 * 20/03/2020
 * Fidelin KOUAME
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

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




//var_dump(base64_encode("$user:$pass"));
$url = 'http://192.168.200.42:8280/pushsms/v1.0.0/sendSms_notification';
$bearer = 'b45f3329-0d6c-3d17-903f-4049726fc077';
//---------------------------------------------------



// $chemin = getcwd();
$chemin = API_REP;
// print "<br/>\n chemin: $chemin <br/>\n";
//exit("<br/>\n----------quit");

require_once($chemin . '/MyPDO.php');
// require_once($chemin . '/api/MyPDO.php');
// require_once('/var/www/html/sms/api/' . 'MyPDO.php');

$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();


$param = simplexml_load_file($chemin_params);

$file_alert_log = $param->files->file_alert_log;


$log_rep = $param->reps->log_rep;

$file_lock_push_sms = $param->files->file_lock_dpush_sms;
$file_lock_push_sms = $log_rep . $file_lock_push_sms;

$uti = $param->sms->user;
$mdp = $param->sms->mdp;
// var_dump("$uti $mdp");


// D'autres manières d'appeler error_log():
#error_log("TEST !", 3, $log_rep . $file_alert_log);



if (file_exists($file_lock_push_sms)) {
    exit("<br/>\n exit! traitement encours...");
} else {
    $myfile = fopen("$file_lock_push_sms", "w");
}

// exit("<br/>\n --------quitter manuellement---------");




$ftp_outgoing_rep = $param->reps->ftp_outgoing;
$push_rep = $ftp_outgoing_rep;


$arch_outgoing = $param->reps->arch_outgoing;


if (!is_dir($arch_outgoing)) {
    mkdir($arch_outgoing);
}

$trouve = false;


if ($dossier = opendir($push_rep)) {
    while (false !== ($fichier = readdir($dossier))) {
        if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
            // var_dump($fichier);
            $pathinfo = pathinfo($fichier);
            // print "<br/>\n pathinfo : = ";
            //var_dump($pathinfo);

            $ext = $pathinfo['extension'];
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];

            // exit("<br/>\n --------quitter manuellement---------");


            if (preg_match("/^DPUSH/i", $filename)) {


                $now = new DateTime('NOW', new DateTimeZone(('UTC')));
                print "<br/><br/><br/>\n\n\n ========== EXECUTION DIFFERE PUSH SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s');


                $trouve = true;


                print "<br/>\n echo trouve ==> $filename";


                # 25102019 koufide / chercher une erreur dans le fichier
                if (strpos(file_get_contents("$push_rep/$fichier"), 'ORA-') !== false) {

                    #deplacer le fichier
                    rename($push_rep  . $fichier, $arch_outgoing . 'error2_' . $filename . '.' . $ext);
                    ##############################################
                    $res_send = $fct->sendSMS('22509327626', $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);
                } else {

                    $handle = fopen("$push_rep/$fichier", "r");
                    if ($handle) {

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

                                    // var_dump($tab_line);

                                    $code = $tab_line[0];
                                    //$compte = $tab_line[1];
                                    $message = $tab_line[1];
                                    $tel = $tab_line[2];
                                    // $message = $fct->supprimerRetourChariot($message);
                                    $message = $fct->replaceSpecialChar($message);

                                    $schedule = $tab_line[3];


                                    print "<br/>\n  code:  $code";
                                    //print "<br/>\n  compte:   $compte";
                                    print "<br/>\n  message:   $message";
                                    print "<br/>\n new tel:   $tel";
                                    print "<br/>\n  schedule:  $schedule";

                                    //-----------------------------------------------------------------
                                    $res_flooding = $fct->flooding($tel, $message,  $now->format('d/m/Y'));

                                    if ($res_flooding == 0) {

                                        $tab_messages = [];
                                        $destinations = [
                                            "to" => $tel,
                                            "messageId" => $fct->getMessageId()
                                        ];

                                        $tab_destinations = [];
                                        $tab_destinations['from'] = $fct->getFrom();
                                        $tab_destinations['destinations'] = $destinations;
                                        $tab_destinations['text'] = $message;
                                        $tab_destinations['flash'] = $fct->getFlash(); //oui <==  / par defaut non 
                                        $tab_destinations['language'] = ["languageCode" => $fct->getLanguageCode()];
                                        $tab_destinations['transliteration'] = $fct->getTransliteration();
                                        $tab_destinations['sendAt'] = $schedule;

                                        $tmp_destinations[] = $tab_destinations;


                                        $tab_messages["bulkId"] = $fct->getBulkId();
                                        $tab_messages["messages"] = $tmp_destinations;


                                        if (!empty($tab_messages)) {
                                            $applic = 'CGB';
                                            $res = $fct->sendMultiSMStoMultiDestScheduleInfo($tab_messages, $applic);
                                        }
                                    } else {

                                        print "<br/>\n Le message [$message] a deja ete envoye au numero [$tel]";
                                    } // if($res_flooding == 0){

                                    //-----------------------------------------------------------------
                                } //if(!empty($tab_line)){


                            } // if ($line != "." && $line != "..") {

                        } //while (($line = fgets($handle)) !== false) {

                        fclose($handle);


                        print "<br/>\n  traite: $i <br/>\n<br/>\n";
                    } else { // if ($handle) {
                        print "<br/>\n erreur ouverture du fichier ";
                    }

                    //deplacer le fichier
                    rename($push_rep . '/' . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);
                }  // if (strpos(file_get_contents("$push_rep/$fichier"), 'ORA-') !== false) {


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
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


unlink($file_lock_push_sms);

if ($trouve) {
    // $now = new DateTime('NOW', new DateTimeZone(('UTC')));
    // print "<br/><br/><br/>\n\n\n ========== EXECUTION DIFFERE PUSH SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s');
    $out1 = ob_get_contents();
    $myfile = file_put_contents('log/d2_pushsms_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
}
