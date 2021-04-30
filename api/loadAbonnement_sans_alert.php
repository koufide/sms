<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION LOAD ABONNEMENT 3 ====== <br/>\n" . $now->format('d/m/Y H:i:s');

define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

// $chemin = getcwd();
$chemin = API_REP;

// $chemin = getcwd() . '/api/params.xml';
$chemin_params = $chemin . '/params.xml';
// var_dump($chemin);
// exit("<br/>\n-------quitter");

if (file_exists($chemin_params)) {
    $param = simplexml_load_file($chemin_params);
    // echo"<pre>";
    // print_r($param);
} else {
    exit('Echec lors de l\'ouverture du fichier ' . $chemin_params);
}


//$chemin = getcwd();

require_once($chemin . '/Fonction.php');
$fct = new Fonction();


# creer un fichier lock pour empecher l'envoi des sms



// exit("<br/>\n-------quitter");   


//$param=simplexml_load_file($chemin) or die("Error simplexml_load_file $chemin");
// echo"<pre>";
// var_dump($param);

$adr = $param->serveur->adr;
$username = $param->serveur->uti;
$password = $param->serveur->mdp;
$schema = $param->bd->cgbtst;
$tnsname = $param->tnsname->cgbtst;
$oci_vue = $param->serveur->vue->Abonnement;

$log_rep = $param->reps->log_rep;

// $old = umask(0);
// mkdir("log_rep", 0777);
// umask($old);

# creer repertoire s'il n existe pas
if (!file_exists($log_rep)) {
    mkdir($log_rep, 0777, true);
}



$api_rep = $param->reps->api_rep;
$nom_file_abonnesms = $param->files->file_abonnesms;

$file_abonnesms = $api_rep . $nom_file_abonnesms;

$ftp_outgoing_rep = $param->reps->ftp_outgoing;
$push_rep = $ftp_outgoing_rep;

$file_lock_push_sms = $param->files->file_lock_push_sms;
$file_lock_push_sms = $log_rep . $file_lock_push_sms;
// var_dump($file_lock_push_sms);
// exit("<br/>\n-------quitter");


$arch_outgoing = $param->reps->arch_outgoing;



if (file_exists($file_lock_push_sms)) {
    // exit(0);
    exit("<br/>\n exit! traitement encours...");
} else {
    $myfile = fopen("$file_lock_push_sms", "w");
}


// var_dump($adr);
// var_dump($username);
// var_dump($password);
// var_dump($schema);
// var_dump($tnsname);

// var_dump($api_rep);
// var_dump($file_abonnesms);


// exit("----------quittter-----------");

$mysql_adr = $param->serveur_mysql->adr;
$mysql_username = $param->serveur_mysql->uti;
$mysql_password = $param->serveur_mysql->mdp;
$mysql_bd = $param->serveur_mysql->bd;
$mysql_table = $param->serveur_mysql->table->Abonnement;

$msg_abonne = $param->messages->abonnement;
// var_dump($msg_abonne);
// exit("----------quittter-----------");

// $mysql_table = $param->serveur_mysql->table;

// var_dump($mysql_adr);
// var_dump($mysql_username);
// var_dump($mysql_password);
// var_dump($mysql_bd);



$i = 0;
//$retour = true;

// exit("----------quittter-----------");

