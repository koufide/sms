<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
// print "<br/><br/><br/>\n\n\n ========== EXECUTION SEND SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s') . "<br/>\n";

define("API_REP",     '/var/www/html/sms/api');

require_once(API_REP . '/MyPDO.php');


//retour_json($retourErreur = 'retourErreur', $retourMessage = 'retourMessage', $retour = 'retour');

$retourErreur = '0';
$retourMessage = '';
$retour = [];

try {

    $conn = new MyPDO();
    $dbh = $conn->getConn();


    require_once(API_REP . '/Fonction.php');
    $fct = new Fonction();


    $chemin_params = API_REP . '/params.xml';

    if (!file_exists($chemin_params)) {
        $retourErreur = '1';
        $retourMessage = 'Parametre [params] incorrect';
        retour_json($retourErreur, $retourMessage, $retour);
    }

    $param = simplexml_load_file($chemin_params);


    if (!empty($_SERVER['PHP_AUTH_USER']) and !empty($_SERVER['PHP_AUTH_PW'])) {

        $user = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];

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
                    retour_json($retourErreur, $retourMessage, $retour);
                } //if ($mdp !== $crypte) {



                #mise a jour /// sms envoye
                $smsenvoye++;
                // if ($nbresms != '0') {

                if ($nbresms != '0' and $smsenvoye > $nbresms) {

                    $retourErreur = '1';
                    $retourMessage = "le quota sms [$nbresms] est atteint";
                    retour_json($retourErreur, $retourMessage, $retour);
                } else {
                    $input = file_get_contents('php://input'); //OK
                    $input_tab = json_decode($input, true);
                    // print "<pre>";
                    // print_r($input_tab);

                    $to = $input_tab['to'];
                    $to = $fct->getTel($to);
                    $text = $input_tab['text'];


                    # CONTROLE FLOODING # 25072019 koufide
                    # eviter d'envoyer  un meme message plusieurs fois 
                    $sql = "select * from outgoing where  a = :tel and text = :text and date_format(sendsms_at,'%d/%m/%Y') = :date_str ";
                    $stmt = $dbh->prepare("$sql");
                    $stmt->execute(
                        [
                            'tel' => $to,
                            'text' =>  trim($text),
                            'date_str' => $now->format('d/m/Y')
                        ]
                    );

                    $outgoings = $stmt->fetChall();

                    if (empty($outgoings)) {



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


                        #send sms
                        $res_send = $fct->sendMultiSMStoMultiDestV4($tab_messages);

                        // echo "<pre>";
                        // print_r($res_send);
                        $tab_message = json_decode($res_send, true);
                        // echo "<pre>";
                        // print_r($tab_message);
                        // echo $tab_message;

                        $bulkid = $tab_message['bulkId'];
                        // echo $bulkid;
                        $retour['bulkId'] = $bulkid;
                        $retour['from'] = $fct->getFrom();
                        $retour['text'] = $text;

                        if (empty($tab_message['messages'])) {
                            $retourErreur = '1';
                            $retourMessage = 'Echec sendsms';
                            retour_json($retourErreur, $retourMessage, $retour);
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
                            // var_dump($res_update);
                            $count = $stmt->rowCount();

                            // retour_json(1, $count . $sql);
                            if ($count == '0') {
                                $retourErreur = '1';
                                $retourMessage = 'Echec [update] smsenvoye';
                                retour_json($retourErreur, $retourMessage, $retour);
                            } else {
                                // echo "Success !";
                            }



                            $messages = $tab_message['messages'];
                            // print_r($messages);

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
                                $data2['applic'] = strtoupper($applic);



                                $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, 
                            send_groupname, send_id, send_name, send_description, bulk_id, tentative, applic)
                            VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , 
                            :send_id , :send_name , :send_description, :bulkId, :tentative, :applic)
                            ";

                                $stmt = $dbh->prepare($sql);
                                //print_r($sql);
                                //var_dump($data2);
                                $res = $stmt->execute($data2);

                                $count = $stmt->rowCount();

                                if ($count == '0') {
                                    $retourErreur = '1';
                                    $retourMessage = 'Echec [insert] outgoing';
                                    retour_json($retourErreur, $retourMessage, $retour);
                                } else {
                                    $retourErreur = '0';
                                    $retourMessage = '';
                                    retour_json($retourErreur, $retourMessage, $retour);
                                } //if ($count == '0') {

                            } // foreach ($r_messages as $key => $r_message) {

                        } // if(empty( $tab_message['messages'])){


                    } else {
                        $retourErreur = '1';
                        $retourMessage = "Le message [$text] a deja ete envoye au numero [$to]";
                        retour_json($retourErreur, $retourMessage, $retour);
                    } // if (empty($outgoings)) {


                } // if ($smsenvoye > $nbresms) {
            } //for

        } else {
            $retourErreur = '1';
            $retourMessage =  "Compte [$user] introuvable ou desactive";
            retour_json($retourErreur, $retourMessage, $retour);
        }
    } else {
        $retourErreur = '1';
        $retourMessage =  "Autorisation non defini";
        retour_json($retourErreur, $retourMessage, $retour);
    } //if( !empty($_SERVER['PHP_AUTH_USER']) and !empty($_SERVER['PHP_AUTH_PW'] )){





} catch (\Throwable $th) {
    $retourErreur = '1';
    $retourMessage = $th->getMessage();
    retour_json($retourErreur, $retourMessage, $retour);
}













function retour_json($retourErreur, $retourMessage, $retour)
{
    header('Content-type: application/json');
    // echo $message;
    // var_dump($retour);
    echo $retour = json_encode([
        'erreur' => $retourErreur,
        'message' => $retourMessage,
        'retour' => $retour
    ]);


    //--------------------------------------------------------------
    $out1 = ob_get_contents();
    // // var_dump($out1);
    // $fp = fopen('sendsms.log', 'w');
    // fwrite($fp, $out1);
    // fclose($fp);

    $myfile = file_put_contents('log/sendsms.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);


    exit(0);
}
