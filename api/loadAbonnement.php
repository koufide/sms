<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// $chemin = getcwd() . '/api/params.xml';
$chemin = getcwd() . '/params.xml';

if (file_exists($chemin)) {
    $param = simplexml_load_file($chemin);

    //print_r($xml);
} else {
    exit('Echec lors de l\'ouverture du fichier ' . $chemin);
}  

 
//$param=simplexml_load_file($chemin) or die("Error simplexml_load_file $chemin");
//echo"<pre>";
//var_dump($param);

$adr = $param->serveur->adr;
$username = $param->serveur->uti;
$password = $param->serveur->mdp;
$schema = $param->bd->cgbtst;
$tnsname = $param->tnsname->cgbtst;
$oci_vue = $param->serveur->vue->Abonnement;

// var_dump($adr);
// var_dump($username);
// var_dump($password);
// var_dump($schema);
// var_dump($tnsname);

$mysql_adr = $param->serveur_mysql->adr;
$mysql_username = $param->serveur_mysql->uti;
$mysql_password = $param->serveur_mysql->mdp;
$mysql_bd = $param->serveur_mysql->bd;
$mysql_table = $param->serveur_mysql->table->Abonnement;
// $mysql_table = $param->serveur_mysql->table;

// var_dump($mysql_adr);
// var_dump($mysql_username);
// var_dump($mysql_password);
// var_dump($mysql_bd);


$res = array();
$i = 0;
$retour = true;

// exit("----------quittter-----------");

