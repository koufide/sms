<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION STATS SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s');

define("API_REP",     '/var/www/html/sms/api');

// print "<br/>\n current getcwd: " . getcwd();
chdir(API_REP);
// print "<br/>\n after change getcwd: " . getcwd();

// $chemin_params = getcwd() . '/params.xml';
$chemin_params = 'params.xml';

if (!file_exists($chemin_params)) {
    exit("<br/>\n chemin : $chemin_params introuvable");
}


// $chemin = getcwd();
$chemin = API_REP;
// print "<br/>\n chemin: $chemin <br/>\n";
//exit("<br/>\n----------quit");

require_once($chemin . '/MyPDO.php');
// require_once($chemin . '/api/MyPDO.php');
// require_once('/var/www/html/sms/api/' . 'MyPDO.php');

$conn = new MyPDO();
$dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();


$param = simplexml_load_file($chemin_params);

$file_alert_log = $param->files->file_alert_log;


$log_rep = $param->reps->log_rep;

$file_lock_push_sms = $param->files->file_lock_push_sms;
$file_lock_push_sms = $log_rep . $file_lock_push_sms;

$uti = $param->sms->user;
$mdp = $param->sms->mdp;
// var_dump("$uti $mdp");



// D'autres manières d'appeler error_log():
#error_log("TEST !", 3, $log_rep . $file_alert_log);

// if (file_exists($file_lock_push_sms)) {
//     exit("<br/>\n exit! traitement encours...");
// }
// else {
//     $myfile = fopen("$file_lock_push_sms", "w");
// }


$solde = $fct->getAccountBalance();
var_dump($solde);
print_r($solde);


$sql = "select count(id) nbresms from outgoing where  date_format(sendsms_at,'%d/%m/%Y') = :date_str ";
$stmt = $dbh->prepare("$sql");
$stmt->execute(
    [
        'date_str' => $now->format('d/m/Y')
    ]
);

$row = $stmt->fetch();
var_dump($row);
print_r($row);
$nbresms = $row['nbresms'];
var_dump($nbresms);
print_r($nbresms);



$colonnes = 'datoper,datestat,solde,nbresms';
$valeurs = ':datoper,:datestat,:solde,:nbresms';

$sql = "insert into statsms($colonnes) values($valeurs)";
$stmt = $dbh->prepare($sql);
try {

    $data = [
        'datoper' => $now->format('Y-m-d H:i:s'),
        'datestat' => $now->format('Y-m-d'),
        'solde' => $solde,
        'nbresms' => $nbresms
    ];

    $stmt->execute($data);
} catch (\Throwable $th) {

    // $colonnes = 'datoper = :datoper,datestat = :datestat,solde = :solde,nbresms = :nbresms';
    $colonnes = 'datoper = :datoper,solde = :solde,nbresms = :nbresms';
    $valeurs = ',,,';

    $sql = "update statsms set $colonnes where date_format(datestat,'%d/%m/%Y') = :date_str ";
    $stmt = $dbh->prepare($sql);

    try {
        $stmt->execute([
            'date_str' => $now->format('d/m/Y'),
            'datoper' => $now->format('Y-m-d H:i:s'),
            //'datestat' => $now->format('Y-m-d'),
            'solde' => $solde,
            'nbresms' => $nbresms
        ]);
    } catch (\Throwable $th2) {
        //throw $th;
        $fct->returnError(null, $th2->getFile() . ' ' . $th2->getLine() . ' ' . $th2->getMessage());
    }
}


$out1 = ob_get_contents();
$myfile = file_put_contents('log/statsms_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
