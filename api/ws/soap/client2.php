        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');

        // première étape : désactiver le cache lors de la phase de test
        ini_set("soap.wsdl_cache_enabled", "0");

        // lier le client au fichier WSDL
        $clientSOAP = new SoapClient('http://192.168.56.220/test/webservice/soap/kimia.wsdl');
        // var_dump($clientSOAP->__getFunctions());

        //exit("<br/>\n--------------------quitter--------------");

        $to = '22503612783';
        $message = 'text';

        // executer la methode getSave
        echo $clientSOAP->sendSMS($to, $message);

        echo $clientSOAP->__getLastRequest();
