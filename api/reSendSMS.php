<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

#
# RENSEVOYER le message chaque 30 min. (3 essais) lorsque le precedent n'a pas été delivré
#
#


$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION RE-SEND SMS ====== " . $now->format('d/m/Y H:i:s');

define("API_REP",     '/var/www/html/sms/api');

// print "<br/>\n current getcwd: " . getcwd();
chdir(API_REP);
// print "<br/>\n after change getcwd: " . getcwd();


$chemin = API_REP;

require_once($chemin . '/MyPDO.php');
$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();


// $sql = "select * FROM outgoing where (report_status_groupname not like '%DELIVERED%' or report_status_groupname is null) ";
$sql = "select * FROM outgoing where report_status_groupname not like '%DELIVERED%' AND tentative < 3  ";

// select a particular user by id
$stmt = $dbh->prepare("$sql");
$stmt->execute();

while ($row = $stmt->fetch()) {
    //$data = $row[0] . "\t" . $row[1] . "\t" . $row[2] . "\n";
    //print $data;
    var_dump($row);

    $message = $row['text'];
    $messageId = $row['message_id'];
    $de = $row['de'];
    $to = $row['a'];
    $tentative = $row['tentative'];
    $tentative++;

    echo "<br/>\n";
    $fct->resendSMS($to, $message, $messageId, $tentative);
} //while


// $rows_outgoing = $stmt->fetchAll();
// print("<pre>");
// print_r($rows_outgoing);

// exit("<br/>\n---------------QUITTER----------------");

// print("<pre>");
// foreach ($rows_outgoing as $key => $row) {
//     // print_r($row);

//     $messageId = $row['message_id'];

//     print("<br/>\n-------------messageId: $messageId");
//     $res = $fct->getDelivreryReports($messageId);
//     var_dump($res);
//     print_r($res);

//     // $data = [
//     //     'messageId' => $messageId,
//     //     'results_reports' => $res
//     // ];

//     // $sql = "UPDATE outgoing SET results_reports= :results_reports WHERE message_id= :messageId ";
//     // $stmt = $dbh->prepare($sql);
//     // $stmt->execute($data);


// }//rows//message
