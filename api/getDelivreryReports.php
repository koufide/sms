<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION DELIVERY REPORT ======<br/>\n " . $now->format('d/m/Y H:i:s') . "<br/>\n";

define("API_REP",     '/var/www/html/sms/api');

// print "<br/>\n current getcwd: " . getcwd();
chdir(API_REP);
// print "<br/>\n after change getcwd: " . getcwd();



// $chemin = getcwd();
// $chemin = getcwd() .'/sms/api';
// $chemin = '/var/www/html/sms/api';
$chemin = API_REP;

require_once($chemin . '/MyPDO.php');
$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));


// $sql = "select * from outgoing where message_id is not null and report_status_groupname is null  and DATE_FORMAT(sendsms_at, '%d/%m/%Y') = :datejour order by id ASC   ";
$sql = "select * from outgoing where message_id is not null and  ( report_status_groupname not like '%DELIVERED%'  or report_status_groupname is null )  
and DATE_FORMAT(sendsms_at, '%Y/%m/%d') between :datejour and  :datejour2  order by id ASC   ";
// $sql = "select * from outgoing where id ='99345' order by id ASC   ";


$Date1 =  $now->format('Y/m/d');
$date = new DateTime($Date1);
//$Date1 = $date->format('d/m/Y');
$date->add(new DateInterval('P2D')); // P1D means a period of 1 day
$Date2 = $date->format('Y/m/d');

//$Date2 = $date2->format('d/m/Y');
var_dump($Date1);
var_dump($Date2);



// select a particular user by id
$stmt = $dbh->prepare("$sql");
$stmt->execute([
    'datejour' =>  $Date1, //'27/06/2019'
    'datejour2' =>  $Date2 //'27/06/2019'
]);
$rows_outgoing = $stmt->fetchAll();
// print("<pre>");
//print_r($rows);

// print("<pre>");
$i = 0;
foreach ($rows_outgoing as $key => $row) {
    $i++;
    // print "<pre>";
    // print_r($row);

    $messageId = $row['message_id'];

    // print("<br/>\n-------------messageId: $messageId");
    $res = $fct->getDelivreryReports($messageId);
    // var_dump($res);
    // print_r($res);

    // $data = [
    //     'messageId' => $messageId,
    //     'results_reports' => $res
    // ];

    // $sql = "UPDATE outgoing SET results_reports= :results_reports WHERE message_id= :messageId ";
    // $stmt = $dbh->prepare($sql);
    // $stmt->execute($data);


} //FOR rows//message
print "<br/>\n ------nbres lignes : $i";

$out1 = ob_get_contents();
$myfile = file_put_contents('log/getDelivreryReports_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
