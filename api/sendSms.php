<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');


$chemin = getcwd();

require_once($chemin . '/MyPDO.php');
$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));


print("<pre>");
// print_r($rows);


$message = uniqid();
print("<br/>\n-------------message: $message");

// print_r($row_abonne);

$to = $_GET['to'];
$message = $_GET['message'];
$message = $fct->replaceSpecialChar($message);


// $to = $fct->getTel('06736367--');
// $to = $fct->getTel('09327626');
// $to = $fct->getTel('06306746-');
$to = $fct->getTel($to);
// $to = '225' . $to;
print "<br/>\n----phone: $to";

$res = $fct->sendSMS($to, $message);
var_dump($res);

$response = json_decode($res);
var_dump($response);


// $data = [];

// if ($response->messages) {
//     print("<br/>\n---------ok");
//     $r_messages = $response->messages;
//     var_dump($r_messages);


//     $r_to = $r_messages[0]->to;
//     var_dump($r_to);

//     // foreach ($r_messages as $m) {
//     //     var_dump($m);
//     // $r_to = $r_messages[0]->to;

//     $r_status = $r_messages[0]->status;
//     $groupId = $r_status->groupId;
//     $groupName = $r_status->groupName;
//     $id = $r_status->id;
//     $name = $r_status->name;
//     $description = $r_status->description;
//     $messageId = $r_messages[0]->messageId;

//     $data['de'] = $fct->getFrom();
//     $data['a'] = $to;
//     $data['text'] = $message;
//     $data['message_id'] = $messageId;
//     // $data['status_sendsms'] = $response->messages;
//     $data['status_sendsms'] = $response->messages[0]->messageId;
//     $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
//     // $data['letest'] = $response->messages;
//     $data['letest'] = $response->messages[0]->messageId;
//     $data['send_groupid'] = $groupId;
//     $data['send_groupname'] = $groupName;
//     $data['send_id'] = $id;
//     $data['send_name'] = $name;
//     $data['send_description'] = $description;

// // }//for


// } else {
//     print("<br/>\n---------nok----$res");
// }


// $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description)
// VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description)
//  ";
// $stmt = $dbh->prepare($sql);
// print_r($sql);
// var_dump($data);
// $stmt->execute($data);
