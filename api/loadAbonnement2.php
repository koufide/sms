<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

// $chemin = getcwd() . '/api/params.xml';
$chemin = getcwd() . '/params.xml';
// var_dump($chemin);
// exit("<br/>\n-------quitter");

if (file_exists($chemin)) {
    $param = simplexml_load_file($chemin);
    // echo"<pre>";
    // print_r($param);
} else {
    exit('Echec lors de l\'ouverture du fichier ' . $chemin);
}
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

$api_rep = $param->reps->api_rep;
$file_abonnesms = $param->files->file_abonnesms;

$file_abonnesms = $api_rep . $file_abonnesms;

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
    // $oci_conn = oci_connect($username, $password, $tnsname);

    // if (!$oci_conn) {
    //     $m = oci_error();
    //     // trigger_error(htmlentities($m['message']), E_ERROR(1));
    //     $res['conn'] = "Erreur Connexion: " . $m['message'];
    //     $retour = $oci_conn;
    // } else {
    //     $oci_sql = 'SELECT * from ' . $oci_vue . '';
    //     $stid = oci_parse($oci_conn, $oci_sql);
    //     $retour = oci_execute($stid);
    //     // exit("<br/>\n retour: $retour");


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
        // echo "<br/>\n  $sql";
        $res['lock_table'] = "$sql";


        // $sql = "SET sql_mode = 'NO_ZERO_IN_DATE' ";
        // $conn->exec($sql);
        // // echo "<br/>\n  $sql";
        // $res['lock_table'] = "$sql";




        $res['nbre_lignes'] = chargerDonneesTable($conn, $mysql_table, $file_abonnesms);

        // if($res != 0){
        //     selectTable($conn, $mysql_table, $mtnci_fct);
        // }



        $sql = "UNLOCK TABLES";
        $conn->exec($sql);
        // echo "<br/>\n $sql";
        $res['unlock_table'] = "$sql";

        $conn = null; //mysql

    } //retour

    //     oci_free_statement($stid);
    //     oci_close($oci_conn);

    // }//if (!$oci_conn) {


} catch (Exception $e) {
    //echo "<pre>";
    //print_r($e);
    //echo "<br/>\n Connection failed: " . $e->getMessage();
    $res['conn'] = $e->getMessage();
}


// $res['nbre_lignes'] = "$i";

/* Output header */
header('Content-type: application/json');
echo json_encode($res);





function chargerDonneesTable($dbh, $table, $fichier)
{

    //$path = '/var/www/html/sms/api/'.$fichier;

    // $sql="
    // 	LOAD DATA INFILE '$fichier'
    // 	IGNORE
    // 	INTO TABLE $table
    // 	FIELDS TERMINATED BY ';'
    // 	ENCLOSED BY '\"' 
    //     LINES TERMINATED BY '\\r\\n'
    //     (agence,
    //     agencelib,
    //     rm,
    //     typ,
    //     typlib,
    //     client,
    //     phone,
    //     compte,
    //     datouv,
    //     datfrm,
    //     typcptlib,
    //     ncg,
    //     libelle,
    //     formule,
    //     databon,
    //     userabon,
    //     nom_userabon,
    //     valide,
    //     datvalidation,
    //     uservalide,
    //     nom_uservalide,
    //     datactif,
    //     useractif,
    //     nom_useractif,
    //     actif,
    //     datedesactif,
    //     userresili,
    //     nom_userresili,
    //     exonere_facture_pull,
    //     exonere_facture_push ) 
    // ";

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
                datouv = STR_TO_DATE(@datouv,'%d/%m/%Y') , 
                datfrm = if(@datfrm='',NULL,STR_TO_DATE(@datfrm,'%d/%m/%Y')),
                databon = STR_TO_DATE(@databon,'%d/%m/%Y'),
                datvalidation = STR_TO_DATE(@datvalidation,'%d/%m/%Y'),
                datactif = STR_TO_DATE(@datactif,'%d/%m/%Y'),
                datedesactif =  if(@datedesactif='',NULL,STR_TO_DATE(@datedesactif,'%d/%m/%Y')), 
                datefinabon =  if(@datefinabon='',NULL,STR_TO_DATE(@datefinabon,'%d/%m/%Y')), 
                actif =  if(@actif='A',1,0), 
                valide =  if(@valide='V',1,0) 
        ";
    //datfrm = if(@datfrm='',NULL,STR_TO_DATE(@datfrm,'%d/%m/%Y')),

    // SET datouv = STR_TO_DATE(@datouv,'%d/%m/%Y') , 
    // datfrm =   STR_TO_DATE(@datfrm,'%d/%m/%Y'),
    // databon = STR_TO_DATE(@databon,'%d/%m/%Y'),
    // datvalidation = STR_TO_DATE(@datvalidation,'%d/%m/%Y'),
    // datactif = STR_TO_DATE(@datactif,'%d/%m/%Y'),
    // datedesactif = STR_TO_DATE(@datedesactif,'%d/%m/%Y'),
    // databon = STR_TO_DATE(@databon,'%d/%m/%Y')

    // print($sql);
    //exit("----");  
    $res = $dbh->exec("
			$sql
		")
        or die(print_r($dbh->errorInfo(), true));

    //var_dump("chargerDonneesTable $table from $fichier : ".$res);
    return $res;
}


// function selectTable($dbh, $table){
// 	$select = $dbh->query("SELECT *  FROM `$table`");
	
// 	foreach  ($select as $row) {
// 		$tel = $row['phone'] ; 
// 		$nom = $row['rm'] ; 
// 		$service = $row['libelle'] ; 
// 		$repabonne = $row['client'] ;
		
		
// 		$service = str_replace(' ','',$service);
// 		$service = str_replace('\t','',$service);
// 		$service = str_replace('\n','',$service);
// 		$service = str_replace('\r','',$service);
// 		$service = strtoupper($service);
		
// 		$tel = str_replace(' ','',$tel);
// 		$tel = str_replace('\t','',$tel);
// 		$tel = str_replace('\n','',$tel);
// 		$tel = str_replace('\r','',$tel);
// 		$tel=str_pad($tel,8,'0',STR_PAD_LEFT);
		
//         //print $tel. "\n";
// 		//print $from. "\n";
// 		//print $smsc. "\n";
		
		
//         print $row['nom'] . "\t";
//         print  $row['tel'] . "\t";
//         print $row['service'] . "\n";
//         print $row['repabonne'] . "\n";
// 	}//for
  
	//var_dump("Nbre de ligne dans $table : ".$select->fetchColumn() );
	//return $select->fetchColumn();
// }
