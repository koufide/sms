<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "<br/><br/><br/>\n\n\n ========== EXECUTION LOAD ABONNEMENT 4 ====== <br/>\n" . $now->format('d/m/Y H:i:s');

define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin = API_REP;

$chemin_params = $chemin . '/params.xml';




if (file_exists($chemin_params)) {
    $param = simplexml_load_file($chemin_params);
} else {
    exit('Echec lors de l\'ouverture du fichier ' . $chemin_params);
}


require_once($chemin . '/Fonction.php'); 
$fct = new Fonction();


$adr = $param->serveur->adr;
$username = $param->serveur->uti;
$password = $param->serveur->mdp;
$schema = $param->bd->cgbtst;
$tnsname = $param->tnsname->cgbtst;
$oci_vue = $param->serveur->vue->Abonnement;

$log_rep = $param->reps->log_rep;

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


$arch_outgoing = $param->reps->arch_outgoing;


// # supprimer le fichier
// unlink($file_lock_push_sms); ///fidelin a supprimer apres test

if (file_exists($file_lock_push_sms)) {
    exit("<br/>\n exit! traitement encours...");
} else {
    $myfile = fopen("$file_lock_push_sms", "w");
}



// exit("----------quittter-----------");

$mysql_adr = $param->serveur_mysql->adr;
$mysql_username = $param->serveur_mysql->uti;
$mysql_password = $param->serveur_mysql->mdp;
$mysql_bd = $param->serveur_mysql->bd;
$mysql_table = $param->serveur_mysql->table->Abonnement;

$msg_abonne = $param->messages->abonnement;

$i = 0;

