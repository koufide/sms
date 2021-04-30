<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$chemin = getcwd();
$chemin = '/var/www/html/sms/api';

require_once($chemin . '/MyPDO.php');
$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));


$sql = "select * from message where is_valid1=1 AND is_valid2=1 AND is_diffu=0";

// select a particular user by id
$stmt = $dbh->prepare("$sql");
$stmt->execute();
$rows = $stmt->fetchAll();
print("<pre>");
// print_r($rows);

foreach ($rows as $key => $row) {
    print_r($row);

    $message = $row['contenu'];
    print("<br/>\n-------------message: $message");

    $stmt_abonne = $conn->__select2('abonnement', '*', 'WHERE is_actif=1 ');


    if ($stmt_abonne->execute()) {
        while ($row_abonne = $stmt_abonne->fetch(PDO::FETCH_ASSOC)) {
            print_r($row_abonne);

            $to = $row_abonne['phone'];
            $to = '225' . $to;
            print "<br/>\n----phone: $to";

            $res = $fct->sendSMS($to, $message);
            var_dump($res);

            $response = json_decode($res);
            if ($response->messages) {
                print("<br/>\n-----quittter----ok");
                print_r($response);
            } else {
                print("<br/>\n-----quittter----nok----$res");
            }

            $data = [
                'de' => $fct->getFrom(),
                'a' => $to,
                'text' => $message,
                'status' => $response,
                'message_id' => $response->messages[0]->messageId,
                'sent_at' => $now->format('d-m-Y H:i:s'),
            ];

            // $stmt = $dbh->prepare("INSERT INTO outgoing (de, a, text, status, message_id, sent_at) VALUES ('" . $fct->getFrom() . "', '" . $to . "', '" . $message . "','" . $res . "','" . $response->messages[0]->messageId . "', '" . $now->format('Y-m-d H:i:s') . "' )");
            // $stmt->execute();




        }//while abonne

    }//if abonne


    #faire la mise à jour du message
    $data = [
        'is_diffu' => true,
        'datediffu' => $now->format('Y-m-d H:i:s'),
        'id' => $row['id'],
    ];
    $sql = "UPDATE message SET is_diffu=:is_diffu, datediffu=:datediffu WHERE id=:id";
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);


}//rows//message


    
        
                        