<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "\n\n\n ========== EXECUTION TEST INTERCEPT SMS ====== \n" . $now->format('d/m/Y H:i:s') . "\n";



define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin = API_REP;

require_once($chemin . '/Fonction.php');
$fct = new Fonction();



//------------------------------------------------------------------------
//------------------------------------------------------------------------
$from = 'INCIDENT MO';
$to='2250575201581';
$text='LE 05/06/21 12:56:15 LE GAB 13100014 EST EN SERVICE';
// $delai_ref=60;
$user='test_powercard_';
$retour = [];

$res = $fct->interceptSMS($from,$to,$text);
var_dump($res);


//------------------------------------------------------------------------
//------------------------------------------------------------------------


$out1 = ob_get_contents();
file_put_contents('/var/www/html/sms/api/log/test_intercept_sms.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