try {

    if ($dossier = opendir($push_rep)) {



       


        while (false !== ($fichier = readdir($dossier))) {


            if ($fichier != '.' && $fichier != '..' && $fichier != 'index.php' && $fichier != 'archive') {
                $pathinfo = pathinfo($fichier);
                $ext = $pathinfo['extension'];
                $basename = $pathinfo['basename'];
                $filename = $pathinfo['filename'];

               


                if (preg_match("/^" . $nom_file_abonnesms . "/i", $filename)) {


                    # 26102019 / controle sur la taille du fichier
                    $taille_fic = filesize($push_rep . $fichier);
                    if ($taille_fic == '0') {

                        #deplacer le fichier
                        rename($push_rep . '/' . $fichier, $arch_outgoing . '/error2_' . $filename . '.' . $ext);

                        $res_send = $fct->sendSMS($fct->getMyTel(), 'fichier vide ' . $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);
                    
                    } else {

                        // print("etape debug 1");
                       

                        ### 26102019 koufide /  controler les erreurs ORA- dans le fichier
                        if ($fct->chercherErreurFichier($push_rep . $fichier, 'ORA-') == true) {

                            // print("etape debug 2");
                            // exit("----------quittter-----------");

                            #deplacer le fichier
                            rename($push_rep . '/' . $fichier, $arch_outgoing . '/error2_' . $filename . '.' . $ext);

                            $res_send = $fct->sendSMS($fct->getMyTel(), 'ORA- dans ' . $push_rep  . $fichier . ' , ' .  '/error2_' . $filename . '.' . $ext);

                            // supprimer le fichier
                            //unlink($file_lock_push_sms);
                        } else {

                            // print("etape debug 3");
                            // exit("----------quittter-----------");

                            $res = array();

                            $conn = new PDO("mysql:host=$mysql_adr;dbname=$mysql_bd", $mysql_username, $mysql_password);
                            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $res['conn'] = " OK ";

                            


                            $handle = fopen($push_rep . $fichier, "r");

                           
                            if ($handle) {

                                // print("<br/>\n etape debug 3  $mysql_table "); 
                                // exit("<br/>\n----------quittter-----------");
    

                                // print("<br/>\n etape debug 4 --> $save_table , $mysql_table"); 
                                //  exit("<br/>\n----------quittter-----------");

                                // try {
                                //     $sql = "CREATE TABLE IF NOT EXISTS `$mysql_table` AS SELECT * FROM `$mysql_table` WHERE 1=2 ; ALTER TABLE `$save_table` ADD `save_date` DATETIME NULL AFTER `exonere_facture_push` ";
                                //     $res['create_save_table'] = $sql;
                                //     $temp = $conn->exec($sql);
                                // }catch(Exception $e){
                                //     echo 'Could not connect : ' . $e->getMessage();
                                // } catch (\Throwable $th) {
                                //     $res['create_save_table'] = $th->getMessage();
                                //     print($th);
                                // } //try


                                try {
                                    $save_table = $mysql_table . '_save';

                                    //  print("<br/>\n etape debug 4 --> $save_table,  $mysql_table "); 

                                    //  exit("<br/>\n----------quittter-----------");

                                    $sql = "CREATE TABLE IF NOT EXISTS `$save_table` AS SELECT * FROM `$mysql_table` WHERE 1=2 ; ALTER TABLE `$save_table` ADD `save_date` DATETIME NULL AFTER `exonere_facture_push` ";
                                    $res['create_save_table'] = $sql;
                                    $temp = $conn->exec($sql);

                                }catch(Exception $e){
                                    echo 'Could not connect : ' . $e->getMessage();
                                } catch (\Throwable $th) {
                                    $res['create_save_table'] = $th->getMessage();
                                    print($th);
                                } //try

                                // print("<br/>\n etape debug 3  $mysql_table "); 
                                // exit("<br/>\n----------quittter-----------3");

                              


                                try {
                                    #SAAUVEGARDE DE
                                    $sql = "INSERT INTO abonnement_save (id, agence, agencelib, rm, typ, typlib, client, phone, compte, datouv, datfrm, typcptlib, ncg, libelle, formule, databon, userabon, nom_userabon, valide, datvalidation, uservalide, nom_uservalide, datactif, useractif, nom_useractif, actif, datedesactif, datefinabon, userresili, nom_userresili, exonere_facture_pull, exonere_facture_push, save_date) SELECT id, agence, agencelib, rm, typ, typlib ,client, phone, compte, datouv, datfrm, typcptlib, ncg, libelle, formule, databon, userabon, nom_userabon, valide, datvalidation, uservalide, nom_uservalide, datactif, useractif, nom_useractif, actif, datedesactif, datefinabon, userresili, nom_userresili, exonere_facture_pull, exonere_facture_push ,NOW() FROM abonnement";
                                    $temp = $conn->exec($sql);
                                    $res['nbre_insert_save_table'] = $temp;
                                } catch (\Throwable $th) {
                                    $res['nbre_insert_save_table'] = $th->getMessage();
                                }

                               print( "<br/>\n nbre_insert_save_table in abonnement_save :  $temp" );
                            //    print("<br/>\n etape debug 3  $mysql_table "); 
                            //    exit("<br/>\n----------quittter-----------3");


                                $temp_table = 'temp_' . $mysql_table;
                                $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$temp_table` ; SET FOREIGN_KEY_CHECKS = 1; ";
                                $temp = $conn->exec($sql);
                                // echo " < br / > \n DROP TABLE successfully ";
                                $res['drop_temp_table'] = $sql;

                                // print("<br/>\n etape debug 4  $mysql_table "); 
                                // exit("<br/>\n----------quittter-----------4");

                               



                                try {
                                    $sql = "CREATE TABLE `$temp_table` AS SELECT * FROM `$mysql_table` WHERE 1=2";
                                    $res['create_temp_table'] = $sql;
                                    $temp = $conn->exec($sql);
                                } catch (\Throwable $th) {
                                    $res['create_temp_table'] = $th->getMessage();
                                }

                                // var_dump($res);
                                // print("<br/>\n etape debug 4  $mysql_table "); 
                                // exit("<br/>\n----------quittter-----------4");

                               



                                ### avant de traiter / copier le fichier dans le repertoire log et traite

                                $res['copy_fichier'] = copy($ftp_outgoing_rep . $fichier, $log_rep . $fichier);

                                // var_dump($res);
                                // print("<br/>\n etape debug 5  "); 
                                // exit("<br/>\n----------quittter-----------5");

                                try {
                                    $res['nbre_lignes'] = chargerDonneesTable($conn, $temp_table, $log_rep . $fichier);
                                } catch (\Throwable $th) {
                                    $res['nbre_lignes'] = $th->getMessage();
                                }

                                // var_dump($res);
                                // print("<br/>\n etape debug 6  "); 
                                // exit("<br/>\n----------quittter-----------6");


                                // print("<br/>\n nbre_lignes: chargerDonneesTable: ");
                                // var_dump($res['nbre_lignes']) ;
                               
                                // exit("<br/>\n----------quittter-----------1");

                                # supprimer la copie du fichier temporaire
                                $res['unlink_fichier'] = unlink($log_rep . $fichier);

                                // var_dump($res);
                                // print("<br/>\n etape debug 6  "); 
                                // exit("<br/>\n----------quittter-----------6");

                                ### comparer les lignes.
                                # si c est nouvel abonne , lui envoyer un message d'abonnement.
                                # si c est un abonnement on ne lui envoie pas de message

                                try {

                                    print("<br/>\n temp_table: $temp_table ");

                                    $sql = "SELECT * FROM `$temp_table`";
                                    $stmt = $conn->query($sql);

                                    // var_dump($res);
                                    // print("<br/>\n etape debug 7  "); 
                                    // // exit("<br/>\n----------quittter-----------7");


                                    $progression = ("log/progression.log");
                                   if(file_exists($progression)){
                                       unlink($progression);
                                   }

                                    $i = 0;
                                    while ($row = $stmt->fetch()) {
                                        $i++;

                                        print("<br/>\n i => $i ");

                                        // var_dump($row); print("<br/>\n");

                                        // var_dump($res); print("<br/>\n");

                                        // file_put_contents($progression,$row, FILE_APPEND);
                                        // file_put_contents($progression,"<br/>\n<br/>\n i => $i <br/>\n", FILE_APPEND);
                                        // file_put_contents($progression,  $row['compte'] + " => "+ $row['phone'] + " => "+  $row['client'] +"\n", FILE_APPEND);


                                        // var_dump($row);

                                        $compte = $row['compte'];
                                        $phone = $row['phone'];
                                        $actif = $row['actif'];
                                        $client = $row['client'];

                                        $chaine = "\n<br/> client: $client /// Numero: ".$i." /// compte: ".$compte . " /// phone: ".$phone." /// actif: ".$actif ."\n<br/>";
                                        print("<br/>\n chaine: $chaine");
                                        file_put_contents($progression,  $chaine, FILE_APPEND);


                                        // if($i == 30){

                                        //     print("<br/>\n etape debug 7  "); 
                                        //     exit("<br/>\n----------quittter-----------7");
                                        // }

                                        $sql = "SELECT * FROM `$mysql_table` WHERE compte=:compte and phone=:phone";
                                        $stmt2 = $conn->prepare($sql);
                                        $stmt2->execute(['compte' => $row['compte'], 'phone' => $row['phone']]);
                                        $user = $stmt2->fetch();


                                        
                                        if (isset($user) and !empty($user)) { //ANCIEN ABONNE / VERIFIER LE STATUT
                                            if ($user['actif'] != $row['actif']) {

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


                                                $msg_abonne = $param->messages->abonnement;

                                                $compte_hash = $fct->getCompteHash($row['compte']);

                                                $msg_abonne = str_replace('[ACCOUNT]', $compte_hash, $msg_abonne);

                                                print "<br/>\n ******** msg_abonne: $msg_abonne";

                                                $res_send = $fct->sendSMS_V2($row['phone'], $msg_abonne, true, false);

                                            } else { //desabonnement
                                                //RAS
                                            }
                                        }


                                    } //while

                                    // fclose($progression);

                                    $res['nbre_lignes_read'] = $i;

                                    // var_dump($res); print("<br/>\n");
                                    // print("<br/>\n etape debug 8  "); 
                                    // exit("<br/>\n----------quittter-----------8");

                                } catch (\Throwable $th) {
                                    $res['nbre_lignes_read'] = $th->getMessage();

                                    // var_dump($res); print("<br/>\n");
                                    // print("<br/>\n etape debug 9  "); 
                                    // exit("<br/>\n----------quittter-----------9");


                                } //try  



                                // print("<br/>\n nbre_lignes_read: ");
                                // var_dump($res['nbre_lignes_read']) ;
                               
                                // exit("<br/>\n----------quittter-----------");


                                # supprimer la table temporaire # 
                                $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$temp_table` ; SET FOREIGN_KEY_CHECKS = 1; ";
                                $temp = $conn->exec($sql);
                                $res['drop_temp_table'] = $sql;


                                # supprimer la table principale et la recharger
                                $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$mysql_table` ; SET FOREIGN_KEY_CHECKS = 1; ";
                                $temp = $conn->exec($sql);
                                $res['drop_table'] = $sql;


                                $sql = "CREATE TABLE IF NOT EXISTS `$mysql_table` (
                                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                                `agence` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `agencelib` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `rm` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `typ` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `typlib` varchar(9) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `client` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
                                                `phone` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
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
                                $res['lock_table'] = "$sql";



                                $res['copy_fichier2'] = copy($ftp_outgoing_rep . $fichier, $log_rep . $fichier);

                                $res['nbre_lignes2'] = chargerDonneesTable($conn, $mysql_table,  $log_rep . $fichier);

                                # supprimer la copie du fichier temporaire
                                $res['unlink_fichier2'] = unlink($log_rep . $fichier);

                                fclose($handle);
                            } else {
                            }


                            $sql = "UNLOCK TABLES";
                            $conn->exec($sql);
                            // echo "<br/>\n $sql";
                            $res['unlock_table'] = "$sql";

                            $conn = null; //mysql



                            #deplacer le fichier
                            rename($push_rep . '/' . $fichier, $arch_outgoing . '/arch_' . $filename . '.' . $ext);

                        } // if($fct->chercherErreurFichier("$push_rep/$fichier",'ORA-')==true){

                            // exit("----------quittter-----------"); 

                    } //  if($taille_fic =='0'){


                } else { 
                }
            } //if

        } //WHILE
        closedir($dossier);
    } else {
        echo 'Le dossier n\' a pas pu être ouvert';
    }


} catch (\Throwable $e) {
    $res['conn'] = $e->getMessage();
}

# supprimer le fichier
unlink($file_lock_push_sms);



function chargerDonneesTable($dbh, $table, $fichier)
{
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
    $res = $dbh->exec("$sql");

    return $res;
}


$out1 = ob_get_contents();
$myfile = file_put_contents('log/loadAbonnement4_' . $now->format('Y_m_d') . '.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