try {
    //==================================================================================================================

    // exit("<br/>\n------------------------ stop ");
    if ($dossier = opendir($push_rep)) {

        // var_dump($push_rep);
        // exit("<br/>\n------------------------ stop ");


        while (false !== ($fichier = readdir($dossier))) {

            // var_dump($fichier);
            // var_dump($dossier);
            // exit("<br/>\n------------------------ stop ");


            if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
                // var_dump($fichier);
                $pathinfo = pathinfo($fichier);
                // var_dump($pathinfo);
                $ext = $pathinfo['extension'];
                $basename = $pathinfo['basename'];
                $filename = $pathinfo['filename'];

                // var_dump($fichier);
                // var_dump($dossier);
                // exit("<br/>\n------------------------ stop ");


                if (preg_match("/^" . $nom_file_abonnesms . "/i", $filename)) {


                    # 26102019 / controle sur la taille du fichier
                    $taille_fic = filesize($push_rep . $fichier);
                    //print "<br/>\n taille_fic: $taille_fic";
                    if ($taille_fic == '0') {

                        #deplacer le fichier
                        rename($push_rep . '/' . $fichier, $arch_outgoing . '/error2_' . $filename . '.' . $ext);

                        $res_send = $fct->sendSMS($fct->getMyTel(), 'fichier vide ' . $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);
                    } else {

                        // print "<br/>\n " . $push_rep . $fichier;
                        // $erreurORA = $fct->chercherErreurFichier($push_rep . $fichier, 'ORA-');
                        // print "<br/>\n erreurORA: $erreurORA";

                        ### 26102019 koufide /  controler les erreurs ORA- dans le fichier
                        if ($fct->chercherErreurFichier($push_rep . $fichier, 'ORA-') == true) {

                            #deplacer le fichier
                            rename($push_rep . '/' . $fichier, $arch_outgoing . '/error2_' . $filename . '.' . $ext);

                            $res_send = $fct->sendSMS($fct->getMyTel(), 'ORA- dans ' . $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);

                            // supprimer le fichier
                            //unlink($file_lock_push_sms);
                        } else {

                            $res = array();

                            $conn = new PDO("mysql:host=$mysql_adr;dbname=$mysql_bd", $mysql_username, $mysql_password);
                            // set the PDO error mode to exception
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            // echo "<br/>\n Connected successfully"; 
                            // $res['conn'] = "Connexion [$mysql_adr, $mysql_bd,  $mysql_username] ";
                            $res['conn'] = " OK ";

                            // var_dump($res);
                            // var_dump($conn);
                            // exit("<br/>\n------------------------ stop ");

                            //$now = new DateTime('NOW', new DateTimeZone(('UTC')));





                            // print "<br/>\n echo trouve ==> $filename";

                            // var_dump($nom_file_abonnesms);
                            // var_dump($filename);
                            // exit("<br/>\n------------------------ stop ");


                            $handle = fopen($push_rep . $fichier, "r");

                            // var_dump($push_rep);
                            // var_dump($fichier);
                            // var_dump($push_rep . $fichier);
                            // var_dump($handle);
                            // exit("<br/>\n------------------------ stop ");


                            if ($handle) {
                                // var_dump($handle);
                                // exit("<br/>\n------------------------ stop ");


                                try {
                                    //code...
                                    // $save_table = $mysql_table . '_save' . $now->format('YmdHis');
                                    $save_table = $mysql_table . '_save';
                                    // $sql = "CREATE TABLE `$save_table` AS SELECT * FROM `$mysql_table` ";
                                    $sql = "CREATE TABLE IF NOT EXISTS `$save_table` AS SELECT * FROM `$mysql_table` WHERE 1=2 ; ALTER TABLE `$save_table` ADD `save_date` DATETIME NULL AFTER `exonere_facture_push` ";
                                    $res['create_save_table'] = $sql;
                                    $temp = $conn->exec($sql);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $res['create_save_table'] = $th->getMessage();
                                } //try



                                // var_dump($res);
                                // var_dump($temp);
                                // exit("<br/>\n------------------------ stop --------------");


                                try {
                                    //code...
                                    #SAAUVEGARDE DE
                                    $sql = "INSERT INTO abonnement_save (id, agence, agencelib, rm, typ, typlib, client, phone, compte, datouv, datfrm, typcptlib, ncg, libelle, formule, databon, userabon, nom_userabon, valide, datvalidation, uservalide, nom_uservalide, datactif, useractif, nom_useractif, actif, datedesactif, datefinabon, userresili, nom_userresili, exonere_facture_pull, exonere_facture_push, save_date) SELECT id, agence, agencelib, rm, typ, typlib ,client, phone, compte, datouv, datfrm, typcptlib, ncg, libelle, formule, databon, userabon, nom_userabon, valide, datvalidation, uservalide, nom_uservalide, datactif, useractif, nom_useractif, actif, datedesactif, datefinabon, userresili, nom_userresili, exonere_facture_pull, exonere_facture_push ,NOW() FROM abonnement";
                                    //$res['insert_save_table'] = $sql;
                                    $temp = $conn->exec($sql);
                                    // var_dump($temp);
                                    $res['nbre_insert_save_table'] = $temp;
                                } catch (\Throwable $th) {
                                    // print"<pre>";
                                    //throw $th;
                                    // print_r($th);
                                    $res['nbre_insert_save_table'] = $th->getMessage();
                                }

                                //  var_dump($res);
                                // var_dump($temp);
                                // exit("<br/>\n------------------------ stop --------------");




                                $temp_table = 'temp_' . $mysql_table;
                                $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$temp_table` ; SET FOREIGN_KEY_CHECKS = 1; ";
                                $temp = $conn->exec($sql);
                                // echo " < br / > \n DROP TABLE successfully ";
                                $res['drop_temp_table'] = $sql;


                                // var_dump($res);
                                // exit("<br/>\n------------------------ stop --------------");





                                try {
                                    //code...
                                    $sql = "CREATE TABLE `$temp_table` AS SELECT * FROM `$mysql_table` WHERE 1=2";
                                    $res['create_temp_table'] = $sql;
                                    $temp = $conn->exec($sql);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $res['create_temp_table'] = $th->getMessage();
                                }



                                ### avant de traiter / copier le fichier dans le repertoire log et traite

                                $res['copy_fichier'] = copy($ftp_outgoing_rep . $fichier, $log_rep . $fichier);
                                // var_dump($res); 
                                // exit("<br/>\n------------------------ stop --------------");

                                try {
                                    $res['nbre_lignes'] = chargerDonneesTable($conn, $temp_table, $log_rep . $fichier);
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $res['nbre_lignes'] = $th->getMessage();
                                }

                                # supprimer la copie du fichier temporaire
                                $res['unlink_fichier'] = unlink($log_rep . $fichier);


                                // var_dump($conn);
                                // var_dump($temp_table);
                                // var_dump($file_abonnesms);
                                // var_dump($ftp_outgoing_rep . $fichier);
                                // var_dump($res);
                                // exit("<br/>\n------------------------ stop ");
                                // var_dump($conn);
                                // exit("<br/>\n------------------------ stop ");


                                ### comparer les lignes.
                                # si c est nouvel abonne , lui envoyer un message d'abonnement.
                                # si c est un abonnement on ne lui envoie pas de message

                                // select all users
                                try {
                                    $sql = "SELECT * FROM `$temp_table`";
                                    $stmt = $conn->query($sql);
                                    //code...

                                    $i = 0;
                                    while ($row = $stmt->fetch()) {
                                        $i++;


                                        // echo $row['compte'] . " - ";
                                        // echo $row['phone'] . " - ";
                                        // echo $row['actif'] . "<br />\n";

                                        // select a particular user by id
                                        $sql = "SELECT * FROM `$mysql_table` WHERE compte=:compte and phone=:phone";
                                        $stmt2 = $conn->prepare($sql);
                                        // $stmt->execute(['compte' => $row['compte']]);
                                        // $stmt->execute(['compte' => '11042050008','phone'=> $row['phone']]);
                                        $stmt2->execute(['compte' => $row['compte'], 'phone' => $row['phone']]);
                                        $user = $stmt2->fetch();

                                        // var_dump($user);
                                        if (isset($user) and !empty($user)) { //ANCIEN ABONNE / VERIFIER LE STATUT
                                            // if ($user['phone'] == '22503612783')
                                            // var_dump($user);
                                            if ($user['actif'] != $row['actif']) {
                                                // echo "RAS  <br />\n";
                                                //echo "UPDATE SUR ACTIF <br />\n";

                                                if ($row['actif'] == '1') { //abonne
                                                    // echo "UPDATE ACTIVER OLD ABONNE <br />\n";
                                                } else { //desabonnement
                                                    //RAS
                                                }
                                            }
                                        } else { //NOUVEL ABONNE //VERIFIER LE STATUT
                                            if ($row['actif'] == '1') { //abonne


                                                echo "AJOUTER NOUVEL ABONNE <br />\n";
                                                print "<pre>";
                                                print_r($row);



                                                // echo $user['phone'] . " : PHONE <br />\n";
                                                // var_dump($user);
                                                //print "<br/>\n msg_abonne: $msg_abonne";
                                                // print "<br/>\n ==>: compte " . $row['compte'];
                                                // print "<br/>\n ==>: phone " . $row['phone'];
                                                $msg_abonne = $param->messages->abonnement;
                                                // print "<br/>\n msg_abonne : $msg_abonne";
                                                $msg_abonne = str_replace('[ACCOUNT]', $row['compte'], $msg_abonne);

                                                print "<br/>\n ******** msg_abonne: $msg_abonne";
                                                #if ($row['phone'] == '22503612783') ############################################################

                                                #### 23102019 FIDELIN  / 26102019 
                                                #$res_send = $fct->sendSMS($row['phone'], $msg_abonne);  #### zone commenté ###
                                                #$res_send = $fct->sendSMS('22503612783', $msg_abonne);
                                                #$res_send = $fct->sendSMS('22506736367', $msg_abonne);

                                                // $data = [
                                                //     'service' => '400',
                                                //     'compte' => $row['compte'],
                                                //     'message' => $msg_abonne,
                                                //     'nomfic' => 'PUSH_' . $now->format('YmdHis') . '.txt',
                                                //     'traite' => '0',
                                                //     'datecharge' => $now->format('Y/m/d H:i:s'),
                                                // ];

                                                // $sql = "INSERT INTO chargesms (service, compte, message, nomfic, traite, datecharge) VALUES (:service, :compte, :message, :nomfic, :traite, :datecharge)";
                                                // $stmt = $conn->prepare($sql);
                                                // $res_insert = $stmt->execute($data);

                                                // var_dump($res_insert);
                                                // $count = $stmt->rowCount();

                                                // if ($count == '0') {
                                                //     echo "Failed !";
                                                // } else {
                                                //     echo "Success !";
                                                // }
                                                // var_dump($count);



                                                // $res_send = $fct->sendSMS('22503612783', $msg_abonne);
                                                // var_dump($res_send);
                                                // $mtnci_fct
                                            } else { //desabonnement
                                                //RAS
                                            }
                                        }
                                    } //while
                                    $res['nbre_lignes_read'] = $i;
                                } catch (\Throwable $th) {
                                    //throw $th;
                                    $res['nbre_lignes_read'] = $th->getMessage();
                                } //try  



                                // exit("<br/>\n----------------stop----------------------");

                                # supprimer la table temporaire # 
                                $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$temp_table` ; SET FOREIGN_KEY_CHECKS = 1; ";
                                // use exec() because no results are returned
                                $temp = $conn->exec($sql);
                                // echo " < br / > \n DROP TABLE successfully ";
                                $res['drop_temp_table'] = $sql;


                                # supprimer la table principale et la recharger
                                $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$mysql_table` ; SET FOREIGN_KEY_CHECKS = 1; ";
                                // use exec() because no results are returned
                                $temp = $conn->exec($sql);
                                // echo " < br / > \n DROP TABLE successfully ";
                                $res['drop_table'] = $sql;


                                //exit(" < br / > \n retour : $sql : $temp "); 


                                $sql = "CREATE TABLE IF NOT EXISTS `$mysql_table` (
                                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                                `agence` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `agencelib` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `rm` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `typ` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `typlib` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `client` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `phone` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `compte` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `datouv` date NOT NULL,
                                                `datfrm` date DEFAULT NULL,
                                                `typcptlib` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `ncg` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `libelle` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `formule` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `databon` date NOT NULL,
                                                `userabon` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `nom_userabon` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `valide` tinyint(1) NOT NULL,
                                                `datvalidation` date DEFAULT NULL,
                                                `uservalide` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `nom_uservalide` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `datactif` date NOT NULL,
                                                `useractif` varchar(35) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `nom_useractif` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `actif` tinyint(1) NOT NULL,
                                                `datedesactif` date DEFAULT NULL,
                                                `datefinabon` date DEFAULT NULL,
                                                `userresili` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                `nom_userresili` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                                                `exonere_facture_pull` tinyint(1) NOT NULL,
                                                `exonere_facture_push` tinyint(1) NOT NULL,
                                                PRIMARY KEY (id)
                                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                                            ";


                                // use exec() because no results are returned
                                $conn->exec($sql);
                                // echo "<br/>\n Table V_VIREPREL_CPT created successfully";
                                $res['create_table'] = "CREATE TABLE IF NOT EXISTS [$mysql_table] ";


                                $sql = "LOCK TABLES `$mysql_table` WRITE";
                                $conn->exec($sql);
                                // echo "<br/>\n  $sql";
                                $res['lock_table'] = "$sql";



                                $res['copy_fichier2'] = copy($ftp_outgoing_rep . $fichier, $log_rep . $fichier);
                                // var_dump($res);
                                // exit("<br/>\n------------------------ stop --------------");

                                $res['nbre_lignes2'] = chargerDonneesTable($conn, $mysql_table,  $log_rep . $fichier);

                                # supprimer la copie du fichier temporaire
                                $res['unlink_fichier2'] = unlink($log_rep . $fichier);



                                fclose($handle);
                            } else {
                                // error opening the file.
                            }


                            $sql = "UNLOCK TABLES";
                            $conn->exec($sql);
                            // echo "<br/>\n $sql";
                            $res['unlock_table'] = "$sql";

                            $conn = null; //mysql



                            #deplacer le fichier
                            rename($push_rep . '/' . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);

                            /* Output header */
                            /*
                            header('Content-type: application/json');
                            echo json_encode($res);
                            */
                        } // if($fct->chercherErreurFichier("$push_rep/$fichier",'ORA-')==true){

                    } //  if($taille_fic =='0'){





                } else { /////////////////////////////////////////////////////////
                    //print"<br/>\n echo fichier PUSH non trouve";
                }
            } //if

        } //WHILE
        closedir($dossier);
    } else {
        echo 'Le dossier n\' a pas pu être ouvert';
    }

    //===================================================================================================================

    // exit("<br/>\n----------------stop----------------------");





    //} //retour



} catch (\Throwable $e) {
    // echo "<pre>";
    // print_r($e);
    //echo "<br/>\n Connection failed: " . $e->getMessage();
    $res['conn'] = $e->getMessage();
}


// $res['nbre_lignes'] = "$i";


# supprimer le fichier
unlink($file_lock_push_sms);







function chargerDonneesTable($dbh, $table, $fichier)
{

    // $fichier = '/var/www/html/sms/api/SMS_ABONNE_DESABONNE_20190517569946.txt';
    // $fichier = '/home/ftpuser/ftp/outgoing/SMS_ABONNE_DESABONNE_20190517569946.txt';
    // $fichier = '/var/www/html/sms/api/log/SMS_ABONNE_DESABONNE_20190517569946.txt';

    $sql = "
			LOAD DATA INFILE '$fichier'
			IGNORE
			INTO TABLE $table
			FIELDS TERMINATED BY ';'
			ENCLOSED BY '\"' 
            LINES TERMINATED BY '\\r\\n'
            (agence,
            agencelib,
            rm,
            typ,
            typlib,
            client,
            phone,
            compte,
            @datouv,
            @datfrm,
            typcptlib,
            ncg,
            libelle,
            formule,
            @databon,
            userabon,
            nom_userabon,
            @valide,
            @datvalidation,
            uservalide,
            nom_uservalide,
            @datactif,
            useractif,
            nom_useractif,
            @actif,
            @datedesactif,
            @datefinabon,
            userresili,
            nom_userresili,
            exonere_facture_pull,
            exonere_facture_push )
            SET 
                datouv = STR_TO_DATE(@datouv,'%d-%b-%y') , 
                datfrm = if(@datfrm='',NULL,STR_TO_DATE(@datfrm,'%d-%b-%y')),
                databon = STR_TO_DATE(@databon,'%d-%b-%y'),
                datvalidation = STR_TO_DATE(@datvalidation,'%d-%b-%y'),
                datactif = STR_TO_DATE(@datactif,'%d-%b-%y'),
                datedesactif =  if(@datedesactif='',NULL,STR_TO_DATE(@datedesactif,'%d-%b-%y')), 
                datefinabon =  if(@datefinabon='',NULL,STR_TO_DATE(@datefinabon,'%d-%b-%y')), 
                actif =  if(@actif='A',1,0), 
                valide =  if(@valide='V',1,0) 
        ";
    //datfrm = if(@datfrm='',NULL,STR_TO_DATE(@datfrm,'%d-%b-%y')),

    // SET datouv = STR_TO_DATE(@datouv,'%d-%b-%y') , 
    // datfrm =   STR_TO_DATE(@datfrm,'%d-%b-%y'),
    // databon = STR_TO_DATE(@databon,'%d-%b-%y'),
    // datvalidation = STR_TO_DATE(@datvalidation,'%d-%b-%y'),
    // datactif = STR_TO_DATE(@datactif,'%d-%b-%y'),
    // datedesactif = STR_TO_DATE(@datedesactif,'%d-%b-%y'),
    // databon = STR_TO_DATE(@databon,'%d-%b-%y')

    // print($sql);
    //exit("----");  
    // try {
    //code...
    $res = $dbh->exec("$sql");
    //or die(print_r($dbh->errorInfo(), true))
    // } catch (\Throwable $th) {
    //throw $th;
    // }

    //var_dump("chargerDonneesTable $table from $fichier : ".$res);


    // var_dump($dbh);
    // var_dump($table);
    // var_dump($fichier);
    // var_dump($res);
    // exit("<br/>\n------------------------ stop -------------- ");

    return $res;
}

$out1 = ob_get_contents();
$myfile = file_put_contents('log/loadAbonnement_sans_alert_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
