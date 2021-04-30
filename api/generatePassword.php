<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION GENERATE PASSWORD ====== <br/>\n" . $now->format('d/m/Y H:i:s');

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

// $file_alert_log = $param->files->file_generatepassword_log;

// $log_rep = $param->reps->log_rep;

// $file_alert_log = $log_rep . $file_alert_log;



$pwd = $fct->genererPassword(8);
$salt = $fct->getSalt();
$crypte = $fct->crypterPassword($pwd, $salt);

print "<br/>\n pwd ==> $pwd";
print "<br/>\n salt ==> $salt";
print "<br/>\n crypte ==> $crypte";

exit("<br/>\n ========= EXIT");


// $tab_destinations = [];
// $tmp_destinations = [];
$tab_messages = [];


# liste des messages chargés non traités
// $sql = "SELECT a.phone, s.* FROM chargesms s JOIN abonnement a WHERE s.compte=a.compte AND a.actif = 1 AND s.traite = 0 ";
$sql = "SELECT distinct a.phone, s.* FROM chargesms s JOIN abonnement a WHERE s.compte=a.compte AND a.actif = 1 AND s.traite = 0 ";
// $sql = "SELECT a.phone, s.* FROM chargesms s JOIN abonnement a WHERE s.compte=a.compte AND a.actif = 1 AND s.traite = 0 AND s.compte='15001700004' LIMIT 0,1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$abonnements = $stmt->fetchAll();

// print("<pre>");
// print_r($abonnements);
// print("<br/>\n");

$i = 0;
foreach ($abonnements as $abonnement) {
    $i++;
    $tel = $abonnement['phone'];
    $message = $abonnement['message'];

    $destinations = [
        "to" => $tel,
        "messageId" => $fct->getMessageId()
    ];


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


    // if (!empty($tab_destinations)) {
    $tmp_destinations[] = $tab_destinations;
    // }
} //foreach($abonnements as $abonnement){

// print("<pre>");
// print_r($tmp_destinations);
// print("<br/>\n");

print "<br/>\n non traite: $i <br/>\n<br/>\n";
// exit("<br/>\n-----exit tmp_destinations----");


if (!empty($tmp_destinations)) {

    // print("<br/>\n-----tmp_destinations----");
    // print("<pre>");
    // print_r($tmp_destinations);

    $json = json_encode($tmp_destinations);
    // print_r($json);
    // var_dump($json);

    $tab_messages["bulkId"] = $fct->getBulkId();
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


if (!empty($tab_messages)) {
    print("<br/>\n----request -tab_messages----");
    print("<pre>");
    print_r($tab_messages);
    print("<br/>\n");

    $res = $fct->sendMultiSMStoMultiDestV3($tab_messages);
    // var_dump($res);
}
