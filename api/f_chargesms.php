<?php

/**
 * charger le contenu des fichiers M*PUSH dans la table f_chargesms
 * 11/09/2021
 * Fidelin KOUAME
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// if (preg_match("#^MPUSH|^m2push#i", "M2PUSH_202110090801.txt")) {
//     print("<br/>\n\v trouve: ");
// }else{
//     print("<br/>\n\v non trouve: ");
// }

// $res = preg_match("#^M[23456]PUSH#i", "MPUSH_202110090801.txt");

// $res = preg_match("#^M[0-9]PUSH#i", "M8PUSH_202110090801.txt");


// $res = preg_match("#^M[0-9]?(PUSH_)+[0-9]+(.txt)+#i", "MPUSH_202110090801.txt");

// if ($res) {
//     print("<br/>\n\v trouve: $res");
// }else{
//     print("<br/>\n\v non trouve: $res");
// }



ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== CHARGEMENT MANUEL M*PUSH ====== <br/>\n" . $now->format('d/m/Y H:i:s');


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

$file_alert_log = $param->files->file_falert_log;

$log_rep = $param->reps->log_rep;


//
$file_lock_charge_sms = $param->files->file_lock_fcharge_sms;
$file_lock_charge_sms = $log_rep . $file_lock_charge_sms;

if (file_exists($file_lock_charge_sms)) {
    print("<br/>\n exit! chargement manuel des sms encours...$file_lock_charge_sms");
    exit(0);
}


$file_lock_charge_abonne = $param->files->file_lock_charge_abonne;
$file_lock_charge_abonne = $log_rep . $file_lock_charge_abonne;

if (file_exists($file_lock_charge_abonne)) {
    unlink($file_lock_charge_sms);
    exit("<br/>\n exit! chargement des abonnes encours...");
}



# verifier s il y a des abonnés
$sql = "SELECT count(*) FROM   abonnement a ";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$number_of_rows = $stmt->fetchColumn();
// print "\n<br/> number_of_rows: $number_of_rows";
// $abonnements = $stmt->fetchAll();
// var_dump($number_of_rows);

if ($number_of_rows == '0') {
    $res_send = $fct->sendSMS($fct->getMyTel(), "Erreur chargesms.php:  $number_of_rows abonnes. Table abonnement vide");
    // $myfile = fopen("$file_lock_charge_sms", "w"); //koufide 01052021 0944 correction erreur chargement
    unlink($file_lock_charge_sms);  // ++++ ajoute
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



// $message = $fct->replaceSpecialChar("é à â î ï ç Î Ï ");
// print("<br/>\n\v message: $message");

// exit("<br/>\n----------quit");





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
            // if (preg_match("/^PUSH/i", $filename)) {

            // $res = preg_match("#^M[0-9]?(PUSH_)+[0-9]+(.txt)+#i", $filename);

            // $res = preg_match("#^F[0-9]?(PUSH_)+[0-9]+(.txt)+#i", $filename);
            $res = preg_match("#^F[0-9]?PUSH_[0-9]+#i", $filename);

            print("<br/>\n trouve ?: $res ");
            // exit("<br/>\n --------quitter manuellement---------");


            if ($res) {


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

                    // $has_error_nbre_car = false;

                    $handle = fopen("$push_rep/$fichier", "r");

                    if ($handle) {

                        // $tab_destinations = [];
                        // $tmp_destinations = [];

                        while (($line = fgets($handle)) !== false) {

                            if ($line != "." && $line != "..") {

                                print "<br/>\n line dans le fichier : => ";
                                var_dump("fichier: $fichier, line : " . $line);

                                $nbre_car_message = strlen(str_replace(' ', '', trim($line)));
                                print("<br/>\n nbre_car_message de la line: $nbre_car_message \v");

                                if ($nbre_car_message != 0) {
                                    # nbre d'occurances # dans 
                                    $nbre_de_dieses =  substr_count($line, '#');
                                    print("<br/>\n nbre_de_dieses: " . $nbre_de_dieses);

                                    if ($nbre_de_dieses != 1) {
                                        print("<br/>\n Fichier: $filename mal formaté ");

                                        #renommer et deplacer le fichier
                                        rename($push_rep  . $fichier, $arch_outgoing . 'error2_' . $filename . '.' . $ext);

                                        # envoyer un sms pour informer
                                        $res_send = $fct->sendSMS($fct->getMyTel(), $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);
                                    } else {

                                        $tab_line = explode('#', $line);

                                        print "<br/>\n tab_line : => ";
                                        var_dump($tab_line);

                                        if (!empty($tab_line)) {
                                            // var_dump($tab_line);

                                            $message = $tab_line[0];
                                            $tel = $tab_line[1];
                                            $tel = $fct->getTel($tel);

                                            $message = $fct->replaceSpecialChar($message);
                                            // $message = $fct->enleveaccents($message);

                                            print "<br/>\n  message:   $message";
                                            $nbre_car_message =  strlen($message);
                                            print "<br/>\n  nbre_caractere_message: $nbre_car_message <br/>\n \v";


                                            //========== VERIFIER LA PRESENCE DE DOUBLON
                                            $sql = " select * from f_chargesms
                                             where date_format(datecharge,'%d/%m/%Y') = date_format(NOW(),'%d/%m/%Y') 
                                             and tel=:tel 
                                             and message=:message
                                             and nomfic=:nomfic";

                                            $params = [
                                                'tel' => $tel,
                                                'message' => $message,
                                                'nomfic' => $basename
                                            ];

                                            $res_doublon = $fct->checkDoublon($sql, $params);
                                            print "<br/>\n  res_doublon:   $res_doublon";

                                            if ($res_doublon != 0) {
                                                print "<br/>\n chargemenet deja effectué: $basename,  $tel, $message";
                                            } else {

                                                // if ($nbre_car_message > '160') {
                                                //     $has_error_nbre_car = true;
                                                // } else {

                                                // $data['service'] = $code;
                                                // $data['compte'] = $compte;
                                                $data['tel'] = $tel;
                                                $data['message'] = $message;
                                                $data['nomfic'] = $basename;
                                                $data['traite'] = 0;
                                                $data['datecharge'] = $now->format('Y/m/d H:i:s');
                                                print "<pre>";
                                                print_r($data);
                                                print "</pre>";



                                                $sql = "INSERT INTO f_chargesms(tel,  message, nomfic, traite, datecharge) VALUES(:tel,  :message, :nomfic , :traite, :datecharge)";
                                                $stmt = $dbh->prepare($sql);
                                                //print_r($sql);
                                                // //var_dump($data);

                                                try {
                                                    $res_insert = $stmt->execute($data);
                                                    print_r("<br/>\n res_insert: $res_insert");
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
                                                // } //if($nbre_car_message > '160'){

                                            } // if ($res_doublon != 0) {


                                        } //if(!empty($tab_line)){

                                    } //  if($nbre_de_dieses != 1){

                                } // if($nbre_car_message != 0){



                            } // if ($line != "." && $line != "..") {

                        } //while (($line = fgets($handle)) !== false) {

                        fclose($handle);
                    } else { // if ($handle) {
                        // error opening the file.
                        print "<br/>\n erreur ouverture du fichier ";
                    }


                    #deplacer le fichier
                    rename($push_rep  . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);


                    // sleep(120);

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
$myfile = file_put_contents('log/f_chargesms_' . $now->format('Y_m_d_H_i_s') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
