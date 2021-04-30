<?php 
error_reporting(E_ALL);
ini_set('display_errors', 'On'); 

class MyPDO extends PDO
{

    private $conn;

    public function __construct()
    {

        // $chemin = getcwd() . '/api/params.xml';
        // $chemin = getcwd() . '/params.xml';
        $chemin = '/var/www/html/sms/api' . '/params.xml';

        if (file_exists($chemin)) {
            $param = simplexml_load_file($chemin);
        } else {
            exit('Echec lors de l\'ouverture du fichier ' . $chemin);
        }

        $array = json_decode(json_encode((array)$param), true);
        // var_dump($array);

        $mysql_adr = $param->serveur_mysql->adr;
        $mysql_username = $param->serveur_mysql->uti;
        $mysql_password = $param->serveur_mysql->mdp;
        $mysql_bd = $param->serveur_mysql->bd;
        $mysql_table = $param->serveur_mysql->table->Compte;



        try {
            //print "<br/>\n--------------stop-----------";
            $conn = new PDO("mysql:host=$mysql_adr;dbname=$mysql_bd", $mysql_username, $mysql_password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");

            // $sql = "USE $mysql_bd;";
            // $stmt = $conn->prepare($sql);
            // $stmt->execute();

            $this->conn = $conn;
        } catch (\Throwable $th) {
            print("<pre>");
            print_r($th);
        }
    } //construct


    public function getConn()
    {
        return $this->conn;
    }



    /**
     * Insert into database with using transaction (if operation failed the changes go before)
     */
    public function __insert($statement)
    {
        $this->beginTransaction();
        $status = $this->exec($statement);
        if ($status) {
            $this->commit();
        } else {
            $this->rollback();
        }
    }


    function __update($entity, array $tab_attr, $where)
    {
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);


        $nb = count($tab_attr);
        $i = 1;

        $query = "Update " . $entity . " SET ";


        $keys = "";

        foreach ($tab_attr as $key => $valeur) {

            if ($nb == $i) {
                $keys .= "`" . $key . "` = '" . $valeur . "' WHERE " . $where;
            } else {
                $keys .= "`" . $key . "` = '" . $valeur . "',";
            }

            $i++;
        } //for



        $query .= $keys;

        $action = $this->conn->prepare($query);
        
        // var_dump($query);
        echo($query);


        //try{
        $res = $action->execute();
        //  }catch(Exception $e){
        //        print "<br/>\n Erreur execute =>: " . $e->getMessage();
        //         $this->__beginTransaction();
        //   }
        //$res = $action->rowCount();
        //print("----Modification de $res lignes.\n");
        //print_r("\n".$this->connection->errorInfo());
        //print_r("\n".$action->errorInfo());

        //print '<br>'.$query;

        //$res=$this->connection->exec($query);
        //print_r("\n".$this->connection->errorInfo());
        //exit('<br>qtz');

        return $res;
    } //__update



    function __select2($entity, $colums = null, $where = null)
    {
        //verification du contenu de la colums
        if (empty($colums)) { //si vide 
            $colums = "*";
        }

        //verification du contenu de la clause where
        if (empty($where)) { //si vide
            $query = "SELECT $colums  FROM " . $entity;
        } else {
            $query = "SELECT $colums  FROM " . $entity . " " . $where;
        }

        var_dump($query);

        //preparation de l'exution de la requette.
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            //execution de la requette sql
            $stmt->execute();

            return $stmt;
        } else {
            //cas d'erreur d'execution de la requette sql
            return self::get_error();
        }
    } //select2


    function __select1($entity, $colums = null, $where = null, $fetch = null, $mode = null)
    {
        //verification du contenu de la colums
        if (empty($colums)) { //si vide 
            $colums = "*";
        }

        //verification du contenu de la clause where
        if (empty($where)) { //si vide
            $query = "SELECT $colums  FROM " . $entity;
        } else {
            $query = "SELECT $colums  FROM " . $entity . " " . $where;
        }

        var_dump($query);

        //preparation de l'exution de la requette.
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            //execution de la requette sql
            $stmt->execute();

            if ($fetch == null) {
                if ($mode == null) {
                    return $stmt->fetchAll();
                } else {
                    return $stmt->fetchAll($mode);
                }
            } else {
                if ($mode == null) {
                    return $stmt->$fetch();
                } else {
                    return $stmt->$fetch($mode);
                }
            }
        } else {
            //cas d'erreur d'execution de la requette sql
            return self::get_error();
        }
    } //select1



    function __select($entity, $colums = null, $where = null, $mode = null)
    {


        //verification du contenu de la colums
        if (empty($colums)) { //si vide 
            $colums = "*";
        }

        //verification du contenu de la clause where
        if (empty($where)) { //si vide
            $query = "SELECT $colums  FROM " . $entity;
        } else {
            $query = "SELECT $colums  FROM " . $entity . " " . $where;
        }

        var_dump($query);

        //preparation de l'exution de la requette.
        $stmt = $this->conn->prepare($query);

        if ($stmt) {
            //execution de la requette sql
            $stmt->execute();
            if (!empty($mode)) {
                return $stmt->fetch($mode);
            } else {
                return $stmt->fetchAll();
            }
        } else {
            //cas d'erreur d'execution de la requette sql
            return self::get_error();
        }
    } //select
} //MyPDO
