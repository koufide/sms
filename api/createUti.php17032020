<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION CREATE UTI ====== <br/>\n" . $now->format('d/m/Y H:i:s') . "<br/>\n";

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

$file_createuti_log = $param->files->file_createuti_log;

$log_rep = $param->reps->log_rep;

$file_createuti_log = $log_rep . $file_createuti_log;

var_dump(!empty($_GET['uti']));
var_dump(!empty($_GET['applic']));
var_dump(!empty($_GET['statut']));
var_dump(!empty($_GET['nbresms']));
var_dump(!empty($_GET['smsenvoye']));

print "<br/>\n<pre>";
print_r($_GET);


try {

    if (
        !empty($_GET['uti']) && !empty($_GET['applic']) && !empty($_GET['statut'])
        // && !empty($_GET['nbresms']) && empty($_GET['smsenvoye'])
    ) {

        // $uti = 'PowerCard';
        // $applic = 'Monetique';
        // $statut = '1';
        // $nbresms = '5';
        // $smsenvoye = '0';

        $uti = $_GET['uti'];
        $applic = $_GET['applic'];
        $statut = $_GET['statut'];
        $nbresms = $_GET['nbresms'];
        $smsenvoye = $_GET['smsenvoye'];

        if (empty($smsenvoye))
            $smsenvoye = 0;

        if (empty($nbresms))
            $nbresms = 0;

        $pwd = $fct->genererPassword(8);
        $salt = $fct->getSalt();
        $crypte = $fct->crypterPassword($pwd, $salt);

        print "<br/>\n pwd ==> $pwd";
        print "<br/>\n salt ==> $salt";
        print "<br/>\n crypte ==> $crypte";




        $data = [
            'uti' => $uti,
            'mdp' => $crypte,
            'salt' => $salt,
            'applic' => $applic,
            'statut' => $statut,
            'nbresms' => $nbresms,
            'smsenvoye' => $smsenvoye,
        ];


        print "<br/>\n<pre>";
        print_r($data);

        # verifier si l'utilisateur existe deja. si c est le cas faire un update sinon inserer une nouvelle ligne.
        $sql = "select * from connexion where uti = :uti";
        $stmt = $dbh->prepare($sql);
        $stmt->execute(['uti' => $uti]);
        $connexion = $stmt->fetchAll();
        var_dump($connexion);
        // exit("<br/>\n------------------quitter");

        if (empty($connexion)) {


            $sql = "INSERT INTO connexion (uti, mdp, salt, applic, statut, nbresms, smsenvoye) VALUES (:uti, :mdp, :salt, :applic, :statut, :nbresms, :smsenvoye)";
            $stmt = $dbh->prepare($sql);
            $res_insert = $stmt->execute($data);

            print_r($sql);

            var_dump($res_insert);
            $count = $stmt->rowCount();

            if ($count == '0') {
                echo "Failed !";
            } else {
                echo "Success !";
            }
            var_dump($count);
        } else { // if(empty($connexion)){
            # update
            $sql = "update connexion SET uti = :uti, mdp = :mdp, salt = :salt, applic = :applic, statut = :statut, nbresms = :nbresms, smsenvoye = :smsenvoye WHERE uti = :uti";
            $stmt = $dbh->prepare($sql);
            $res = $stmt->execute($data);
            print_r($sql);

            var_dump($res);
            $count = $stmt->rowCount();

            if ($count == '0') {
                echo "Failed !";
            } else {
                echo "Success !";
            }
            var_dump($count);
        } /// if(empty($connexion)){



    } else {
        print "<br/>\n AUCUN PARAMETRE SAISI";
    }
} catch (PDOException $th) {
    print "<br/>\n<pre>";
    print_r($th);
}

$out1 = ob_get_contents();
// var_dump($out1);
$fp = fopen($file_createuti_log, 'w+');
fwrite($fp, $out1);
fclose($fp);
