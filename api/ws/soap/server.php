<?php

//Connexion a la bdd


// première étape : désactiver le cache lors de la phase de test
ini_set("soap.wsdl_cache_enabled", "0");

// on indique au serveur à quel fichier de description il est lié
$serveurSOAP = new SoapServer('kimia.wsdl');

// ajouter la fonction addNew au serveur
$serveurSOAP->addFunction('addNew');

// ajouter la fonction displayDataFromDataBase au serveur
$serveurSOAP->addFunction('displayDataFromDataBase');

// ajouter la fonction sendSMS au serveur
$serveurSOAP->addFunction('sendSMS');


// lancer le serveur
// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$serveurSOAP->handle();
// } else {
//     echo 'Utiulisation de la methode POST est recommandee, veuillez seulement utiliser POST';
// }

function addNew($prenom, $nom)
{

    $user = 'root';
    $pass = 'iexploit';
    $dsn = 'mysql:host=localhost;dbname=sf42wDemo';
    $etat = null;
    // Connexion à la base de données
    try {
        $dbh = new PDO($dsn, $user, $pass);

        $etat = true; //echo 'Ok';

    } catch (PDOException $e) {

        //die( "Erreur ! : " . $e->getMessage() );
        $etat = false; //echo 'Ko';
    }

    if ($etat) {

        $sql = "INSERT INTO typefichier (code,libelle) VALUES ('$nom','$prenom')";
        $dbh->exec($sql);
        return 'L\'enregistrement de ' . $prenom . ' ' . $nom . ' est effectue avec succes';
    } else {
        return 'Veiller verifier vos parametres';
    }
}

function displayDataFromDataBase($critere)
{


    $user = 'root';
    $pass = 'iexploit';
    $dsn = 'mysql:host=localhost;dbname=sf42wDemo';
    $etat = null;
    $tab = array();
    // Connexion à la base de données
    try {
        $dbh = new PDO($dsn, $user, $pass);

        $etat = true; //echo 'Ok';

    } catch (PDOException $e) {

        //die( "Erreur ! : " . $e->getMessage() );
        $etat = false; //echo 'Ko';
    }

    if ($etat) {

        $i = 0;
        $sql = "SELECT * FROM typefichier";
        $resultat = $dbh->query($sql);
        while ($row = $resultat->fetch()) {

            $tab[$i] = $row;
            $i++;
        }
        return $tab;
    } else {
        return 'Veiller verifier vos parametres encore une fois';
    }
}

function sendSMS($to, $message)
{
    return "$message envoyé à $to avec succès";
    // return 'test';
} //sendSMS
