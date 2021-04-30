<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION ALERT ====== <br/>\n" . $now->format('d/m/Y H:i:s');

define("API_REP",     '/var/www/html/sms/api');

// print "<br/>\n current getcwd: " . getcwd();
chdir(API_REP);
// print "<br/>\n after change getcwd: " . getcwd();

// $chemin_params = getcwd() . '/params.xml';
$chemin_params = 'params.xml';

if (!file_exists($chemin_params)) {
    exit("<br/>\n chemin : $chemin_params introuvable");
}


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

$file_lock_push_sms = $param->files->file_lock_push_sms;
$file_lock_push_sms = $log_rep . $file_lock_push_sms;



// D'autres manières d'appeler error_log():
#error_log("TEST !", 3, $log_rep . $file_alert_log);



if (file_exists($file_lock_push_sms)) {
    exit("<br/>\n exit! traitement encours...");
}
// else {
//     $myfile = fopen("$file_lock_push_sms", "w");
// }


// exit("<br/>\n --------quitter manuellement---------");



// $push_rep='/var/www/html/sms/public/outgoing';
// $arch_outgoing='/var/www/html/sms/public/arch_outgoing';

// $push_rep = '/home/ftpuser3/outgoing';
// $push_rep = '/home/ftpuser/ftp/outgoing';

$ftp_outgoing_rep = $param->reps->ftp_outgoing;
$push_rep = $ftp_outgoing_rep;


// $arch_outgoing = '/var/www/html/sms/public/arch_outgoing';
$arch_outgoing = $param->reps->arch_outgoing;




// if (!is_dir($push_rep)) {
//     mkdir($push_rep);
// }

if (!is_dir($arch_outgoing)) {
    mkdir($arch_outgoing);
}

// $messages = array();
$tab_messages = array();
$trouve = false;

// $push_rep = array_diff(scandir($push_rep), array('..', '.'));

if ($dossier = opendir($push_rep)) {
    while (false !== ($fichier = readdir($dossier))) {
        if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
            // var_dump($fichier);
            $pathinfo = pathinfo($fichier);

            // var_dump($pathinfo);

            $ext = $pathinfo['extension'];
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];

            // exit("<br/>\n --------quitter manuellement---------");


            if (preg_match("/^PUSH/i", $filename)) {
                print "<br/>\n echo trouve ==> $filename";

                $handle = fopen("$push_rep/$fichier", "r");
                if ($handle) {

                    $tab_destinations = [];
                    $tmp_destinations = [];

                    while (($line = fgets($handle)) !== false) {

                        if ($line != "." && $line != "..") {
                            // process the line read.
                            // var_dump("fichier $fichier line : " . $line);


                            $tab_line = explode('#', $line);
                            // var_dump($tab_line);

                            if (!empty($tab_line)) {
                                // var_dump($tab_line);

                                $code = $tab_line[0];
                                $compte = $tab_line[1];
                                #$tel = $tab_line[2];
                                $message = $tab_line[2];

                                ## recuperer l abonnement  => telephone
                                // $sql = "SELECT c.compte, a.* FROM abonnement a JOIN compte c WHERE c.id=a.compte_id AND c.compte = :compte";
                                // $sql = "SELECT a.phone FROM abonnement a JOIN compte c WHERE c.id=a.compte_id AND c.compte = :compte AND a.is_actif = 1";
                                $sql = "SELECT a.phone FROM abonnement a JOIN compte c WHERE c.compte=a.compte AND c.compte = :compte AND a.actif = 1";
                                $stmt = $dbh->prepare($sql);
                                $stmt->execute(
                                    [':compte' => $compte]
                                );

                                // var_dump($compte);
                                // exit("<br/>\n----------quitter----------------");
                                // $abonnements = $stmt->fetchAll();
                                $abonnements = $stmt->fetch();
                                // print "<pre>";
                                // var_dump($abonnements);
                                $tel = $abonnements['phone'];
                                // print "<br/>\n ------ tel: $tel  <br/>\n<br/>\n";
                                // exit("<br/>\n----------quitter----------------");


                                if ($tel) {
                                    $tel = $fct->getTel($tel);
                                    #if ($tel == '22503612783') ################################################################################
                                    #{
                                    $trouve = true;
                                    // print "<br/>\n ------ tel: $tel  <br/>\n<br/>\n";
                                    // exit("<br/>\n----------quitter----------------");




                                    $destinations = [
                                        "to" => $tel,
                                        "messageId" => $fct->getMessageId()
                                    ];
                                    // $tmp_destinations[] = $destinations;

                                    // https://dev.infobip.com/send-sms/fully-featured-text-message

                                    $tab_destinations['from'] = $fct->getFrom();
                                    $tab_destinations['destinations'] = $destinations;
                                    $tab_destinations['text'] = $message;
                                    $tab_destinations['flash'] = $fct->getFlash();
                                    $tab_destinations['language'] = ["languageCode" => $fct->getLanguageCode()];
                                    $tab_destinations['transliteration'] = $fct->getTransliteration();
                                    // $tab_destinations['intermediateReport'] = $fct->getFlash();
                                    // $tab_destinations['notifyUrl'] = "https://www.example.com/sms/advanced";
                                    // $tab_destinations['notifyContentType'] =  application / json ";
                                    // $tab_destinations['callbackData'] =  "application / json ";
                                    // $tab_destinations['validityPeriod'] = 720;
                                    // $tab_destinations['sendAt'] = "2015-07-07T17:00:00.000+01:00";
                                    // $tab_destinations['deliveryTimeWindow'] = "2015-07-07T17:00:00.000+01:00";

                                    if (!empty($tab_destinations)) {
                                        $tmp_destinations[] = $tab_destinations;
                                    }
                                    #} // if ($tel != '22503612783')  ################################################################################


                                } //if tel

                            } //if(!empty($tab_line)){

                        } // if ($line != "." && $line != "..") {

                    }


                    fclose($handle);

                    if (!empty($tmp_destinations)) {

                        print("<br/>\n-----tmp_destinations----");
                        print("<pre>");
                        print_r($tmp_destinations);

                        $json = json_encode($tmp_destinations);
                        // print_r($json);
                        // var_dump($json);

                        $tab_messages["bulkId"] = $fct->getBulkId();
                        // $tab_messages["messages"] = $tab_destinations;
                        $tab_messages["messages"] = $tmp_destinations;

                        // print("<br/>\n----request -tab_messages----");
                        // print("<pre>");
                        // print_r($tab_messages);
                        // print("<br/>\n");

                        $json = json_encode($tab_messages);
                        // print_r($json);
                        // print("<br/>\n");
                        // print("<br/>\n");
                    } //if


                } else {
                    // error opening the file.
                }

                #deplacer le fichier
                rename($push_rep . '/' . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);
            } else {
                print "<br/>\n echo fichier PUSH non trouve";
            }
        } //if

    } //WHILE
    closedir($dossier);
} else {
    echo 'Le dossier n\' a pas pu être ouvert';
}


#$res = $fct->sendSMS('09327626', 'TEST');
if (!empty($tab_messages) and $trouve) {
    // print("<br/>\n----request -tab_messages----");
    // print("<pre>");
    // print_r($tab_messages);
    // print("<br/>\n");

    $res = $fct->sendMultiSMStoMultiDestV2($tab_messages);
    // var_dump($res);
}
