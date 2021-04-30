<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
// print "<br/><br/><br/>\n\n\n ========== EXECUTION GET DELIVRERY REPORT  ====== <br/>\n" . $now->format('d/m/Y H:i:s') . "<br/>\n";

define("API_REP",     '/var/www/html/sms/api');

require_once(API_REP . '/MyPDO.php');


$retourErreur = '0';
$retourMessage = '';
$retour = [];

try {

    $conn = new MyPDO();
    $dbh = $conn->getConn();


    require_once(API_REP . '/Fonction.php');
    $fct = new Fonction();

    // chdir(API_REP);


    if (!empty($_SERVER['PHP_AUTH_USER']) and !empty($_SERVER['PHP_AUTH_PW'])) {

        $user = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];


        $chemin_params = API_REP . '/params.xml';

        if (!file_exists($chemin_params)) {
            $retourErreur = '1';
            $retourMessage = 'Parametre [params] incorrect';
            retour_json($user, $retourErreur, $retourMessage, $retour);
        }

        $param = simplexml_load_file($chemin_params);



        # authentification
        $sql = "select * from connexion where uti =:uti and statut = :statut";
        $data = [
            'uti' => $user,
            'statut' => '1',
        ];

        $stmt = $dbh->prepare("$sql");
        $stmt->execute($data);
        $rows = $stmt->fetchAll();

        if (!empty($rows)) {
            foreach ($rows as $key => $row) {
                // print_r($row);
                $mdp = $row['mdp'];
                $salt = $row['salt'];
                $applic = $row['applic'];
                $nbresms = $row['nbresms'];
                $smsenvoye = $row['smsenvoye'];

                $crypte = $fct->crypterPassword($pw, $salt);
                // print("<br/>\n$crypte");
                // print("<br/>\n$mdp");

                if ($mdp !== $crypte) {
                    $retourErreur = '1';
                    $retourMessage = "Mot de passe [$pw] incorrect";
                    retour_json($user, $retourErreur, $retourMessage, $retour);
                }


                $input = file_get_contents('php://input'); //OK
                $input_tab = json_decode($input, true);

                if (array_key_exists('messageId', $input_tab)) {

                    $messageId = $input_tab['messageId'];

                    # verifier le message ID 
                    $sql = "select * from outgoing where message_id = :messageId";
                    $stmt = $dbh->prepare("$sql");
                    $stmt->execute(['messageId' => $messageId]);
                    $rows_messages = $stmt->fetchAll();

                    // retour_json($erreur, $retours);


                    // retour_json(1, $rows_messages);


                    foreach ($rows_messages as $message) {
                        $text = $message['text'];
                        $from = $message['de'];


                        $res_send = $fct->getDelivreryReportsV2($messageId);

                        $tab_message = json_decode($res_send, true);
                        // print "<pre>";
                        // print_r($tab_message);

                        // retour_json($erreur, $tab_message);


                        if (empty($tab_message['results'])) {
                            $retourErreur = '1';
                            $retourMessage =  'Aucun resultat';
                            retour_json($user, $retourErreur, $retourMessage, $retour);
                        } else {

                            $results = $tab_message['results'];
                            $results = $results[0];
                            // retour_json($erreur, $results);//ok


                            // print_r($results);
                            // retour_json($erreur, $results); //ok

                            $bulkId = $results['bulkId'];
                            $messageId = $results['messageId'];
                            $to = $results['to'];
                            $sentAt = $results['sentAt'];
                            $doneAt = $results['doneAt'];
                            $smsCount = $results['smsCount'];
                            $mccMnc = $results['mccMnc'];
                            // $callbackData = $results['callbackData'];
                            $pricePerMessage = $results['price']['pricePerMessage'];
                            $currency = $results['price']['currency'];
                            $statusId = $results['status']['id'];
                            $statusGroupId = $results['status']['groupId'];
                            $statusGroupName = $results['status']['groupName'];
                            $statusName = $results['status']['name'];
                            $statusDescription = $results['status']['description'];
                            $errorId = $results['error']['id'];
                            $errorGroupId = $results['error']['groupId'];
                            $errorGroupName = $results['error']['groupName'];
                            $errorName = $results['error']['name'];
                            $errorDescription = $results['error']['description'];
                            $errorPermanent = $results['error']['permanent'];


                            $sentAt =  new DateTime($sentAt, new DateTimeZone(('UTC')));
                            $sentAt_str = $sentAt->format('Y-m-d H:i:s');

                            $doneAt =  new DateTime($doneAt, new DateTimeZone(('UTC')));
                            $doneAt_str = $doneAt->format('Y-m-d H:i:s');



                            $data = [
                                'message_id' => $messageId,
                                'results_reports' => true,
                                'report_sentat' => $sentAt_str,
                                'report_doneat' => $doneAt_str,
                                'sms_count' => $smsCount,
                                'report_mccmnc' => $mccMnc,
                                'report_pricepermessage' => $pricePerMessage,
                                'report_currency' => $currency,

                                'report_status_groupid' => $statusGroupId,
                                'report_status_groupname' => $statusGroupName,
                                'report_status_id' => $statusId,
                                'report_status_name' => $statusName,
                                'report_status_description' => $statusDescription,

                                'report_error_groupid' => $errorGroupId,
                                'report_error_groupname' => $errorGroupName,
                                'report_error_id' => $errorId,
                                'report_error_name' => $errorName,
                                'report_error_description' => $errorDescription,
                                'report_error_permanent' => $errorPermanent,
                            ];

                            // retour_json($erreur, $colonnes);


                            $retour['bulkId'] = $bulkId;
                            $retour['messageId'] = $messageId;
                            $retour['to'] = $to;
                            $retour['from'] = $from;
                            $retour['sentAt'] = $sentAt_str;
                            $retour['doneAt'] = $doneAt_str;
                            $retour['smsCount'] = $smsCount;
                            $retour['statusGroupId'] = $statusGroupId;
                            $retour['statusGroupName'] = $statusGroupName;
                            $retour['statusId'] = $statusId;
                            $retour['statusName'] = $statusName;
                            $retour['statusDescription'] = $statusDescription;
                            $retour['errorGroupId'] = $errorGroupId;
                            $retour['errorGroupName'] = $errorGroupName;
                            $retour['errorId'] = $errorId;
                            $retour['errorName'] = $errorName;
                            $retour['errorDescription'] = $errorDescription;
                            $retour['text'] = $text;


                            $colonnes = "
                            results_reports = :results_reports,
                            report_sentat = :report_sentat,
                            report_doneat = :report_doneat,
                            sms_count = :sms_count,
                            report_mccmnc = :report_mccmnc,
                            report_pricepermessage = :report_pricepermessage,
                            report_currency = :report_currency,
                            report_status_groupid = :report_status_groupid,
                            report_status_groupname = :report_status_groupname,
                            report_status_id = :report_status_id,
                            report_status_name = :report_status_name,
                            report_status_description = :report_status_description,
                            report_error_groupid = :report_error_groupid,
                            report_error_groupname = :report_error_groupname,
                            report_error_id = :report_error_id,
                            report_error_name = :report_error_name,
                            report_error_description = :report_error_description,
                            report_error_permanent = :report_error_permanent
                        ";
                            // print_r($data);
                            // print_r($colonnes);




                            $sql =  "update outgoing set $colonnes where message_id = :message_id ";
                            // var_dump($sql);
                            //print  "<br/>\n compte => " . $abonnement['compte'];
                            $stmt = $dbh->prepare($sql);
                            $res_update = $stmt->execute($data);
                            // var_dump($res_update);
                            $count = $stmt->rowCount();

                            if ($count == '0') {
                                $retourErreur = '1';
                                $retourMessage =  'Echec [update] outgoing';
                                retour_json($user, $retourErreur, $retourMessage, $retour);
                            } else {

                                $retourErreur = '0';
                                $retourMessage = '';
                                retour_json($user, $retourErreur, $retourMessage, $retour);
                            }
                        } //foreach ($rows_messages as $message) {

                    } //if (empty($tab_message['results'])) {

                } else {
                    $retourErreur = '1';
                    $retourMessage =  'Parametre [messageId] inexistant';
                    retour_json($user, $retourErreur, $retourMessage, $retour);
                } // if(array_key_exists('messageId',$input_tab)){

            } //for

        } else {
            $retourErreur = '1';
            $retourMessage = "Compte [$user] introuvable ou desactive";
            retour_json($user, $retourErreur, $retourMessage, $retour);
        }
    } else {
        $retourErreur = '1';
        $retourMessage =  "Autorisation non defini";
        retour_json($user = null, $retourErreur, $retourMessage, $retour);
    } //if (!empty($_SERVER['PHP_AUTH_USER']) and !empty($_SERVER['PHP_AUTH_PW'])) {


} catch (\Throwable $th) {
    $retourErreur = '1';
    $retourMessage = $th->getMessage();
    retour_json($user, $retourErreur, $retourMessage, $retour);
}









function retour_json($user, $retourErreur, $retourMessage, $retour)
{
    $now2 = new DateTime('NOW', new DateTimeZone(('UTC')));

    header('Content-type: application/json');
    // echo $message;
    // var_dump($retour);
    echo $retour = json_encode([
        'datexec' => $now2->format('Y-m-d H:i:s'),
        'user' => $user,
        'erreur' => $retourErreur,
        'message' => $retourMessage,
        'retour' => $retour
    ]);


    //--------------------------------------------------------------
    $out1 = ob_get_contents();
    // var_dump($out1);
    // $fp = fopen('/var/www/html/test/webservice/rest/log/getdelivreryreport.log', 'w');
    // fwrite($fp, $out1);
    // fclose($fp);

    if ($user == null)
        $myfile = file_put_contents('log/getdelivreryreport_' . $now2->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
    else {
        $myfile = file_put_contents('log/' . $user . '_getdelivreryreport_' . $now2->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
    }



    exit(0);
}
