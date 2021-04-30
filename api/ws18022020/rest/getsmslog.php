<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
// print "<br/><br/><br/>\n\n\n ========== EXECUTION GET DELIVRERY REPORT  ====== <br/>\n" . $now->format('d/m/Y H:i:s') . "<br/>\n";

define("API_REP",     '/var/www/html/sms/api');

require_once(API_REP . '/MyPDO.php');


$erreur = '0';
$result = [];

try {

    $conn = new MyPDO();
    $dbh = $conn->getConn();


    require_once(API_REP . '/Fonction.php');
    $fct = new Fonction();

    // chdir(API_REP);

    $chemin_params = API_REP . '/params.xml';

    if (!file_exists($chemin_params)) {
        // exit("<br/>\n chemin : $chemin_params introuvable");
        $erreur = '1';
        $result['message'] = 'Parametre [params] incorrect';
        retour_json($erreur, $result);
        // exit(0);
    }

    $param = simplexml_load_file($chemin_params);
    // print "<pre>";
    // print_r($param);





    // print_r($_SERVER); //OK
    $user = $_SERVER['PHP_AUTH_USER'];
    $pw = $_SERVER['PHP_AUTH_PW'];
    // var_dump("$user $pw");

    # authentification
    $sql = "select * from connexion where uti =:uti and statut = :statut";
    $data = [
        'uti' => $user,
        'statut' => '1',
    ];
    // print "<pre>";
    // print_r($data);
    // print_r($sql);

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
                $erreur = '1';
                $result['message'] = "Mot de passe [$pw] incorrect";
                retour_json($erreur, $result);
            }



            #mise a jour /// sms envoye
            $smsenvoye++;

            if ($nbresms != '0' and $smsenvoye > $nbresms) {
                $erreur = '1';
                $result['message'] = "le quota sms [$nbresms] est atteint";
                retour_json($erreur, $result);
            } else {
                // echo file_get_contents('php://input'); //OK
                $input = file_get_contents('php://input'); //OK
                $input_tab = json_decode($input, true);
                // print "<pre>";
                // print_r($input_tab);

                $to = $input_tab['to'];
                $text = $input_tab['text'];


                // $res_send  = $fct->sendSmsApplic($to, $text);
                // print "<pre>";
                // print_r($res_send);

                $tab_messages = [
                    'bulkId' => $fct->getBulkId(strtoupper(substr($applic, 0, 3))),
                    'messages' => [
                        'from' => $fct->getFrom(),
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

                // print "<pre>";
                // print_r($tab_messages);


                // print "$res_send";
                // $res_send = json_decode($res_send, true);
                // var_dump($res_send);
                // print "<pre>";
                // print_r($res_send);

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
                // var_dump($res_update);
                $count = $stmt->rowCount();

                if ($count == '0') {
                    // echo "Failed !";
                    // retour_json(1, 'Echec update smsenvoye');
                    // exit(0);

                    $erreur = '1';
                    $result['message'] = "Echec update smsenvoye";
                    retour_json($erreur, $result);
                } else {
                    // echo "Success !";
                }



                #
                $res_send = $fct->sendMultiSMStoMultiDestV4($tab_messages);

                // echo "<pre>";
                // print_r($res_send);
                $tab_message = json_decode($res_send, true);
                // echo "<pre>";
                // print_r($tab_message);
                // echo $tab_message;
                // exit(0);

                $bulkid = $tab_message['bulkId'];
                // echo $bulkid;
                $result['bulkId'] = $bulkid;
                $result['from'] = $fct->getFrom();
                $result['text'] = $text;

                $messages = $tab_message['messages'];
                // print_r($messages);
                // exit(0);

                foreach ($messages as $key => $r_message) {
                    $r_to = $r_message['to'];
                    $result['to'] = $r_to;
                    // echo $r_to;
                    // exit(0);

                    $r_status = $r_message['status'];

                    $groupId = $r_status['groupId'];
                    $result['statusGroupId'] = $groupId;

                    $groupName = $r_status['groupName'];
                    $result['statusGroupName'] = $groupName;

                    $id = $r_status['id'];
                    $result['statusId'] = $id;

                    $name = $r_status['name'];
                    $result['statusName'] = $name;

                    $description = $r_status['description'];
                    $result['statusDescription'] = $description;

                    $messageId = $r_message['messageId'];
                    $result['messageId'] = $messageId;


                    $data2 = [];
                    $data2['bulkId'] = $bulkid;
                    $data2['de'] = $fct->getFrom();

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



                    $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, 
                send_groupname, send_id, send_name, send_description, bulk_id, tentative)
                VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , 
                :send_id , :send_name , :send_description, :bulkId, :tentative)
                 ";

                    $stmt = $dbh->prepare($sql);
                    //print_r($sql);
                    //var_dump($data2);
                    $res = $stmt->execute($data2);

                    $count = $stmt->rowCount();

                    if ($count == '0') {
                        $erreur = '1';
                        // echo "Failed !";
                        // retour_json($erreur, $result);
                        // exit(0);

                        $erreur = '1';
                        $result['message'] = "Echec insert outgoing";
                        retour_json($erreur, $result);
                    } else {
                        $erreur = '0';
                        // $result['message'] = "success";
                        retour_json($erreur, $result);
                    }

                    // retour_json(0, $res_send);
                    // exit(0);
                } // foreach ($r_messages as $key => $r_message) {

                // exit("stop");

                // var_dump($count);
            } // if ($smsenvoye > $nbresms) {
            // } //if($nbresms !='0'){
        } //for

    } else {
        $erreur = '1';
        $result['message'] = "Compte [$user] introuvable ou desactive";
        retour_json($erreur, $result);
    }
} catch (\Throwable $th) {
    $erreur = '1';
    $result['message'] = $th->getMessage();
    retour_json($erreur, $result);
}








//--------------------------------------------------------------
//--------------------------------------------------------------
//--------------------------------------------------------------
$out1 = ob_get_contents();
// var_dump($out1);
$fp = fopen('sendsms.log', 'w');
fwrite($fp, $out1);
fclose($fp);




function retour_json($erreur, $result)
{
    header('Content-type: application/json');
    // echo $message;
    // var_dump($result);
    echo $retour = json_encode([
        'error' => $erreur,
        'results' => $result
    ]);
    exit(0);
}
