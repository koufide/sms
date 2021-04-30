<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "\n\n\n ========== EXECUTION NOTIFY URL ====== \n" . $now->format('d/m/Y H:i:s');



define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin = API_REP;
// $chemin_params = 'params.xml';

// if (!file_exists($chemin_params)) {
//     exit("\n chemin : $chemin_params introuvable");
// }

// require_once($chemin . '/MyPDO.php');

// $conn = new MyPDO();
// $dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$fct->getAccountBalance();



$out1 = ob_get_contents();
file_put_contents('/var/www/html/sms/api/log/notifyurl.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
