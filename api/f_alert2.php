<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION  MANUEL F ALERT ====== <br/>\n" . $now->format('d/m/Y H:i:s');

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

$file_alert_log = $param->files->file_malert_log;


$log_rep = $param->reps->log_rep;


$uti = $param->sms->user;
$mdp = $param->sms->mdp;
// var_dump("$uti $mdp");



$file_lock_push_sms = $param->files->file_lock_fpush_sms;
$file_lock_push_sms = $log_rep . $file_lock_push_sms;
if (file_exists($file_lock_push_sms)) {
    exit("<br/>\n exit! diffusion sms encours...");
} else {
    $myfile = fopen("$file_lock_push_sms", "w");
}


$file_lock_charge_abonne = $param->files->file_lock_charge_abonne;
$file_lock_charge_abonne = $log_rep . $file_lock_charge_abonne;
if (file_exists($file_lock_charge_abonne)) {
    unlink($file_lock_push_sms); //supprimer le lock de diffu
    exit("<br/>\n exit! chargement des abonnes encours...");
}


$applic = 'CGB'; ///?????

$tab_messages = [];


# liste des messages chargés non traités
$sql = "SELECT distinct tel, message FROM f_chargesms WHERE traite = 0  limit 0,100";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$abonnements = $stmt->fetchAll();

// print("<pre>");
// print_r($abonnements);
// print("<br/>\n");





$i = 0;
foreach ($abonnements as $abonnement) {
    $i++;
    // $compte = $abonnement['compte'];
    $tel = $abonnement['tel'];
    $message = $abonnement['message'];
    $message = $fct->replaceSpecialChar($message);

    ####CHECK FLOODING
    # eviter d'envoyer  un meme message plusieurs fois 
    $sql = "select * from outgoing where  a = :tel and text = :text and date_format(sendsms_at,'%d/%m/%Y') = date_format(NOW(),'%d/%m/%Y')  ";
    $stmt = $dbh->prepare("$sql");
    $stmt->execute(
        [
            'tel' => $tel,
            'text' =>  trim($message)
        ]
    );

    $outgoings = $stmt->fetChall();
    // $outgoings = $stmt->fetch(PDO::FETCH_ASSOC);
    var_dump($outgoings);
    // $nbre = $outgoings['nbre'];
    // var_dump($nbre);
    // var_dump(intval($nbre));
    // unlink(($file_lock_push_sms));
    // exit("<br/>\n --------- stop ---------");


    if (empty($outgoings)) {
        // if (intval($nbre) != 0) {

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

    } else { //// if(empty($outgoings)){
        print "<br/>\n doublon message en double: ";
        var_dump($outgoings);
        # ------------- DOUBLON 
        $sql = "UPDATE f_chargesms SET traite = :traite WHERE traite='0' AND tel = :tel and message = :message and date_format(datecharge,'%d/%m/%Y') = date_format(NOW(),'%d/%m/%Y') ";

        $stmt = $dbh->prepare($sql);

        $data = [];
        $data['traite'] = '3';
        #// 3 - doublon     /2- client sans abonnement
        // $data['compte'] = $compte;
        $data['tel'] = $tel;
        $data['message'] = trim($message);
        // $data['date_str'] = $now->format('d/m/Y');

        // print_r($sql);
        // var_dump($data);

        try {
            $res_update = $stmt->execute($data);
            var_dump($res_update);
            $count = $stmt->rowCount();

            if ($count == '0') {
                echo "Failed update !";
            } else {
                echo "Success update!";
            }
            var_dump($count);
        } catch (\Throwable $th) {
            // $fct->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
            print_r($th);
        }
    } // if(empty($outgoings)){


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

    // $res = $fct->sendMultiSMStoMultiDestV5($tab_messages, $applic);
    $res = $fct->sendMultiSMStoMultiDestV6($tab_messages, $applic);
    // var_dump($res);
}

unlink($file_lock_push_sms);


$out1 = ob_get_contents();
$myfile = file_put_contents('log/f_alert2_' . $now->format('Y_m_d_H_i_s') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
