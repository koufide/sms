<?php
/**
 * charger le contenu des fichiers PUSH_SMS dans la table chargesms
 * 22/06/2019
 * Fidelin KOUAME
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION CHARGE SMS ====== <br/>\n" . $now->format('d/m/Y H:i:s');



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

$file_lock_charge_sms = $param->files->file_lock_charge_sms;
$file_lock_charge_sms = $log_rep . $file_lock_charge_sms;



// D'autres manières d'appeler error_log():
#error_log("TEST !", 3, $log_rep . $file_alert_log);





if (file_exists($file_lock_charge_sms)) {
    print("<br/>\n exit! traitement encours...$file_lock_charge_sms");
    exit(0);
}


# verifier s il y a des abonnés
$sql = "SELECT count(*) FROM   abonnement a ";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$number_of_rows = $stmt->fetchColumn();
print "\n<br/> number_of_rows: $number_of_rows";
// $abonnements = $stmt->fetchAll();
// var_dump($number_of_rows);

if ($number_of_rows == '0') {
    $res_send = $fct->sendSMS($fct->getMyTel(), "Erreur chargesms.php:  $number_of_rows abonnes. Table abonnement vide");
    $myfile = fopen("$file_lock_charge_sms", "w");
    exit(0);
}

// exit("<br/>\n --------quitter manuellement---------");


$ftp_outgoing_rep = $param->reps->ftp_outgoing;
$push_rep = $ftp_outgoing_rep;


$arch_outgoing = $param->reps->arch_outgoing;


if (!is_dir($arch_outgoing)) {
    mkdir($arch_outgoing);
}

$tab_messages = array();
$trouve = false;


if ($dossier = opendir($push_rep)) {
    while (false !== ($fichier = readdir($dossier))) {
        if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
            // var_dump($fichier);
            $pathinfo = pathinfo($fichier);
            // print "<br/>\n pathinfo : = ";
            var_dump($pathinfo);

            $ext = $pathinfo['extension'];
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];

            // exit("<br/>\n --------quitter manuellement---------");


            #if (preg_match("/^arch_PUSH/i", $filename)) {   # RELANCER LES FICHIERS ARCHIVES
            if (preg_match("/^PUSH/i", $filename)) {

                $myfile = fopen("$file_lock_charge_sms", "w");

                print "<br/>\n echo trouve ==> $filename";



                print "<br/>\n Recherche ORA- " . strpos(file_get_contents("$push_rep/$fichier"), 'ORA-');

                # 25102019 koufide / chercher une erreur dans le fichier
                if (strpos(file_get_contents("$push_rep/$fichier"), 'ORA-') !== false) {

                    #deplacer le fichier
                    // rename($push_rep  . $fichier, $arch_outgoing . 'error2_' . $filename . '.' . $ext . ' # ' . $file_lock_charge_sms);
                    rename($push_rep  . $fichier, $arch_outgoing . 'error2_' . $filename . '.' . $ext);
                    //print "<br/>\n test ORA- :" . "$push_rep . '/' . $fichier, $arch_outgoing . '/error2_' . $filename . '.' . $ext . ' # ' . $file_lock_charge_sms";
                    ##############################################
                    ///$fct->returnError2(null, $push_rep . '/' . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext . ' # ' . $file_lock_charge_sms);
                    $res_send = $fct->sendSMS($fct->getMyTel(), $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);


                    # ajoute le 23102019 koufide. permet de continuer les traitements apres une erreur
                    #supprimer le lock
                    unlink($file_lock_charge_sms);
                } else {

                    $has_error_nbre_car = false;

                    $handle = fopen("$push_rep/$fichier", "r");

                    if ($handle) {

                        // $tab_destinations = [];
                        // $tmp_destinations = [];

                        while (($line = fgets($handle)) !== false) {

                            if ($line != "." && $line != "..") {
                                print "<br/>\n line dans le fichier : => ";
                                var_dump("fichier $fichier line : " . $line);

                                # nbre d'occurances # dans 
                                $nbre_de_dieses =  substr_count($line, '#');
                                print("nbre_de_dieses: " . $nbre_de_dieses);


                                $tab_line = explode('#', $line);
                                print "<br/>\n tab_line : => ";
                                var_dump($tab_line);

                                // if (!empty($tab_line) ) {
                                if (!empty($tab_line) and $nbre_de_dieses >= 2) {
                                    // var_dump($tab_line);

                                    $code = $tab_line[0];
                                    $compte = $tab_line[1];
                                    $message = $tab_line[2];
                                    // $message = $fct->supprimerRetourChariot($message);
                                    $message = $fct->replaceSpecialChar($message);

                                    print "<br/>\n  code:  $code";
                                    print "<br/>\n  compte:   $compte";
                                    print "<br/>\n  message:   $message";
                                    $nbre_car_message =  strlen($message);
                                    print "<br/>\n  nbre_caractere_message: $nbre_car_message";

                                    if ($nbre_car_message > '160') {

                                        $has_error_nbre_car = true;
                                    } else {




                                        $data['service'] = $code;
                                        $data['compte'] = $compte;
                                        $data['message'] = $message;
                                        $data['nomfic'] = $basename;
                                        $data['traite'] = 0;
                                        $data['datecharge'] = $now->format('Y/m/d H:i:s');
                                        print_r($data);

                                        $sql = "INSERT INTO chargesms(service, compte, message, nomfic, traite, datecharge) VALUES(:service , :compte , :message, :nomfic , :traite, :datecharge)";
                                        $stmt = $dbh->prepare($sql);
                                        //print_r($sql);
                                        // //var_dump($data);

                                        try {
                                            //code...
                                            $res_insert = $stmt->execute($data);
                                            //print_r($res_insert);
                                        } catch (\Throwable $th) {
                                            ############################################# DEPLACER LE FICHIER SI Y A UNE ERREUR
                                            #deplacer le fichier
                                            rename($push_rep . '/' . $fichier, $arch_outgoing . '/error_' . $filename . '.' . $ext . ' # ' . $file_lock_charge_sms);
                                            ##############################################
                                            $fct->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());

                                            # ajoute le 23102019 koufide. permet de continuer les traitements apres une erreur
                                            #supprimer le lock
                                            unlink($file_lock_charge_sms);
                                        }
                                    } //if($nbre_car_message > '160'){



                                } //if(!empty($tab_line)){


                            } // if ($line != "." && $line != "..") {

                        } //while (($line = fgets($handle)) !== false) {

                        fclose($handle);
                    } else { // if ($handle) {
                        // error opening the file.
                        print "<br/>\n erreur ouverture du fichier ";
                    }



                    print "<br/>\n has_error_nbre_car: $has_error_nbre_car ";

                    #informer sur l'exception du nbre de carac sup à 160
                    if ($has_error_nbre_car) {
                        $res_send = $fct->sendSMS($fct->getMyTel(), "Nbre CAR SUP à 160 dans fichier: " . $filename . '.' . $ext);
                    }

                    #deplacer le fichier
                    rename($push_rep . '/' . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);

                    #supprimer le lock
                    unlink($file_lock_charge_sms);
                }  // if (strpos(file_get_contents("$push_rep/$fichier"), 'ORA-') !== false) {





            } else { // if (preg_match("/^PUSH/i", $filename)) {
                print "<br/>\n echo fichier PUSH non trouve";
            }
        } // if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {

    } //while (false !== ($fichier = readdir($dossier))) {
    closedir($dossier);
} else {
    echo 'Le dossier n\' a pas pu être ouvert';
}

$out1 = ob_get_contents();
$myfile = file_put_contents('log/chargesms_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
