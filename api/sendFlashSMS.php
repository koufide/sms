<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

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


$to = $fct->getTel('06736367');
// $to = $fct->getTel('03612783');
            // $to = '225' . $to;
print "<br/>\n----phone: $to";

$res = $fct->sendFlashSMS($to, $message);
var_dump($res);

$response = json_decode($res);
var_dump($response);


