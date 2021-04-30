<?php
//error_reporting(E_ALL);
//ini_set('display_errors','On');

// $chemin=getcwd().'/params.xml';
$chemin = getcwd() . '/api/params.xml';

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
$oci_vue = $param->serveur->vue->Maofil;

// var_dump($adr);
// var_dump($username);
// var_dump($password);
// var_dump($schema);
// var_dump($tnsname);

$mysql_adr = $param->serveur_mysql->adr;
$mysql_username = $param->serveur_mysql->uti;
$mysql_password = $param->serveur_mysql->mdp;
$mysql_bd = $param->serveur_mysql->bd;
$mysql_table = $param->serveur_mysql->table->Maofil;

// var_dump($mysql_adr);
// var_dump($mysql_username);
// var_dump($mysql_password);
// var_dump($mysql_bd);


$res = array();


try {
    $conn = new PDO("mysql:host=$mysql_adr;dbname=$mysql_bd", $mysql_username, $mysql_password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "<br/>\n Connected successfully"; 
    // $res['conn'] = "Connexion [$mysql_adr, $mysql_bd,  $mysql_username] ";
    $res['conn'] = "OK ";
    
    // sql to create table
    $sql = "DROP TABLE IF EXISTS `$mysql_table` ";
    // $sql = "DROP TABLE IF EXISTS `V_VIREPREL_CPT` ";
    // use exec() because no results are returned
    $conn->exec($sql);
    // echo "<br/>\n DROP TABLE successfully";
    $res['drop_table'] = $sql;


    
    // $sql = "CREATE TABLE `V_VIREPREL_CPT` (
    // sql to create table
    $sql = "CREATE TABLE IF NOT EXISTS `$mysql_table` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `BANQUE` varchar(40)  NULL,
    `ABRBNQ` varchar(10)  NULL,
    `CODBNQ` varchar(5) NOT NULL,
    `CODGCH` varchar(5)  NULL,
    `GUICHET` varchar(20)  NULL,
    PRIMARY KEY (`id`)
    )";
    
    // use exec() because no results are returned
    $conn->exec($sql);
    // echo "<br/>\n Table $mysql_table created successfully";
    // $res['create_table'] = "$sql";
    $res['create_table'] = "CREATE TABLE IF NOT EXISTS [$mysql_table] ";


    $sql = "LOCK TABLES `$mysql_table` WRITE";
    $conn->exec($sql);
    //echo "<br/>\n  $sql";
    $res['lock_table'] = "$sql";


    $sql = "INSERT INTO `$mysql_table` VALUES 
    (:NUMERO, :BANQUE, :ABRBNQ, :CODBNQ, :CODGCH, :GUICHET) ";

    $stmt = $conn->prepare($sql);
    $res['insert_table'] = "$sql";



    $oci_conn = oci_connect($username, $password, $tnsname);

    if (!$oci_conn) {
        $m = oci_error();
        trigger_error(htmlentities($m['message']), E_USER_ERROR);
    }

    // $oci_sql = "SELECT * from BBG_V_VIREPREL_CPT WHERE rownum<=10  ";
    $oci_sql = 'SELECT * from ' . $oci_vue . '';
    // $oci_sql = 'SELECT * from BBG_V_VIREPREL_CPT'  ;
    // var_dump($oci_sql);
    // $oci_sql = "SELECT * from  BBG_V_VIREPREL_CPT  " ;

    $stid = oci_parse($oci_conn, $oci_sql);

    oci_execute($stid);

    $i = 0;
    while ($row = oci_fetch_assoc($stid)) {
        $i++;
        // /$agence_rm = $row['agence_rm'];
        // var_dump($row);
        // exit("<br/>\n------stop---------------");

        $BANQUE = $row['BANQUE'];
        $ABRBNQ = $row['ABRBNQ'];
        $CODBNQ = $row['CODBNQ'];
        $CODGCH = $row['CODGCH'];
        $GUICHET = $row['GUICHET'];

        //----------------------------------
        // echo"<pre>";
        // var_dump($row);

        $stmt->bindParam(':NUMERO', $i);
        $stmt->bindParam(':BANQUE', $BANQUE);
        $stmt->bindParam(':ABRBNQ', $ABRBNQ);
        $stmt->bindParam(':CODBNQ', $CODBNQ);
        $stmt->bindParam(':CODGCH', $CODGCH);
        $stmt->bindParam(':GUICHET', $GUICHET);

        $stmt->execute();

         // echo "<br/>\n INSERT CLIENT  $INDICE_CLIENT --- NUMERO : $i";


    }//while
    oci_free_statement($stid);
    oci_close($oci_conn);



    $sql = "UNLOCK TABLES";
    $conn->exec($sql);
    // echo "<br/>\n $sql";
    $res['unlock_table'] = "$sql";


    // echo "<br/>\n LIGNES CHARGEES : $i";
    $res['nbre_lignes'] = "$i";



} catch (PDOException $e) {
        // echo "<br/>\n Connection failed: " . $e->getMessage();
    $res['conn'] = "'.$e->getMessage().'";
}


$conn = null;
    
    /* Output header */
header('Content-type: application/json');
echo json_encode($res);