try {

    $oci_conn = oci_connect($username, $password, $tnsname);

    if (!$oci_conn) {
        $m = oci_error();
        // trigger_error(htmlentities($m['message']), E_ERROR(1));
        $res['conn'] = "Erreur Connexion: " . $m['message'];
        $retour = $oci_conn;
    } else {
        $oci_sql = 'SELECT * from ' . $oci_vue . '';
        $stid = oci_parse($oci_conn, $oci_sql);
        $retour = oci_execute($stid);
        // exit("<br/>\n retour: $retour");


        if ($retour == true) {

            $conn = new PDO("mysql:host=$mysql_adr;dbname=$mysql_bd", $mysql_username, $mysql_password);
                // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // echo "<br/>\n Connected successfully"; 
                // $res['conn'] = "Connexion [$mysql_adr, $mysql_bd,  $mysql_username] ";
            $res['conn'] = " OK ";

            // var_dump($res);
            // exit("<br/>\n retour: $res ");

            $sql = "SET FOREIGN_KEY_CHECKS = 0; DROP TABLE IF EXISTS `$mysql_table` ; SET FOREIGN_KEY_CHECKS = 1; ";


            // sql to create table
            //$sql = "DROP TABLE IF EXISTS `$mysql_table` ";
            //print("<br/>\n sql : $sql"); 

            // $sql = " DROP TABLE if EXISTS `V_VIREPREL_CPT` ";
            // use exec() because no results are returned
            $temp = $conn->exec($sql);


            // echo " < br / > \n DROP TABLE successfully ";
            $res['drop_table'] = $sql;


            //exit(" < br / > \n retour : $sql : $temp ");
            
                
                // $sql = " CREATE TABLE `V_VIREPREL_CPT` (
                // sql to create table
            // $sql = "CREATE TABLE IF NOT EXISTS `$mysql_table` (
            //     `id` int(11) NOT NULL AUTO_INCREMENT,
            //     `agence` varchar(6) NOT NULL,
            //     `compte` varchar(11) NOT NULL,
            //     `ncg` varchar(6) NOT NULL,
            //     `agencelib` varchar(50) NOT NULL,
            //     `nomges` varchar(25) NOT NULL,
            //     `client` varchar(6) NOT NULL,
            //     `nomclient` varchar(50) DEFAULT NULL,
            //     `typecli` varchar(32) NOT NULL,
            //     `datouvcli` varchar(10) NOT NULL,
            //     `datfrmcli` varchar(10) DEFAULT NULL,
            //     `typ` varchar(1) NOT NULL,
            //     `tel` varchar(66) DEFAULT NULL,
            //     `categorie` varchar(20) NOT NULL,
            //     PRIMARY KEY (`id`)
            //     )";

                $sql="CREATE TABLE IF NOT EXISTS `$mysql_table` (
                    `id` int(11) NOT NULL,
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
                    `userresili` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `nom_userresili` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                    `exonere_facture_pull` tinyint(1) NOT NULL,
                    `exonere_facture_push` tinyint(1) NOT NULL
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

            $sql = "INSERT INTO `$mysql_table` VALUES 
                (:numero, :agence, :agencelib, :rm, :typ, :typlib, :client, :phone, :compte, :datouv, :datfrm, :typcptlib, :ncg, :libelle, :formule, :databon, :userabon, :nom_userabon, :valide, :datvalidation, :uservalide, :nom_uservalide, :datactif, :useractif, :nom_useractif, :actif, :datedesactif, :userresili, :nom_userresili, :exonere_facture_pull, :exonere_facture_push) ";

            $stmt = $conn->prepare($sql);
                // $stmt->bindParam(':name', $name);
                // $stmt->bindParam(':value', $value);
            $res['insert_table'] = "$sql";

            while ($row = oci_fetch_assoc($stid)) {
                $i++;
                    // /$agence_rm = $row['agence_rm'];
                //var_dump($row);
                     //exit("<br/>\n------stop---------------");

                     //$agence = $row['agence'];
                $agence = $row['AGENCE'];
                $compte = $row['COMPTE'];
                $ncg = $row['NCG'];
                $agencelib = $row['AGENCELIB'];
                $nomges = $row['NOMGES'];
                $client = $row['CLIENT'];
                $nomclient = $row['NOMCLIENT'];
                $typecli = $row['TYPECLI'];
                $datouvcli = $row['DATOUVCLI'];
                $datfrmcli = $row['DATFRMCLI'];
                $typ = $row['TYP'];
                $tel = $row['TEL'];
                $categorie = $row['CATEGORIE'];
                // $LIBE_CPT = $row['LIBE_CPT'];
                // $DATOUV = $row['DATOUV'];
                // $DATFRM = $row['DATFRM'];
                // $POSDEV = $row['POSDEV'];
                // $POSDISP = $row['POSDISP'];
                // $NOM_FIC = $row['NOM_FIC'];
                
                    
                    //----------------------------------
                $DATOUV = trim($datouvcli);
                    // var_dump($DATOUV);
                $DATOUV = str_replace(' ', '', $DATOUV);
                    // var_dump($DATOUV);
                $DATOUV_DT = DateTime::createFromFormat('d/m/Y', $DATOUV);
                if ($DATOUV_DT == null) {
                    $DATOUV_DT = DateTime::createFromFormat('d-M-y', $DATOUV);
                }
                $DATOUV_STR = $DATOUV_DT->format('Y-m-d');
                    // var_dump($DATOUV_STR);
                    
                    // try{
                        
                        // }catch(Exception $ex){
                            //     try{
                                //         $DATOUV_DT = DateTime::createFromFormat('d-M-Y',$DATOUV);
                                //         $DATOUV_STR = $DATOUV_DT->format('Y-m-d');
                                //     }catch(Exception $ex){
                                    //         var_dump($ex->getMessage());
                                    //     }
                                    // }
                                    
                                    // $DATOUV_DT = new DateTime($DATOUV,new DateTimeZone('UTC') );
                                    // var_dump($DATOUV_DT);
                                    
                                    //$DATOUV_STR = $DATOUV_DT->format('Y-m-d');
                                    
                                    //var_dump($DATOUV_STR);


                $DATFRM = trim($datfrmcli);
                                    // var_dump($DATOUV);
                $DATFRM = str_replace(' ', '', $DATFRM);
                if (strlen($DATFRM) != 0) {
                    $DATFRM_DT = DateTime::createFromFormat('d/m/Y', $DATFRM);
                    if ($DATFRM_DT == null) {
                        $DATFRM_DT = DateTime::createFromFormat('d-M-y', $DATFRM);
                    }
                    $DATFRM_STR = $DATFRM_DT->format('Y-m-d');
                } else {
                    $DATFRM_DT = null;
                }
                                    
                                    // $DATOUV_DT = new DateTime($DATOUV,new DateTimeZone('UTC') );
                                    // var_dump($DATOUV_DT);
                                    
                                    // echo"<pre>";
                                    // var_dump($NOM_FIC);
                                    // var_dump($row);


                $stmt->bindParam(':numero', $i);
                $stmt->bindParam(':agence', $agence);
                $stmt->bindParam(':compte', $compte);
                $stmt->bindParam(':ncg', $ncg);
                $stmt->bindParam(':agencelib', $agencelib);
                $stmt->bindParam(':nomges', $nomges);
                $stmt->bindParam(':client', $client);
                $stmt->bindParam(':nomclient', $nomclient);
                $stmt->bindParam(':typecli', $typecli);
                $stmt->bindParam(':typ', $typ);
                $stmt->bindParam(':tel', $tel);
                $stmt->bindParam(':categorie', $categorie);
                $stmt->bindParam(':datouvcli', $DATOUV_STR);
                $stmt->bindParam(':datfrmcli', $DATFRM_STR); 

                
                // $stmt->bindParam(':NCG', $NCG);
                // $stmt->bindParam(':AGENCE_COMPTE', $AGENCE_COMPTE);
                // $stmt->bindParam(':RIB', $RIB);
                // $stmt->bindParam(':LIBE_CPT', $LIBE_CPT);


                                    // $stmt->bindParam(':DATFRM', $DATFRM);
                // $stmt->bindParam(':POSDEV', $POSDEV);
                // $stmt->bindParam(':POSDISP', $POSDISP);
                // $stmt->bindParam(':NOM_FIC', $NOM_FIC);

                $stmt->execute();
                                    
                                    
                                    // echo "<br/>\n INSERT CLIENT  $INDICE_CLIENT --- NUMERO : $i";


            }//while

            $sql = "UNLOCK TABLES";
            $conn->exec($sql);
                                // echo "<br/>\n $sql";
            $res['unlock_table'] = "$sql";

            $conn = null;//mysql

        }//retour

        oci_free_statement($stid);
        oci_close($oci_conn);

    }//if (!$oci_conn) {


} catch (Exception $e) {
    //echo "<pre>";
    //print_r($e);
    //echo "<br/>\n Connection failed: " . $e->getMessage();
    $res['conn'] = $e->getMessage();
}


$res['nbre_lignes'] = "$i";
                        
                    /* Output header */
header('Content-type: application/json');
echo json_encode($res);



    
        
                        