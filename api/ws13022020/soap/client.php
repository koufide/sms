<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <title>Client KIMIA</title>
    <meta http-equiv="content-type" content="text/html;charset=utf-8" />
    <meta name="generator" content="Geany 0.21" />

</head>

<body>

    <div class="main">
        <form method="post" action="#">
            <fieldset>
                <label>
                    Nom :
                </label>
                <input type="text" value="" name="nom" required />
            </fieldset>

            <fieldset>
                <label>
                    Prenom :
                </label>
                <input type="text" value="" name="prenom" required />
            </fieldset>
            <input type="submit" name="valider" value="Valider" />

        </form>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');

        if (isset($_POST['valider']) || isset($_POST['afficher'])) {

            // exit("<br/>\n--------------------quitter--------------");


            // première étape : désactiver le cache lors de la phase de test

            ini_set("soap.wsdl_cache_enabled", "0");

            // lier le client au fichier WSDL



            $clientSOAP = new SoapClient('http://192.168.56.220/test/webservice/soap/kimia.wsdl');
            // var_dump($clientSOAP->__getFunctions());

            //exit("<br/>\n--------------------quitter--------------");
        }

        if (isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['valider'])) {

            // exit("<br/>\n--------------------quitter--------------");

            echo '<fieldset>';
            $nom = $_POST['nom'];

            $prenom = $_POST['prenom'];


            // executer la methode getSave
            echo $clientSOAP->addNew($prenom, $nom);
            echo '</fieldset>';
        }


        ?>
        <form method="post" action="#">
            <input type="submit" name="afficher" value="Voir tous les enregistrements" />
        </form>



        <?php


        if (isset($_POST['afficher'])) {
            // exit("<br/>\n--------------------quitter--------------");

            echo '<fieldset>';

            // executer la methode displayDataFromDataBase

            foreach ($clientSOAP->displayDataFromDataBase("Bonjour ba yaya") as $row) {

                echo '<br/>';
                echo $row['code'];
                echo ' ';
                echo $row['libelle'];
                echo '<br/>';
            }
            echo '</fieldset>';
        }


        ?>

    </div>

</body>

</html>