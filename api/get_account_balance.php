<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION GET ACCOUNT BALANCE ====== <br/>\n" . $now->format('d/m/Y H:i:s');



define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin = API_REP;

// $chemin_params = 'params.xml';

// if (!file_exists($chemin_params)) {
//     exit("<br/>\n chemin : $chemin_params introuvable");
// }



// require_once($chemin . '/MyPDO.php');

// $conn = new MyPDO();
// $dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();

$fct->getAccountBalance();


$out1 = ob_get_contents();
$myfile = file_put_contents('log/get_account_balance_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
