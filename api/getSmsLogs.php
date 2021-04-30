<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION SMS LOGS ====== <br/>\n" . $now->format('d/m/Y H:i:s');
// exit("<br/>\n stop");

define("API_REP",     '/var/www/html/sms/api');

// print "<br/>\n current getcwd: " . getcwd();
chdir(API_REP);
// print "<br/>\n after change getcwd: " . getcwd();


// $chemin = getcwd();
// $chemin = getcwd() .'/sms/api';
// $chemin = '/var/www/html/sms/api';
$chemin = API_REP;
// var_dump($chemin);

require_once($chemin . '/MyPDO.php');

$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));


// $sql = "select * from outgoing where message_id is not null and results_logs is null";
// $sql = "select * from outgoing where message_id is not null and report_status_groupname is null and DATE_FORMAT(sendsms_at, '%d/%m/%Y') = :datejour order by id ASC limit 0,1";
// $sql = "select * from outgoing where message_id is not null and report_status_groupname is null and DATE_FORMAT(sendsms_at, '%d/%m/%Y') = :datejour order by id ASC ";
// $sql = "select * from outgoing where message_id is not null and report_status_groupname is null  order by id ASC ";
$sql = "select * from outgoing where message_id is not null  and ( report_status_groupname not like '%DELIVERED%'  or report_status_groupname is null ) and results_logs is null  order by id ASC ";
// $sql = "select * from outgoing where message_id is not null  and ( report_status_groupname not like '%DELIVERED%'  or report_status_groupname is null ) and results_logs is null  order by id ASC LIMIT 0,50 ";

// select a particular user by id
$stmt = $dbh->prepare("$sql");
// $stmt->execute([
//     'datejour' =>  '26/06/2019' //$now->format('d/m/Y') //'27/06/2019'
// ]);
$stmt->execute();
$rows_outgoing = $stmt->fetchAll(); 
//print("<pre>");
//print_r($rows_outgoing);
// exit("<br/>\n stop");
//print("<br/>\n ------");

foreach ($rows_outgoing as $key => $row) {
    //print_r($row);

    // $messageId = $row['message_id'];

    // print("<br/>\n-------------messageId: $messageId");
    // $res = $fct->getSmsLogs($fct->getFrom(), $messageId);
    // $res = $fct->getSmsLogs($messageId);
    $limit = 1;
    // $res = $fct->getSmsLogs($limit);

    $from = $row['de'];
    $to = $row['a'];
    $bulkId = $row['bulk_id'];
    $messageId = $row['message_id'];

    print "<br/>\n => $from, $to, $bulkId, $messageId";

    $res = $fct->getSmsLogsV2($from, $to, $bulkId, $messageId);
    // var_dump($res);



}//rows//message
