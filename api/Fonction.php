<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once('MyPDO.php'); 
// $pdo = new MyPDO();

require_once 'MessageDigestPasswordEncoder.php';


class Fonction
{
    private $user;
    private $pass;
    private $pass_encode;
    private $indice;
    private $from;
    private $my_pdo;
    private $my_tel; // numero utilise par default
    private $base_url;



    public function __construct()
    {
        $this->user = 'Bridge-bank';
        $this->pass = 'Bridge2018';
        $this->pass_encode = base64_encode("$this->user:$this->pass");
        //var_dump($this->pass_encode);
        $this->indice = '225';
        // $this->from = 'TEST SMS';
        $this->from = 'BRIDGE BANK';
        $this->base_url = 'https://193.105.74.159';
        //$this->base_url = 'https://g3lq8.api.infobip.com';
        // $this->base_url = 'http://g3lq8.api.infobip.com';


        $this->my_tel = '2250709327626'; // numero utilise par default


        $this->my_pdo = new MyPDO();

        ////print "<br/>\n--------------stop-----------";
    } //construct




    public function getApiManager_Url()
    {
        //$url = 'http://192.168.200.42:8280/pushsms/v1.0.0/sendSms_notification';
        //$url = 'http://192.168.150.22:8281/pushsms/v1.0.0/sendSms_notification';
        return 'http://192.168.150.22:8281';
    }

    public function getApiManager_Sendsms()
    {
        return '/pushsms/v1.0.0/sendSms_notification';
    }

    public function getApiManager_Bearer()
    {
        //$bearer = 'b45f3329-0d6c-3d17-903f-4049726fc077';
        // return '4f1abb37-b640-38f2-8b0d-3753ec0237cf';
        // return '91433076-c6bd-36f2-ac43-9f1f6be80c5e';
        // return '10717daa-c898-3959-a390-10fa45763ccc';
        return '8d84c6f6-8db1-39d2-b145-f32c6ae3a60c';
    }


    /**
     * cacher certains caractères du compte
     * @var $compte : le compte à crypter
     */
    public function getCompteHash($compte)
    {

        // print "<br/>\n compte: $compte";
        $tab_compte = str_split($compte);
        // var_dump($tab_compte);
        $compte_hash = $tab_compte[0] . $tab_compte[1] . $tab_compte[2] . $tab_compte[3] . '*****' . $tab_compte[9] . $tab_compte[10];
        // print "<br/>\n compte_hash: $compte_hash";
        return $compte_hash;
    } //    public function getCompteHash($compte){


    public function getScheduledSMS()
    {

        $baseUrl =  $this->base_url;
        //$url = $baseUrl . '/sms/1/bulks/status';
        $url = $baseUrl . '/sms/1/bulks/status';
        var_dump($url);
        $method = 'GET';


        $res = array(
            "bulkId" => "BBG-BULK-ID-5e789d7f0b5ef"
        );

        $json = json_encode($res);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "$method",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        print_r($response);
        print_r($err);
    } //getScheduledSMS


    /**
     * flooding: verifier si un message a deja ete envoyé dans la meme journée.
     * si le message a deja ete envoye, une erreur est envoyée
     * sinon le message est transmis normalement
     * @param $tel: numero de telephone au format indicatif+numero
     * @param $text: le message a envoyé
     * @param date_str : date au format format('d/m/Y')
     * @return return 0 si
     */
    public function flooding($tel, $text, $date_str)
    {

        $dbh = $this->my_pdo->getConn();
        $sql = "select * from outgoing where  a = :tel and text = :text and date_format(sendsms_at,'%d/%m/%Y') = :date_str ";
        $stmt = $dbh->prepare("$sql");
        $stmt->execute(
            [
                'tel' => $tel,
                'text' =>  trim($text),
                'date_str' => $date_str
            ]
        );

        $count = $stmt->rowCount();

        if ($count == '0') {
            //echo "Failed !";
        } else {
            //echo "Success !";
        }
        //var_dump($count);

        return $count;

        // exit("quiiter");
    } //flooding


    /**
     * retourner le numero de telephone par default
     */
    public function getMyTel()
    {
        return $this->my_tel;
    }

    /**
     * chercher une erreur $erreur dans le fichier $fichier
     */
    public function chercherErreurFichier($fichier, $erreur)
    {
        $trouve = false;

        if (strpos(file_get_contents("$fichier"), $erreur) !== false) {
            if (substr($fichier, 0, 4) === $erreur) { # ajoute le 24042021 0915 / koufide / correction erreur FLORA-COLOMBE
                $trouve = true;
            }
        }
        // print "<br/>\n chercherErreurFichier erreur : $erreur";
        // print "<br/>\n chercherErreurFichier fichier : $fichier";
        // print "<br/>\n chercherErreurFichier erreur : $erreur";
        // print "<br/>\n chercherErreurFichier fichier : $fichier";
        return $trouve;
    } //chercherErreurFichier



    public function getFlash()
    {
        return false;
    }

    //https://fr.wikipedia.org/wiki/Liste_des_codes_ISO_639-1

    public function getLanguageCode()
    {
        return "FR";
    }
    public function getTransliteration()
    {
        return "French";
    }

    public function getFrom()
    {
        return $this->from;
    } //getFrom


    public function getBulkId()
    {
        return uniqid('BBG-BULK-ID-', false);
    } //getBulkId

    public function getMessageId()
    {
        return uniqid('BBG-MSG-ID-', false);
    } //getMessageId



    public function getTel($tel)
    {
        // //print("<br/>\n------------getTel(tel)");
        // //print("<br/>\n tel: $tel");

        $tel = trim($tel);
        $tel = str_replace(' ', ' ', $tel);
        //$tel = substr($tel, -8); //les 8 derniers caractères
        $tel = substr($tel, -10); //les 8 derniers caractères

        // //print("<br/>\n tel: $tel");

        $n = strlen(($tel));
        //if ($n == 8) {
        $tel = $this->indice . $tel;
        //}
        // //print("<br/>\n getTel----tel: $tel");
        return $tel;
    } //getTel

    public function sendSMS22052019($to, $message)
    {
        //$my_pdo = $this->my_pdo->getConn();

        // $user = ' Bridge - bank ';
        // $pass = ' Bridge2018 ';
        // //var_dump(base64_encode("$user:$pass"));
        // $indice = ' 225 ';
        // $from = ' TEST ';

        $url =  $this->base_url . '/sms/2/text/single';
        //$url = 'http://193.105.74.159/sms/2/text/single';
        //$url = 'https://193.105.74.159/sms/2/text/single';
        ###$url = 'http://193.105.74.159/sms/2/text/single';
        //$url = 'https://193.105.74.159/sms/1/text/single';


        //$message = uniqid();

        // $to = $this->indice . $to;
        $to = $this->getTel($to);
        $text = $message;
        $from = $this->from;

        $res = array(
            "from" => $from,
            "to" => "$to",
            "text" => "$text"
        );

        // $json = '{  
        // "from":"InfoSMS",
        //     "to":"22503612783",
        //     "text":"Test SMS."
        //     }';

        // //var_dump($res);
        $json = json_encode($res);
        // //var_dump($json);



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $retour;

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            // //var_dump($response);
            // return $response;
            $retour = $response;
            $response = json_decode($response);




            if ($response->messages) {

                $now = new DateTime('NOW', new DateTimeZone(('UTC')));

                $dbh = $this->my_pdo->getConn();

                $data = [];

                //print("<br/>\n---------ok");
                $r_messages = $response->messages;
                // //var_dump($r_messages);


                $r_to = $r_messages[0]->to;
                // //var_dump($r_to);

                // foreach ($r_messages as $m) {
                //     //var_dump($m);
                // $r_to = $r_messages[0]->to;

                $r_status = $r_messages[0]->status;
                $groupId = $r_status->groupId;
                $groupName = $r_status->groupName;
                $id = $r_status->id;
                $name = $r_status->name;
                $description = $r_status->description;
                $messageId = $r_messages[0]->messageId;

                $data['de'] = $this->getFrom();
                $data['a'] = $to;
                $data['text'] = $message;
                $data['message_id'] = $messageId;
                // $data['status_sendsms'] = $response->messages;
                $data['status_sendsms'] = $response->messages[0]->messageId;
                $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                // $data['letest'] = $response->messages;
                $data['letest'] = $response->messages[0]->messageId;
                $data['send_groupid'] = $groupId;
                $data['send_groupname'] = $groupName;
                $data['send_id'] = $id;
                $data['send_name'] = $name;
                $data['send_description'] = $description;
                $data['tentative'] = 0;

                // }//for

                $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description, tentative)
    VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description, :tentative)
    ";
                $stmt = $dbh->prepare($sql);
                //print_r($sql);
                // //var_dump($data);
                try {
                    $stmt->execute($data);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
            } //if
        } //err





        // $data = [
        //     $this->getFrom(),
        //     $to,
        //     $message,
        //     $retour,
        //     (($err) ? null : $response->messages[0]->messageId),
        //     ($now->format('Y-m-d H:i:s'))
        // ];


        // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
        // $sth->execute($data);





        //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails

        return $retour;
    } //sendSMS22052019




    public function sendSMS($to, $message, $insert = true)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $dbh = $this->my_pdo->getConn();

        # CONTROLE FLOODING # 25072019 koufide
        # eviter d'envoyer  un meme message plusieurs fois 
        $sql = "select * from outgoing where  a = :tel and text = :text and date_format(sendsms_at,'%d/%m/%Y') = :date_str ";
        $stmt = $dbh->prepare("$sql");
        $stmt->execute(
            [
                'tel' => $to,
                // 'text' =>  trim($message),
                'text' =>  $message,
                'date_str' => $now->format('d/m/Y')
            ]
        );

        $outgoings = $stmt->fetChall();
        var_dump($message);
        var_dump($outgoings);

        if (empty($outgoings)) {


            //$my_pdo = $this->my_pdo->getConn();

            // $user = ' Bridge - bank ';
            // $pass = ' Bridge2018 ';
            // //var_dump(base64_encode("$user:$pass"));
            // $indice = ' 225 ';
            // $from = ' TEST ';

            // $url = 'https://193.105.74.159/sms/2/text/single';
            // $url = 'https://193.105.74.159/sms/2/text/single'; ### ok 22 11 2019
            // $url = 'https://193.105.74.159/sms/2/text/single'; ### ok 22 11 2019
            $url =  $this->base_url . '/sms/2/text/single'; ### ok 22 11 2019
            //$url = 'http://193.105.74.159/sms/2/text/single';
            //$url = 'https://193.105.74.159/sms/2/text/single';
            ###$url = 'http://193.105.74.159/sms/2/text/single';
            //$url = 'https://193.105.74.159/sms/1/text/single';


            //$message = uniqid();

            // $to = $this->indice . $to;
            $to = $this->getTel($to);
            $text = $message;
            $from = $this->from;

            $res = array(
                "from" => $from,
                "to" => "$to",
                "text" => "$text"
            );

            // $json = '{  
            // "from":"InfoSMS",
            //     "to":"22503612783",
            //     "text":"Test SMS."
            //     }';

            // //var_dump($res);
            $json = json_encode($res);
            // //var_dump($json);



            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "$url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
                CURLOPT_POSTFIELDS => "$json",
                CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    // "authorization: Basic " . base64_encode("$user:$pass"),
                    "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                    "content-type: application/json"
                ),
            ));

            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);


            $retour;

            if ($err) {
                // echo "cURL Error #:" . $err;
                // //var_dump($err);
                // return $err;
                $retour = $err;
            } else {
                // echo '<pre>';
                // //var_dump($response);
                // return $response;
                $retour = $response;
                $response = json_decode($response);



                if ($response->messages) {

                    $data = [];

                    //print("<br/>\n---------ok");
                    $r_messages = $response->messages;
                    // //var_dump($r_messages);


                    $r_to = $r_messages[0]->to;
                    // //var_dump($r_to);

                    // foreach ($r_messages as $m) {
                    //     //var_dump($m);
                    // $r_to = $r_messages[0]->to;

                    $r_status = $r_messages[0]->status;
                    $groupId = $r_status->groupId;
                    $groupName = $r_status->groupName;
                    $id = $r_status->id;
                    $name = $r_status->name;
                    $description = $r_status->description;
                    $messageId = $r_messages[0]->messageId;

                    $data['de'] = $this->getFrom();
                    $data['a'] = $to;
                    $data['text'] = $message;
                    $data['message_id'] = $messageId;
                    // $data['status_sendsms'] = $response->messages;
                    $data['status_sendsms'] = $response->messages[0]->messageId;
                    $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                    // $data['letest'] = $response->messages;
                    $data['letest'] = $response->messages[0]->messageId;
                    $data['send_groupid'] = $groupId;
                    $data['send_groupname'] = $groupName;
                    $data['send_id'] = $id;
                    $data['send_name'] = $name;
                    $data['send_description'] = $description;
                    $data['tentative'] = 0;

                    // }//for

                    if ($insert) {

                        $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description,tentative)
            VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description, :tentative)
            ";
                        $stmt = $dbh->prepare($sql);
                        //print_r($sql);
                        // //var_dump($data);
                        try {
                            $stmt->execute($data);
                        } catch (\Throwable $th) {
                            $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                        }
                    } else { //UPDATE
                        //$sql = "UPDATE outgoing SET ";
                    } //insert

                } //if
            } //err





            // $data = [
            //     $this->getFrom(),
            //     $to,
            //     $message,
            //     $retour,
            //     (($err) ? null : $response->messages[0]->messageId),
            //     ($now->format('Y-m-d H:i:s'))
            // ];


            // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
            // $sth->execute($data);





            //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails

            return $retour;
        } else {
            $retourErreur = '1';
            $retourMessage = "Le message [$message] a deja ete envoye au numero [$to]";
            $retourTab = [];
            #retour_json($retourErreur, $retourMessage, []);

            // echo $retour = json_encode([
            //     'erreur' => $retourErreur,
            //     'message' => $retourMessage,
            //     'retour' => $retour
            // ]);

            // return $retour;


            $this->retour_json($retourErreur, $retourMessage, $retourTab, $fichier = null, $content = null);
        } // if (empty($outgoings)) {



    } //sendSMS



    public function sendSMS_V2($to, $message, $insert = true, $retour_json = false)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $dbh = $this->my_pdo->getConn();

        # CONTROLE FLOODING # 25072019 koufide
        # eviter d'envoyer  un meme message plusieurs fois 
        $sql = "select * from outgoing where  a = :tel and text = :text and date_format(sendsms_at,'%d/%m/%Y') = :date_str ";
        $stmt = $dbh->prepare("$sql");
        $stmt->execute(
            [
                'tel' => $to,
                // 'text' =>  trim($message),
                'text' =>  $message,
                'date_str' => $now->format('d/m/Y')
            ]
        );

        $outgoings = $stmt->fetChall();
        var_dump($message);
        var_dump($outgoings);

        if (empty($outgoings)) {


            // $url = 'https://193.105.74.159/sms/2/text/single';
            // $url = 'https://193.105.74.159/sms/2/text/single'; ### ok 22 11 2019
            // $url = 'https://193.105.74.159/sms/2/text/single'; ### ok 22 11 2019
            $url =  $this->base_url . '/sms/2/text/single'; ### ok 22 11 2019
            //$url = 'http://193.105.74.159/sms/2/text/single';
            //$url = 'https://193.105.74.159/sms/2/text/single';
            ###$url = 'http://193.105.74.159/sms/2/text/single';
            //$url = 'https://193.105.74.159/sms/1/text/single';


            //$message = uniqid();

            // $to = $this->indice . $to;
            $to = $this->getTel($to);
            $text = $message;
            $from = $this->from;

            $res = array(
                "from" => $from,
                "to" => "$to",
                "text" => "$text"
            );

            // $json = '{  
            // "from":"InfoSMS",
            //     "to":"22503612783",
            //     "text":"Test SMS."
            //     }';

            // //var_dump($res);
            $json = json_encode($res);
            // //var_dump($json);



            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "$url",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
                CURLOPT_POSTFIELDS => "$json",
                CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    // "authorization: Basic " . base64_encode("$user:$pass"),
                    "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                    "content-type: application/json"
                ),
            ));
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);


            $retour;

            if ($err) {
                $retour = $err;
            } else {
                $retour = $response;
                $response = json_decode($response);



                if ($response->messages) {

                    $data = [];

                    $r_messages = $response->messages;

                    $r_to = $r_messages[0]->to;


                    $r_status = $r_messages[0]->status;
                    $groupId = $r_status->groupId;
                    $groupName = $r_status->groupName;
                    $id = $r_status->id;
                    $name = $r_status->name;
                    $description = $r_status->description;
                    $messageId = $r_messages[0]->messageId;

                    $data['de'] = $this->getFrom();
                    $data['a'] = $to;
                    $data['text'] = $message;
                    $data['message_id'] = $messageId;
                    // $data['status_sendsms'] = $response->messages;
                    $data['status_sendsms'] = $response->messages[0]->messageId;
                    $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                    // $data['letest'] = $response->messages;
                    $data['letest'] = $response->messages[0]->messageId;
                    $data['send_groupid'] = $groupId;
                    $data['send_groupname'] = $groupName;
                    $data['send_id'] = $id;
                    $data['send_name'] = $name;
                    $data['send_description'] = $description;
                    $data['tentative'] = 0;

                    // }//for

                    if ($insert) {

                        $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description,tentative)
                            VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description, :tentative)
                            ";
                        $stmt = $dbh->prepare($sql);
                        try {
                            $stmt->execute($data);
                        } catch (\Throwable $th) {
                            $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                        }
                    } else { //UPDATE
                    } //insert

                } //if
            } //err

            //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails

            return $retour;

        } else {

            if($retour_json){
                # koufide 05122020
                # NB : il y a un exit apres le retour. donc le programme s'arrete
                # mettre retour_json à false pour eviter cela
                $retourErreur = '1';
                $retourMessage = "Le message [$message] a deja ete envoye au numero [$to]";
                $retourTab = [];
                $this->retour_json($retourErreur, $retourMessage, $retourTab, $fichier = null, $content = null);
            }

        } // if (empty($outgoings)) {



    } //sendSMS_V2



    public function sendSmsWithNotifyUrl($to, $message)
    {

        $url =  $this->base_url . '/sms/2/text/advanced';

        $curl = curl_init();

        //         '{
        //         "bulkId":"BULK-ID-123-xyz",
        //         "messages":[
        //             {
        //                 "from":"InfoSMS",
        //                 "destinations":[
        //                     {
        //                     "to":"41793026727",
        //                     "messageId":"MESSAGE-ID-123-xyz"
        //                     },
        //                     {
        //                     "to":"41793026731"
        //                     }
        //                 ],
        //                 "text":"Mama always said life was like a box of chocolates. You never know what you're gonna get.",
        //                 "notifyUrl":"http://www.example.com/sms/advanced",
        //                 "notifyContentType":"application/json",
        //                 "callbackData":"There's no place like home."
        //             }
        //                 ]
        //         }
        // ';	

        $tab = [
            "bulkId" => $this->getBulkId(),
            "messages" => [
                "from" => "BRIDGE BANK",
                "destinations" => [
                    [
                        "to" => "$to",
                        "messageId" => $this->getMessageId()
                    ],
                    ["to" => '22503612783']
                ],
                "text" => "$message",
                "notifyUrl" => "http://192.168.3.100/sms/api/notifyurl.php",
                "notifyContentType" => "application/json",
                "callbackData" => "There's no place like home."
            ]
        ];


        $json = json_encode($tab);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
            print_r(json_decode($response, true));
        }
    } //sendSmsWithNotify


    public function testPhone($to)
    {

        $url =  $this->base_url;

        $curl = curl_init();

        $tab = [
            "to" => "$to"
        ];


        $json = json_encode($tab);

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
            print_r(json_decode($response, true));
        }
    } //testPhone


    public function resendSMS($to, $message, $o_messageId, $tentative)
    {

        $url = $this->base_url . '/sms/2/text/single';

        $to = $this->getTel($to);
        $text = $message;
        $from = $this->from;

        $res = array(
            "from" => $from,
            "to" => "$to",
            "text" => "$text"
        );

        $json = json_encode($res);



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        // $retour;

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            // $retour = $err;
        } else {

            // return $response;
            // $retour = $response;
            $response = json_decode($response);

            // echo '<pre>';
            // var_dump($response);
            // echo "<br/>\n";
            // print_r($response);


            if ($response->messages) {

                $now = new DateTime('NOW', new DateTimeZone(('UTC')));

                $dbh = $this->my_pdo->getConn();

                $data = [];

                //print("<br/>\n---------ok");
                $r_messages = $response->messages;
                // //var_dump($r_messages);

                foreach ($r_messages as $message) {
                    // echo '<pre>';
                    // var_dump($message);
                    // echo "<br/>\n";
                    // print_r($message);

                    $r_status = $message->status;
                    $messageId = $message->messageId;
                    $r_to = $message->to;

                    // var_dump($messageId);
                    // var_dump($r_to);
                    //var_dump($r_status);

                    // var_dump($r_status);
                    // foreach ($r_status as $status) {
                    // var_dump($status);
                    $groupId = $r_status->groupId;
                    $groupName = $r_status->groupName;
                    $id = $r_status->id;
                    $name = $r_status->name;
                    $description = $r_status->description;

                    // var_dump($groupId);
                    // var_dump($groupName);
                    // var_dump($id);
                    // var_dump($name);
                    // var_dump($description);
                    // } //for
                    // exit("<br/>\n---------------QUITTER---fct-------------");


                    //$data['de'] = $this->getFrom();
                    //$data['a'] = $to;
                    //$data['text'] = $message;
                    $data['message_id'] = $messageId;
                    $data['o_message_id'] = $o_messageId;
                    // $data['status_sendsms'] = $response->messages;
                    $data['status_sendsms'] = $messageId;
                    $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                    // $data['letest'] = $response->messages;
                    $data['tentative'] = $tentative;
                    $data['send_groupid'] = $groupId;
                    $data['send_groupname'] = $groupName;
                    $data['send_id'] = $id;
                    $data['send_name'] = $name;
                    $data['send_description'] = $description;

                    // }//for


                    //             $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description)
                    // VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description)
                    // ";
                    //             $stmt = $dbh->prepare($sql);
                    //             //print_r($sql);
                    //             // //var_dump($data);
                    //             $stmt->execute($data);

                    $sql = "UPDATE outgoing 
                    SET message_id=:message_id , status_sendsms=:status_sendsms, sendsms_at=:sendsms_at, send_groupid=:send_groupid, send_groupname=:send_groupname, send_id=:send_id, send_name=:send_name, send_description=:send_description, tentative=:tentative
                    WHERE message_id=:o_message_id";

                    $stmt = $dbh->prepare($sql);
                    // print_r($sql);
                    // var_dump($data);
                    try {
                        $res = $stmt->execute($data);
                    } catch (\Throwable $th) {
                        $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                    }
                    // var_dump($res);
                } //for/messages

                // exit("<br/>\n---------------QUITTER---fct-------------");


                // $r_to = $r_messages[0]->to;
                // //var_dump($r_to);

                // foreach ($r_messages as $m) {
                //     //var_dump($m);
                // $r_to = $r_messages[0]->to;

                // $r_status = $r_messages[0]->status;
                // $groupId = $r_status->groupId;
                // $groupName = $r_status->groupName;
                // $id = $r_status->id;
                // $name = $r_status->name;
                // $description = $r_status->description;
                // $messageId = $r_messages[0]->messageId;


            } //if
        } //err





        // $data = [
        //     $this->getFrom(),
        //     $to,
        //     $message,
        //     $retour,
        //     (($err) ? null : $response->messages[0]->messageId),
        //     ($now->format('Y-m-d H:i:s'))
        // ];


        // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
        // $sth->execute($data);





        //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails

        //return $retour;
    } //resendSMS




    public function sendFlashSMS($to, $message)
    {

        // $url = 'http://193.105.74.159/sms/1/text/advanced';
        $url =  $this->base_url . '/sms/1/text/advanced';

        //$message = uniqid();

        // $to = $this->indice . $to;
        $to = $this->getTel($to);
        $text = $message;
        $from = $this->from;

        // $res = array(
        //     "from" => $from,
        //     "to" => "$to",
        //     "text" => "$text"
        // );


        $json = '{
        "messages":[
            {
            "from":"' . $this->getFrom() . '",
                    "destinations" : [{
                    "to" : "' . $to . '"
                }
                ],
                    "text" : "' . $text . '",
                    "flash" : ' . $this->getFlash() . '
            }
            ]
        }
        ';

        // $json = '{
        // "messages":[
        //     {
        //     "from":"' . $this->getFrom() . '",
        //             "destinations" : [{
        //             "to" : "' . $to . '"
        //         }
        //         ],
        //             "text" : "' . $text . '",
        //             "flash" : true
        //     }
        //     ]
        // }
        // ';

        // echo "<pre>";
        //var_dump($json);

        $res = json_decode($json, true);
        //var_dump($res);
        //print_r($res);



        // //var_dump($res);
        // $json = json_encode($res);
        // //var_dump($json);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));



        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $retour;

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            //var_dump($response);
            // return $response;
            $retour = $response;
            $response = json_decode($response);




            if ($response->messages) {

                $now = new DateTime(' NOW ', new DateTimeZone((' UTC ')));

                $dbh = $this->my_pdo->getConn();

                $data = [];

                // //print("<br/>\n---------ok");
                $r_messages = $response->messages;
                //var_dump($r_messages);


                $r_to = $r_messages[0]->to;
                //var_dump($r_to);

                // foreach ($r_messages as $m) {
                //     //var_dump($m);
                // $r_to = $r_messages[0]->to;

                $r_status = $r_messages[0]->status;
                $groupId = $r_status->groupId;
                $groupName = $r_status->groupName;
                $id = $r_status->id;
                $name = $r_status->name;
                $description = $r_status->description;
                $messageId = $r_messages[0]->messageId;
                $smsCount = $r_messages[0]->smsCount;

                $data['de'] = $this->getFrom();
                $data['a'] = $to;
                $data['text'] = $message;
                $data['message_id'] = $messageId;
                // $data['status_sendsms'] = $response->messages;
                $data['status_sendsms'] = json_encode($response->messages);
                // $data['status_sendsms'] = $response->messages[0]->messageId;
                $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                // $data[' letest '] = $response->messages;
                // $data['letest'] = $response->messages[0]->messageId;
                $data['letest'] = json_encode($response->messages);
                $data['send_groupid'] = $groupId;
                $data['send_groupname'] = $groupName;
                $data['send_id'] = $id;
                $data['send_name'] = $name;
                $data['send_description'] = $description;
                $data['sms_count'] = $smsCount;
                $data['tentative'] = 0;

                // }//for

                $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description, sms_count, tentative)
VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description, :sms_count, :tentative)
 ";
                $stmt = $dbh->prepare($sql);
                //print_r($sql);
                //var_dump($data);

                try {
                    $stmt->execute($data);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
            }
        }



        return $retour;
    } //sendFlashSMS




    public function sendMultiSMStoMultiDest($tab_messages)
    {

        // $tab = ' {  
        //     "messages":[  
        //        {  
        //           "from":"WineShop",
        //           "to":"41793026727",
        //           "text":"Hey Mike, delicious Istrian Malvazija is finally here. Feel free to visit us and try it for free!"
        //        },
        //        {  
        //           "from":"WineShop",
        //           "to":"41793026834",
        //           "text":"Hi Jenny, we have new French Merlot on our shelves. Drop by our store for a free degustation!"
        //        }
        //     ]
        //  }';

        // //print "<pre>";
        // //print_r($tab);
        // echo "<br/>\n----------";

        // $json = json_encode($tab);
        // //print_r($json);
        // echo "<br/>\n----------";

        // $json = json_decode($tab);
        // //print_r($json);
        // echo "<br/>\n----------";


        // $json = json_decode($tab, true);
        // //print_r($json);
        // echo "<br/>\n----------";
        // echo "<br/>\n----------";
        // echo "<br/>\n----------";
        // echo "<br/>\n----------";
        // echo "<br/>\n----------";


        //===============================================================
        // $message = [];
        // $messages = [];
        // $tab_messages = [];

        // $message['from'] = 'TEST';
        // $message['to'] = '22506306746';
        // $message['text'] = 'TEST1';

        // // echo "<br/>\n";
        // // //print_r($message);
        // // exit("<br/>\n------------- : ");


        // $messages[] = $message;


        // $message['from'] = 'TEST';
        // $message['to'] = '22503612783';
        // $message['text'] = 'TEST2';

        // // echo "<br/>\n";
        // // //print_r($message);
        // // exit("<br/>\n------------- : ");

        // $messages[] = $message;

        // // echo "<br/>\n";
        // // //print_r($messages);
        // // echo "<br/>\n----------";


        // $tab_messages['messages'] = $messages;


        // echo "<br/>\n";
        // //print_r($tab_messages);
        // echo "<br/>\n----------";


        // $json = json_encode($tab_messages);
        // echo "<br/>\n";
        // //print_r($json);
        // echo "<br/>\n----------";


        $json = json_encode($tab_messages);

        //exit("<br/>\n------------- : ");



        // $url = 'https://193.105.74.159/sms/2/text/advanced';
        $url =  $this->base_url . '/sms/1/text/multi';
        //$url = 'https://193.105.74.159/sms/2/text/single';

        //$message = uniqid();

        // $to = $this->indice . $to;
        // $text = $message;
        // $from = $this->from;

        // $res = array(
        //     "from" => $from,
        //     "to" => "$to",
        //     "text" => "$text"
        // );

        // $json = '{  
        // "from":"InfoSMS",
        //     "to":"22503612783",
        //     "text":"Test SMS."
        //     }';

        // //var_dump($res);
        // $json = json_encode($res);
        // //var_dump($json);



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $retour;

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            //var_dump($response);
            // return $response;
            $retour = $response;
            $json_response = json_decode($response);
            //var_dump($json_response);

            $array_response = json_decode($response, true);
            //var_dump($array_response);

            //------------------------------------------------
            $now = new DateTime('NOW', new DateTimeZone(('UTC')));

            $data = [];

            if ($json_response->bulkId) {
                $data['bulkId'] = $json_response->bulkId;
                $messages = $json_response->messages;

                foreach ($messages as $key => $message) {
                    $data['a'] = $message->to;
                    $status = $message->status;
                    $data['groupId'] = $status->groupId;
                    $data['groupName'] = $status->groupName;
                    $data['id'] = $status->id;
                    $data['name'] = $status->name;
                    $data['description'] = $status->description;
                } //messages
                $data['messageId'] = $json_response->messageId;
                $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                $data['de'] = $this->getFrom();
                $data['tentative'] = 0;

                //------------------ insert dans outgoing
                $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at, tentative)  
                VALUES (:de, a:, :text , :status_sendsms, :message_id, :sendsms_at, :bulkId, :groupId, :groupName, :id, :name, :description, :tentative)');

                try {
                    $sth->execute($data);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
            }
        }




        // //print "RETOUR / <pre>";
        // //print_r($retour);



        // $data = [
        //     'de' => $this->getFrom(),
        //     'a' => $to,
        //     'text' => $message,
        //     'status_sendsms' => $retour,
        //     'message_id' => (($err) ? null : $response->messages[0]->messageId),
        //     'sendsms_at' => ($now->format('Y-m-d H:i:s'))
        // ];

        // $data = [
        //     $this->getFrom(),
        //     $to,
        //     $message,
        //     $retour,
        //     (($err) ? null : $response->messages[0]->messageId),
        //     ($now->format('Y-m-d H:i:s'))
        // ];

        // // //var_dump($now->format('Y-m-d H:i:s'));

        // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
        // $sth->execute($data);



        return $retour;
    } //sendMultiSMStoMultiDest



    public function sendMultiSMStoMultiDestV211052019($tab_messages)
    {

        $json = json_encode($tab_messages);

        //exit("<br/>\n------------- : ");


        $url =  $this->base_url . '/sms/2/text/advanced';

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $retour;

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            //var_dump($response);
            // return $response;
            $retour = $response;
            $json_response = json_decode($response);
            //var_dump($json_response);

            $array_response = json_decode($response, true);
            //var_dump($array_response);

            //------------------------------------------------
            $now = new DateTime('NOW', new DateTimeZone(('UTC')));

            $data = [];

            if ($json_response->bulkId) {
                $data['bulkId'] = $json_response->bulkId;
                $messages = $json_response->messages;

                //print "<br/>\n ======== messages responses <br/>\n";
                //var_dump($messages);

                // foreach ($messages as $key => $message) {
                //     $data['a'] = $message->to;
                //     $status = $message->status;
                //     $data['groupId'] = $status->groupId;
                //     $data['groupName'] = $status->groupName;
                //     $data['id'] = $status->id;
                //     $data['name'] = $status->name;
                //     $data['description'] = $status->description;
                // }//messages
                // $data['messageId'] = $json_response->messageId;
                // $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                // $data['de'] = $this->getFrom();

                // //------------------ insert dans outgoing
                // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  
                // VALUES (:de, a:, :text , :status_sendsms, :message_id, :sendsms_at, :bulkId, :groupId, :groupName, :id, :name, :description)');
                // $sth->execute($data);

            }
        }




        // //print "RETOUR / <pre>";
        // //print_r($retour);



        // $data = [
        //     'de' => $this->getFrom(),
        //     'a' => $to,
        //     'text' => $message,
        //     'status_sendsms' => $retour,
        //     'message_id' => (($err) ? null : $response->messages[0]->messageId),
        //     'sendsms_at' => ($now->format('Y-m-d H:i:s'))
        // ];

        // $data = [
        //     $this->getFrom(),
        //     $to,
        //     $message,
        //     $retour,
        //     (($err) ? null : $response->messages[0]->messageId),
        //     ($now->format('Y-m-d H:i:s'))
        // ];

        // // //var_dump($now->format('Y-m-d H:i:s'));

        // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
        // $sth->execute($data);



        return $retour;
    } //sendMultiSMStoMultiDestV211052019


    public function sendMultiSMStoMultiDestV2($tab_messages)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $json = json_encode($tab_messages);
        //exit("<br/>\n------------- : ");

        $url =  $this->base_url . '/sms/2/text/advanced';

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        $retour;

        print "<br/>\n<br/>\n ======== messages responses <br/>\n";

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            // //var_dump($response);
            //print_r($response);
            //print "<br/>\n";
            // return $response;
            $retour = $response;
            $json_response = json_decode($response);
            // print_r($json_response);
            //print "<br/>\n";

            $array_response = json_decode($response, true);
            //print_r($array_response);
            //print "<br/>\n";

            //------------------------------------------------
            // 


            if (!is_object($json_response)) {
                // print_r($json_response);
                // print "<br/>\n";
                // var_dump($json_response);
            } else {

                // var_dump($json_response);
                // var_dump($json);
                // var_dump($tab_messages);


                if ($json_response->bulkId) {

                    $dbh = $this->my_pdo->getConn();

                    $tab_message = $tab_messages;

                    // foreach ($tab_messages as $key => $tab_message) {
                    $data = [];
                    $data['de'] = $this->getFrom();


                    // //print "<br/>\n <pre>";
                    // //var_dump($key);
                    // //var_dump($tab_message);
                    // //print_r($tab_message);

                    // //print "<br/>\n <pre>";
                    // //var_dump( $tab_message);
                    // //print_r($tab_message);

                    $messages = $tab_message['messages'];

                    $data['bulkId'] = $tab_message['bulkId'];
                    // //print "<br/>\n <pre>";
                    // //var_dump($messages);
                    // //print_r($messages);

                    foreach ($messages as $key => $message) {

                        // //print "<br/>\n-------";
                        // //var_dump($key);
                        // //var_dump($message);

                        $data['from'] = $message['from'];
                        $destinations =  $message['destinations'];

                        // //var_dump("-----------POSITION---------");
                        // //print "<br/>\n <pre>";
                        // //print_r($destinations);


                        // foreach ($destinations as $destination) {
                        $data['a'] = $destinations['to'];
                        $data['message_id'] = $destinations['messageId'];
                        // }

                        $data['text'] =  $message['text'];

                        $data['flash'] =  $message['flash'];
                        $language =  $message['language'];
                        // foreach ($language as $lang) {
                        $data['languageCode'] =  $language['languageCode'];
                        // }
                        $data['transliteration'] =  $message['transliteration'];




                        ///---------------------
                        $response = $json_response;
                        if ($response->messages) {

                            // //print("<br/>\n---------ok");
                            $r_messages = $response->messages;
                            foreach ($r_messages as $key => $r_message) {
                                // //var_dump($key);
                                //var_dump($r_message);

                                $r_to = $r_message->to;
                                //var_dump($r_to);

                                $r_status = $r_message->status;
                                $groupId = $r_status->groupId;
                                $groupName = $r_status->groupName;
                                $id = $r_status->id;
                                $name = $r_status->name;
                                $description = $r_status->description;
                                $messageId = $r_message->messageId;



                                if ($messageId == $data['message_id']) {

                                    $data2 = [];
                                    $data2['bulkId'] = $tab_message['bulkId'];
                                    $data2['de'] = $this->getFrom();

                                    $data2['message_id'] = $messageId;
                                    $data2['a'] = $destinations['to'];
                                    $data2['text'] =  $message['text'];


                                    $data['status_sendsms'] = $messageId;
                                    $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                                    $data['letest'] = $messageId;
                                    $data['send_groupid'] = $groupId;
                                    $data['send_groupname'] = $groupName;
                                    $data['send_id'] = $id;
                                    $data['send_name'] = $name;
                                    $data['send_description'] = $description;

                                    $data2['status_sendsms'] = $messageId;
                                    $data2['sendsms_at'] = $now->format('Y-m-d H:i:s');
                                    $data2['letest'] = $messageId;
                                    $data2['send_groupid'] = $groupId;
                                    $data2['send_groupname'] = $groupName;
                                    $data2['send_id'] = $id;
                                    $data2['send_name'] = $name;
                                    $data2['send_description'] = $description;
                                    $data2['tentative'] = 0;




                                    //print "<br/>\n";
                                    //print "<br/>\n";
                                    //print "<br/>\n ========  data <br/>\n";
                                    //print_r($data);

                                    $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description, bulk_id, tentative)
                                VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description, :bulkId, :tentative)
                                ";
                                    $stmt = $dbh->prepare($sql);
                                    //print_r($sql);
                                    //var_dump($data2);

                                    try {
                                        $res = $stmt->execute($data2);
                                    } catch (\Throwable $th) {
                                        $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                                    }
                                    //var_dump($res);
                                    //--------------------



                                } //if






                            } //for
                            //exit("<br/>\n----------stop-----");
                            // //var_dump($r_messages);


                        } //if





                    } //for

                    // } //for






                    // $data['de'] = $this->getFrom();
                    // $data['bulkId'] = $json_response->bulkId;
                    // $messages = $json_response->messages;

                    // // //print "<br/>\n ======== messages responses <br/>\n";
                    // //print_r($messages);
                    // //print "<br/>\n";

                    // foreach ($messages as $key => $message) {
                    //     $data['a'] = $message->to;
                    //     $status = $message->status;
                    //     $data['groupId'] = $status->groupId;
                    //     $data['groupName'] = $status->groupName;
                    //     $data['id'] = $status->id;
                    //     $data['name'] = $status->name;
                    //     $data['description'] = $status->description;
                    //     $data['messageId'] = $message->messageId;
                    // } //messages






                    // $data['messageId'] = $json_response->messageId;
                    // $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                    // $data['de'] = $this->getFrom();

                    // //------------------ insert dans outgoing
                    // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  
                    // VALUES (:de, a:, :text , :status_sendsms, :message_id, :sendsms_at, :bulkId, :groupId, :groupName, :id, :name, :description)');
                    // $sth->execute($data);

                    ///-------------- 



                } //if  bulkid
            } //is objet
        } //err




        // //print "RETOUR / <pre>";
        // //print_r($retour);



        // $data = [
        //     'de' => $this->getFrom(),
        //     'a' => $to,
        //     'text' => $message,
        //     'status_sendsms' => $retour,
        //     'message_id' => (($err) ? null : $response->messages[0]->messageId),
        //     'sendsms_at' => ($now->format('Y-m-d H:i:s'))
        // ];

        // $data = [
        //     $this->getFrom(),
        //     $to,
        //     $message,
        //     $retour,
        //     (($err) ? null : $response->messages[0]->messageId),
        //     ($now->format('Y-m-d H:i:s'))
        // ];

        // // //var_dump($now->format('Y-m-d H:i:s'));

        // $sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
        // $sth->execute($data);



        return $retour;
    } //sendMultiSMStoMultiDestV2




    public function sendMultiSMStoMultiDestV3($tab_messages)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $json = json_encode($tab_messages);
        //exit("<br/>\n------------- : ");

        $url =  $this->base_url . '/sms/2/text/advanced';

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        // $retour;

        print "<br/>\n<br/>\n ======== messages responses <br/>\n";

        if ($err) {
            // echo "cURL Error #:" . $err;
            // //var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            // //var_dump($response);
            //print_r($response);
            //print "<br/>\n";
            // return $response;
            $retour = $response;
            $json_response = json_decode($response);
            // print_r($json_response);
            //print "<br/>\n";

            $array_response = json_decode($response, true);
            //print_r($array_response);
            //print "<br/>\n";

            //------------------------------------------------
            // 


            if (!is_object($json_response)) {
                // print_r($json_response);
                // print "<br/>\n";
                // var_dump($json_response);
            } else {

                // var_dump($json_response);
                // var_dump($json);
                // var_dump($tab_messages);


                if ($json_response->bulkId) {

                    $dbh = $this->my_pdo->getConn();

                    $tab_message = $tab_messages;

                    // foreach ($tab_messages as $key => $tab_message) {
                    $data = [];
                    $data['de'] = $this->getFrom();


                    // //print "<br/>\n <pre>";
                    // //var_dump($key);
                    // //var_dump($tab_message);
                    // //print_r($tab_message);

                    // //print "<br/>\n <pre>";
                    // //var_dump( $tab_message);
                    // //print_r($tab_message);

                    $messages = $tab_message['messages'];

                    $data['bulkId'] = $tab_message['bulkId'];

                    var_dump($messages);
                    // print "<br/>\n <pre>";
                    // print_r($messages);

                    // exit("<br/>\n -------exit-------------");

                    foreach ($messages as $key => $message) {
                        print "<br/>\n----message---";

                        var_dump($key);
                        var_dump($message);

                        $data['from'] = $message['from'];
                        $destinations =  $message['destinations'];
                        var_dump($destinations);
                        // exit("<br/>\n -------exit-------------");

                        // //var_dump("-----------POSITION---------");
                        // //print "<br/>\n <pre>";
                        // //print_r($destinations);


                        // foreach ($destinations as $destination) {
                        $data['a'] = $destinations['to'];
                        $data['message_id'] = $destinations['messageId'];
                        var_dump($data['message_id']);
                        // }

                        $data['text'] =  $message['text'];

                        $data['flash'] =  $message['flash'];
                        $language =  $message['language'];
                        // foreach ($language as $lang) {
                        $data['languageCode'] =  $language['languageCode'];
                        // }
                        $data['transliteration'] =  $message['transliteration'];

                        // exit("<br/>\n -------exit-------------");




                        ///---------------------
                        $response = $json_response;
                        if ($response->messages) {

                            // exit("<br/>\n -------exit-------------");


                            // //print("<br/>\n---------ok");
                            $r_messages = $response->messages;
                            var_dump($r_messages);


                            print("<br/>\n-----avant ----data-----");
                            var_dump($data);

                            foreach ($r_messages as $key => $r_message) {

                                print("<br/>\n--------apres -data-----");
                                var_dump($data);

                                // exit("<br/>\n -------exit-------------");


                                // //var_dump($key);
                                print("<br/>\n-------response message---");
                                // var_dump($r_message);

                                $r_to = $r_message->to;
                                //var_dump($r_to);

                                $r_status = $r_message->status;
                                $groupId = $r_status->groupId;
                                $groupName = $r_status->groupName;
                                $id = $r_status->id;
                                $name = $r_status->name;
                                $description = $r_status->description;
                                $messageId = $r_message->messageId;


                                // var_dump($r_messages);
                                // var_dump($messageId);
                                // var_dump($data);
                                // var_dump($data['message_id']);
                                // exit("<br/>\n -------exit-------------");




                                if ($messageId == $data['message_id']) {

                                    $data2 = [];
                                    $data2['bulkId'] = $tab_message['bulkId'];
                                    $data2['de'] = $this->getFrom();

                                    $data2['message_id'] = $messageId;
                                    $data2['a'] = $destinations['to'];
                                    $data2['text'] =  $message['text'];


                                    $data['status_sendsms'] = $messageId;
                                    $data['sendsms_at'] = $now->format('Y-m-d H:i:s');
                                    $data['letest'] = $messageId;
                                    $data['send_groupid'] = $groupId;
                                    $data['send_groupname'] = $groupName;
                                    $data['send_id'] = $id;
                                    $data['send_name'] = $name;
                                    $data['send_description'] = $description;

                                    $data2['status_sendsms'] = $messageId;
                                    $data2['sendsms_at'] = $now->format('Y-m-d H:i:s');
                                    $data2['letest'] = $messageId;
                                    $data2['send_groupid'] = $groupId;
                                    $data2['send_groupname'] = $groupName;
                                    $data2['send_id'] = $id;
                                    $data2['send_name'] = $name;
                                    $data2['send_description'] = $description;
                                    $data2['tentative'] = 0;

                                    var_dump($data);
                                    var_dump($data2);
                                    //exit("<br/>\n -------exit-------------");



                                    //print "<br/>\n";
                                    //print "<br/>\n";
                                    //print "<br/>\n ========  data <br/>\n";
                                    //print_r($data);

                                    $sql = "INSERT INTO outgoing(de, a, text, message_id, status_sendsms, sendsms_at, letest, send_groupid, send_groupname, send_id, send_name, send_description, bulk_id, tentative)
                                VALUES(:de , :a , :text , :message_id , :status_sendsms , :sendsms_at , :letest , :send_groupid , :send_groupname , :send_id , :send_name , :send_description, :bulkId, :tentative)
                                ";
                                    $stmt = $dbh->prepare($sql);
                                    //print_r($sql);
                                    //var_dump($data2);

                                    try {
                                        $res = $stmt->execute($data2);
                                    } catch (\Throwable $th) {
                                        $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                                    }
                                    //var_dump($res);


                                    # recuperer l'abonnement et faire la mise à jour dans la table chargesms , mettre traite à 1
                                    //$sql = "select a.compte from abonnement a where a.phone= :phone";
                                    $sql = "select a.compte FROM chargesms s , abonnement a  where s.traite=0 and s.compte = a.compte  and a.phone= :phone;";
                                    // var_dump($sql);
                                    print "<br/>\n<br/>\n phone => " . $data2['a'];
                                    $stmt = $dbh->prepare($sql);

                                    try {
                                        $stmt->execute(['phone' => $data2['a']]);
                                    } catch (\Throwable $th) {
                                        $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                                    }
                                    $abonnement = $stmt->fetch();
                                    var_dump($abonnement);

                                    $colonnes = "traite = :traite, datetrt = :datetrt ";

                                    $data = [
                                        'compte' => $abonnement['compte'],
                                        'traite' => '1',
                                        'datetrt' => $now->format('Y/m/d H:i:s')
                                    ];

                                    $conditions = "compte = :compte";

                                    $sql = "update chargesms set $colonnes where $conditions ";
                                    // var_dump($sql);
                                    // print "<br/>\n compte => " . $abonnement['compte'];

                                    print "<br/>\n <pre>";
                                    print_r($colonnes);
                                    print_r($conditions);
                                    print_r($data);

                                    $stmt = $dbh->prepare($sql);
                                    try {
                                        $res_update = $stmt->execute($data);
                                        var_dump($res_update);
                                        $count = $stmt->rowCount();

                                        if ($count == '0') {
                                            echo "Failed !";
                                        } else {
                                            echo "Success !";
                                        }
                                        var_dump($count);
                                    } catch (\Throwable $th) {
                                        $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                                    }
                                } //if

                            } //for
                            //exit("<br/>\n----------stop-----");
                            // //var_dump($r_messages);

                        } //if

                    } //for

                    ////$dbh->commit();
                } //if  bulkid
            } //is objet
        } //err

        return $retour;
    } //sendMultiSMStoMultiDestV3



    public function sendMultiSMStoMultiDestV4($tab_messages)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $json = json_encode($tab_messages);
        //exit("<br/>\n------------- : ");

        // print("url infobip: " . $this->base_url);
        $url =  $this->base_url . '/sms/2/text/advanced';

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $err;
        } else {
            return $response;
        }
    } //sendMultiSMStoMultiDestV4


    public function sendMultiSMStoMultiDestV5($tab_messages, $applic)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $json = json_encode($tab_messages);
        //exit("<br/>\n-------fik------ : ");

        $url =  $this->base_url . '/sms/2/text/advanced';
        // $url =  "https://g3lq8.api.infobip.com/sms/2/text/advanced";

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));





        $response = curl_exec($curl);
        $err = curl_error($curl);

        var_dump($response);
        var_dump($err);
        //exit("<br/>\n-------fik3------ : ");


        curl_close($curl);

        // $retour;
        if ($err) {
            $res_send = $err;
        } else {
            $res_send = $response;

            print("<br/>\n----responses -tab_messages----");



            print "<pre>";
            // var_dump($res_send);
            print_r($res_send);


            $dbh = $this->my_pdo->getConn();
            //-----------------------------------------------------
            # inserer la requête

            // print_r($tab_message);

            foreach ($tab_messages['messages'] as $tab_message) {
                $aller = [];
                $aller['bulkId'] = $tab_messages['bulkId'];
                $aller['from'] = $tab_message['from'];

                // var_dump($tab_message['destinations']);
                // print_r($tab_message['destinations']);

                $destinations = $tab_message['destinations'];

                // var_dump($destinations);
                // print_r($destinations);

                // foreach ($tab_message['destinations'] as $destinations) {
                $aller['to'] = $destinations['to'];
                $aller['messageId'] = $destinations['messageId'];
                // } //foreach($t_message['destinations'] as $destinations){
                $aller['text'] = $tab_message['text'];
                $aller['tentative'] = 0;
                $aller['applic'] = $applic;
                $aller['letest'] = $aller['messageId'];
                $aller['sendsms_at'] =  $now->format('Y-m-d H:i:s');
                $aller['status_sendsms'] =  $tab_messages['bulkId'];

                # inserer la requete
                $sql = "INSERT INTO outgoing(de, a, text, message_id,  sendsms_at, letest,  bulk_id, tentative, applic, status_sendsms)
                    VALUES(:from , :to , :text , :messageId  , :sendsms_at , :letest , :bulkId, :tentative, :applic, :status_sendsms)
                    ";

                $stmt = $dbh->prepare($sql);
                //print_r($sql);
                //var_dump($data2);
                $res = $stmt->execute($aller);

                $count = $stmt->rowCount();
                // print "<br/>\n insert count: $count";
                // print "<br/>\n insert count: $count";
                // var_dump("insert : $count");

                if ($count == '0') {
                    $retourErreur = '1';
                    $retourMessage = 'Echec [insert] outgoing';
                    //retour_json($retourErreur, $retourMessage, $retour);
                } else {
                    $retourErreur = '0';
                    $retourMessage = '';
                    //retour_json($retourErreur, $retourMessage, $retour);
                } //if ($count == '0') {



                // } // foreach($tab_message['messages'] as  $t_message)

            } // foreach($tab_messages as $tab_message){



            //-----------------------------------------------------
            //--------TRAITEMENT DE LA REPONSE----------------------
            //-----------------------------------------------------
            $tab_message = json_decode($response, true);
            print "<pre>";
            // var_dump($tab_message);
            print_r($tab_message);

            $retour = [];
            // $retour['bulkId'] = $tab_message['bulkId'];

            foreach ($tab_message['messages'] as $messages) {

                // print "<pre>";
                // var_dump($messages);
                // print_r($messages);

                $retour['to'] = $messages['to'];
                // print "<pre>";
                // var_dump($retour['to']);
                // print_r($retour['to']);

                $retour['send_groupid'] = $messages['status']['groupId'];
                $retour['send_groupname'] = $messages['status']['groupName'];
                $retour['send_id'] = $messages['status']['id'];
                $retour['send_name'] = $messages['status']['name'];
                $retour['send_description'] = $messages['status']['description'];
                $retour['message_id'] = $messages['messageId'];


                $colonnes = "
                    send_groupid = :send_groupid,
                    send_groupname = :send_groupname,
                    send_id = :send_id,
                    send_name = :send_name,
                   send_description = :send_description,
                    message_id = :message_id
                ";
                // print "colonnes : $colonnes";
                // var_dump($retour);


                # enregistrer le statut de la requete
                $sql =  "update outgoing set $colonnes where message_id = :message_id ";
                // print($sql);
                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($retour);
                    // var_dump($res_update);
                    $count = $stmt->rowCount();
                    // print("<br/>\n update count " . $count);

                    if ($count == '0') {
                        $retourErreur = '1';
                        $retourMessage =  'Echec [update] outgoing';
                        // retour_json($retourErreur, $retourMessage, $retour);
                    } else {

                        $retourErreur = '0';
                        $retourMessage = '';
                        // retour_json($retourErreur, $retourMessage, $retour);
                    }
                    //code...
                } catch (\Throwable $th) {
                    print_r($th);
                }


                //------------------------------------------------
                # recuperer l'abonnement et faire la mise à jour dans la table chargesms , mettre traite à 1
                //$sql = "select a.compte from abonnement a where a.phone= :phone";
                $sql = "select a.compte FROM chargesms s , abonnement a  where s.traite=0 and s.compte = a.compte  and a.phone= :phone;";
                // var_dump($sql);
                print "<br/>\n<br/>\n phone => " . $retour['to'];
                $stmt = $dbh->prepare($sql);

                try {
                    $stmt->execute(['phone' => $retour['to']]);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
                $abonnement = $stmt->fetch();
                // var_dump($abonnement);


                $colonnes = "traite = :traite, datetrt = :datetrt ";

                $data = [
                    'compte' => $abonnement['compte'],
                    'traite' => '1',
                    'datetrt' => $now->format('Y/m/d H:i:s')
                ];

                $conditions = "compte = :compte";

                $sql = "update chargesms set $colonnes where $conditions ";
                // var_dump($sql);
                // print "<br/>\n compte => " . $abonnement['compte'];

                // print "<br/>\n <pre>";
                // print_r($colonnes);
                // print_r($conditions);
                // print_r($data);

                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($data);
                    // var_dump($res_update);
                    $count = $stmt->rowCount();

                    if ($count == '0') {
                        echo "Failed !";
                    } else {
                        echo "Success !";
                    }
                    // var_dump($count);
                    print "$count <br/>\n";
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
                //-----------------------------

            } //foreach ($tab_message['messages'] as $messages) {




            //-----------------------------------------------------
        }

        # fik var_dump($res_send);
        return $res_send;
    } //sendMultiSMStoMultiDestV5



    public function sendMultiSMStoMultiDestV5_test($tab_messages, $applic, $url = null)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $json = json_encode($tab_messages);
        //exit("<br/>\n-------fik------ : ");

        if ($url == null) {
            //$url =  $this->base_url . '/sms/2/text/advanced';
            $url =  "https://g3lq8.api.infobip.com/sms/2/text/advanced";
        }

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));





        $response = curl_exec($curl);
        $err = curl_error($curl);

        var_dump($response);
        var_dump($err);
        //exit("<br/>\n-------fik3------ : ");


        curl_close($curl);

        // $retour;
        if ($err) {
            $res_send = $err;
        } else {
            $res_send = $response;

            print("<br/>\n----responses -tab_messages----");



            print "<pre>";
            // var_dump($res_send);
            print_r($res_send);


            $dbh = $this->my_pdo->getConn();
            //-----------------------------------------------------
            # inserer la requête

            // print_r($tab_message);

            foreach ($tab_messages['messages'] as $tab_message) {
                $aller = [];
                $aller['bulkId'] = $tab_messages['bulkId'];
                $aller['from'] = $tab_message['from'];

                // var_dump($tab_message['destinations']);
                // print_r($tab_message['destinations']);

                $destinations = $tab_message['destinations'];

                // var_dump($destinations);
                // print_r($destinations);

                // foreach ($tab_message['destinations'] as $destinations) {
                $aller['to'] = $destinations['to'];
                $aller['messageId'] = $destinations['messageId'];
                // } //foreach($t_message['destinations'] as $destinations){
                $aller['text'] = $tab_message['text'];
                $aller['tentative'] = 0;
                $aller['applic'] = $applic;
                $aller['letest'] = $aller['messageId'];
                $aller['sendsms_at'] =  $now->format('Y-m-d H:i:s');
                $aller['status_sendsms'] =  $tab_messages['bulkId'];

                # inserer la requete
                $sql = "INSERT INTO outgoing(de, a, text, message_id,  sendsms_at, letest,  bulk_id, tentative, applic, status_sendsms)
                    VALUES(:from , :to , :text , :messageId  , :sendsms_at , :letest , :bulkId, :tentative, :applic, :status_sendsms)
                    ";

                $stmt = $dbh->prepare($sql);
                //print_r($sql);
                //var_dump($data2);
                $res = $stmt->execute($aller);

                $count = $stmt->rowCount();
                // print "<br/>\n insert count: $count";
                // print "<br/>\n insert count: $count";
                // var_dump("insert : $count");

                if ($count == '0') {
                    $retourErreur = '1';
                    $retourMessage = 'Echec [insert] outgoing';
                    //retour_json($retourErreur, $retourMessage, $retour);
                } else {
                    $retourErreur = '0';
                    $retourMessage = '';
                    //retour_json($retourErreur, $retourMessage, $retour);
                } //if ($count == '0') {



                // } // foreach($tab_message['messages'] as  $t_message)

            } // foreach($tab_messages as $tab_message){



            //-----------------------------------------------------
            //--------TRAITEMENT DE LA REPONSE----------------------
            //-----------------------------------------------------
            $tab_message = json_decode($response, true);
            print "<pre>";
            // var_dump($tab_message);
            print_r($tab_message);

            $retour = [];
            // $retour['bulkId'] = $tab_message['bulkId'];

            foreach ($tab_message['messages'] as $messages) {

                // print "<pre>";
                // var_dump($messages);
                // print_r($messages);

                $retour['to'] = $messages['to'];
                // print "<pre>";
                // var_dump($retour['to']);
                // print_r($retour['to']);

                $retour['send_groupid'] = $messages['status']['groupId'];
                $retour['send_groupname'] = $messages['status']['groupName'];
                $retour['send_id'] = $messages['status']['id'];
                $retour['send_name'] = $messages['status']['name'];
                $retour['send_description'] = $messages['status']['description'];
                $retour['message_id'] = $messages['messageId'];


                $colonnes = "
                    send_groupid = :send_groupid,
                    send_groupname = :send_groupname,
                    send_id = :send_id,
                    send_name = :send_name,
                   send_description = :send_description,
                    message_id = :message_id
                ";
                // print "colonnes : $colonnes";
                // var_dump($retour);


                # enregistrer le statut de la requete
                $sql =  "update outgoing set $colonnes where message_id = :message_id ";
                // print($sql);
                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($retour);
                    // var_dump($res_update);
                    $count = $stmt->rowCount();
                    // print("<br/>\n update count " . $count);

                    if ($count == '0') {
                        $retourErreur = '1';
                        $retourMessage =  'Echec [update] outgoing';
                        // retour_json($retourErreur, $retourMessage, $retour);
                    } else {

                        $retourErreur = '0';
                        $retourMessage = '';
                        // retour_json($retourErreur, $retourMessage, $retour);
                    }
                    //code...
                } catch (\Throwable $th) {
                    print_r($th);
                }


                //------------------------------------------------
                # recuperer l'abonnement et faire la mise à jour dans la table chargesms , mettre traite à 1
                //$sql = "select a.compte from abonnement a where a.phone= :phone";
                $sql = "select a.compte FROM chargesms s , abonnement a  where s.traite=0 and s.compte = a.compte  and a.phone= :phone;";
                // var_dump($sql);
                print "<br/>\n<br/>\n phone => " . $retour['to'];
                $stmt = $dbh->prepare($sql);

                try {
                    $stmt->execute(['phone' => $retour['to']]);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
                $abonnement = $stmt->fetch();
                // var_dump($abonnement);


                $colonnes = "traite = :traite, datetrt = :datetrt ";

                $data = [
                    'compte' => $abonnement['compte'],
                    'traite' => '1',
                    'datetrt' => $now->format('Y/m/d H:i:s')
                ];

                $conditions = "compte = :compte";

                $sql = "update chargesms set $colonnes where $conditions ";
                // var_dump($sql);
                // print "<br/>\n compte => " . $abonnement['compte'];

                // print "<br/>\n <pre>";
                // print_r($colonnes);
                // print_r($conditions);
                // print_r($data);

                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($data);
                    // var_dump($res_update);
                    $count = $stmt->rowCount();

                    if ($count == '0') {
                        echo "Failed !";
                    } else {
                        echo "Success !";
                    }
                    // var_dump($count);
                    print "$count <br/>\n";
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
                //-----------------------------

            } //foreach ($tab_message['messages'] as $messages) {




            //-----------------------------------------------------
        }

        # fik var_dump($res_send);
        return $res_send;
    } //sendMultiSMStoMultiDestV5




    public function sendMultiSMStoMultiDestScheduleInfo($tab_messages, $applic)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        $json = json_encode($tab_messages);
        //exit("<br/>\n------------- : ");

        // $url = 'https://193.105.74.159/sms/2/text/advanced';
        $url =  $this->base_url . '/sms/1/text/advanced';

        //$message = uniqid();

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));


        $response = curl_exec($curl);
        $err = curl_error($curl);

        // print_r($response);
        // print_r($err);

        curl_close($curl);

        // $retour;
        if ($err) {
            $res_send = $err;
        } else {
            $res_send = $response;

            print("<br/>\n----responses -tab_messages----");

            print "<pre>";
            // var_dump($res_send);
            print_r($res_send);


            $dbh = $this->my_pdo->getConn();
            //-----------------------------------------------------
            # inserer la requête

            // print_r($tab_message);

            foreach ($tab_messages['messages'] as $tab_message) {
                $aller = [];
                $aller['bulkId'] = $tab_messages['bulkId'];
                $aller['from'] = $tab_message['from'];

                // var_dump($tab_message['destinations']);
                // print_r($tab_message['destinations']);

                $destinations = $tab_message['destinations'];

                // var_dump($destinations);
                // print_r($destinations);

                // foreach ($tab_message['destinations'] as $destinations) {
                $aller['to'] = $destinations['to'];
                $aller['messageId'] = $destinations['messageId'];
                // } //foreach($t_message['destinations'] as $destinations){
                $aller['text'] = $tab_message['text'];
                $aller['tentative'] = 0;
                $aller['applic'] = $applic;
                $aller['letest'] = $aller['messageId'];
                $aller['sendsms_at'] =  $now->format('Y-m-d H:i:s');
                $aller['status_sendsms'] =  $tab_messages['bulkId'];

                # inserer la requete
                $sql = "INSERT INTO outgoing(de, a, text, message_id,  sendsms_at, letest,  bulk_id, tentative, applic, status_sendsms)
                    VALUES(:from , :to , :text , :messageId  , :sendsms_at , :letest , :bulkId, :tentative, :applic, :status_sendsms)
                    ";

                $stmt = $dbh->prepare($sql);
                //print_r($sql);
                //var_dump($data2);
                $res = $stmt->execute($aller);

                $count = $stmt->rowCount();
                // print "<br/>\n insert count: $count";
                // print "<br/>\n insert count: $count";
                // var_dump("insert : $count");

                if ($count == '0') {
                    $retourErreur = '1';
                    $retourMessage = 'Echec [insert] outgoing';
                    //retour_json($retourErreur, $retourMessage, $retour);
                } else {
                    $retourErreur = '0';
                    $retourMessage = '';
                    //retour_json($retourErreur, $retourMessage, $retour);
                } //if ($count == '0') {



                // } // foreach($tab_message['messages'] as  $t_message)

            } // foreach($tab_messages as $tab_message){



            //-----------------------------------------------------
            //--------TRAITEMENT DE LA REPONSE----------------------
            //-----------------------------------------------------
            $tab_message = json_decode($response, true);
            print "<pre>";
            // var_dump($tab_message);
            print_r($tab_message);

            $retour = [];
            // $retour['bulkId'] = $tab_message['bulkId'];

            foreach ($tab_message['messages'] as $messages) {

                // print "<pre>";
                // var_dump($messages);
                // print_r($messages);

                $retour['to'] = $messages['to'];
                // print "<pre>";
                // var_dump($retour['to']);
                // print_r($retour['to']);

                $retour['send_groupid'] = $messages['status']['groupId'];
                $retour['send_groupname'] = $messages['status']['groupName'];
                $retour['send_id'] = $messages['status']['id'];
                $retour['send_name'] = $messages['status']['name'];
                $retour['send_description'] = $messages['status']['description'];
                $retour['message_id'] = $messages['messageId'];


                $colonnes = "
                    send_groupid = :send_groupid,
                    send_groupname = :send_groupname,
                    send_id = :send_id,
                    send_name = :send_name,
                send_description = :send_description,
                    message_id = :message_id
                ";
                // print "colonnes : $colonnes";
                // var_dump($retour);


                # enregistrer le statut de la requete
                $sql =  "update outgoing set $colonnes where message_id = :message_id ";
                // print($sql);
                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($retour);
                    // var_dump($res_update);
                    $count = $stmt->rowCount();
                    // print("<br/>\n update count " . $count);

                    if ($count == '0') {
                        $retourErreur = '1';
                        $retourMessage =  'Echec [update] outgoing';
                        // retour_json($retourErreur, $retourMessage, $retour);
                    } else {

                        $retourErreur = '0';
                        $retourMessage = '';
                        // retour_json($retourErreur, $retourMessage, $retour);
                    }
                    //code...
                } catch (\Throwable $th) {
                    print_r($th);
                }


                //------------------------------------------------
                # recuperer l'abonnement et faire la mise à jour dans la table chargesms , mettre traite à 1
                //$sql = "select a.compte from abonnement a where a.phone= :phone";
                $sql = "select a.compte FROM chargesms s , abonnement a  where s.traite=0 and s.compte = a.compte  and a.phone= :phone;";
                // var_dump($sql);
                print "<br/>\n<br/>\n phone => " . $retour['to'];
                $stmt = $dbh->prepare($sql);

                try {
                    $stmt->execute(['phone' => $retour['to']]);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
                $abonnement = $stmt->fetch();
                // var_dump($abonnement);


                $colonnes = "traite = :traite, datetrt = :datetrt ";

                $data = [
                    'compte' => $abonnement['compte'],
                    'traite' => '1',
                    'datetrt' => $now->format('Y/m/d H:i:s')
                ];

                $conditions = "compte = :compte";

                $sql = "update chargesms set $colonnes where $conditions ";
                // var_dump($sql);
                // print "<br/>\n compte => " . $abonnement['compte'];

                // print "<br/>\n <pre>";
                // print_r($colonnes);
                // print_r($conditions);
                // print_r($data);

                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($data);
                    // var_dump($res_update);
                    $count = $stmt->rowCount();

                    if ($count == '0') {
                        echo "Failed !";
                    } else {
                        echo "Success !";
                    }
                    // var_dump($count);
                    print "$count <br/>\n";
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
                //-----------------------------

            } //foreach ($tab_message['messages'] as $messages) {




            //-----------------------------------------------------
        }

        var_dump($res_send);
        return $res_send;
    } //sendMultiSMStoMultiDestScheduleInfo




    public function getMessageScheduleInfo($bulkId)
    {
        $now = new DateTime('NOW', new DateTimeZone(('UTC')));

        // $json = json_encode($tab_messages);
        //exit("<br/>\n------------- : ");

        // $url = 'https://193.105.74.159/sms/2/text/advanced';
        // $url = 'https://193.105.74.159/sms/1/text/advanced';
        // $url = 'http://193.105.74.159/sms/1/bulks?bulkId={bulkId}';
        $url =  $this->base_url . '/sms/1/bulks?bulkId=' . $bulkId;

        var_dump($url);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            // CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_CUSTOMREQUEST => "GET",
            // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
            // CURLOPT_POSTFIELDS => "$json",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                // "authorization: Basic " . base64_encode("$user:$pass"),
                "authorization: Basic " . base64_encode("$this->user:$this->pass"),
                "content-type: application/json"
            ),
        ));



        print("<br/>\n<br/>\n----responses -tab_messages----<br/>\n");


        $response = curl_exec($curl);
        $err = curl_error($curl);

        print "<pre>";
        print_r($response);
        print_r($err);


        //BBG-BULK-ID-5d4c23d3b85ce
        //BBG-MSG-ID-5d4c23d3b859a



        curl_close($curl);

        // $retour;
        if ($err) {
            $res_send = $err;
        } else {
            $res_send = $response;

            print("<br/>\n----responses -tab_messages----");



            print "<pre>";
            // var_dump($res_send);
            print_r($res_send);


            $dbh = $this->my_pdo->getConn();
            //-----------------------------------------------------
            # inserer la requête

            // print_r($tab_message);

            // foreach ($tab_messages['messages'] as $tab_message) {
            //     $aller = [];
            //     $aller['bulkId'] = $tab_messages['bulkId'];
            //     $aller['from'] = $tab_message['from'];

            //     // var_dump($tab_message['destinations']);
            //     // print_r($tab_message['destinations']);

            //     $destinations = $tab_message['destinations'];

            //     // var_dump($destinations);
            //     // print_r($destinations);

            //     // foreach ($tab_message['destinations'] as $destinations) {
            //     $aller['to'] = $destinations['to'];
            //     $aller['messageId'] = $destinations['messageId'];
            //     // } //foreach($t_message['destinations'] as $destinations){
            //     $aller['text'] = $tab_message['text'];
            //     $aller['tentative'] = 0;
            //     $aller['applic'] = $applic;
            //     $aller['letest'] = $aller['messageId'];
            //     $aller['sendsms_at'] =  $now->format('Y-m-d H:i:s');
            //     $aller['status_sendsms'] =  $tab_messages['bulkId'];

            //     # inserer la requete
            //     $sql = "INSERT INTO outgoing(de, a, text, message_id,  sendsms_at, letest,  bulk_id, tentative, applic, status_sendsms)
            //         VALUES(:from , :to , :text , :messageId  , :sendsms_at , :letest , :bulkId, :tentative, :applic, :status_sendsms)
            //         ";

            //     $stmt = $dbh->prepare($sql);
            //     //print_r($sql);
            //     //var_dump($data2);
            //     $res = $stmt->execute($aller);

            //     $count = $stmt->rowCount();
            //     // print "<br/>\n insert count: $count";
            //     // print "<br/>\n insert count: $count";
            //     // var_dump("insert : $count");

            //     if ($count == '0') {
            //         $retourErreur = '1';
            //         $retourMessage = 'Echec [insert] outgoing';
            //         //retour_json($retourErreur, $retourMessage, $retour);
            //     } else {
            //         $retourErreur = '0';
            //         $retourMessage = '';
            //         //retour_json($retourErreur, $retourMessage, $retour);
            //     } //if ($count == '0') {



            //     // } // foreach($tab_message['messages'] as  $t_message)

            // } // foreach($tab_messages as $tab_message){



            //-----------------------------------------------------
            //--------TRAITEMENT DE LA REPONSE----------------------
            //-----------------------------------------------------
            $tab_message = json_decode($response, true);
            print "<pre>";
            // var_dump($tab_message);
            print_r($tab_message);

            $retour = [];
            // $retour['bulkId'] = $tab_message['bulkId'];

            // foreach ($tab_message['messages'] as $messages) {

            //     // print "<pre>";
            //     // var_dump($messages);
            //     // print_r($messages);

            //     $retour['to'] = $messages['to'];
            //     // print "<pre>";
            //     // var_dump($retour['to']);
            //     // print_r($retour['to']);

            //     $retour['send_groupid'] = $messages['status']['groupId'];
            //     $retour['send_groupname'] = $messages['status']['groupName'];
            //     $retour['send_id'] = $messages['status']['id'];
            //     $retour['send_name'] = $messages['status']['name'];
            //     $retour['send_description'] = $messages['status']['description'];
            //     $retour['message_id'] = $messages['messageId'];


            //     $colonnes = "
            //         send_groupid = :send_groupid,
            //         send_groupname = :send_groupname,
            //         send_id = :send_id,
            //         send_name = :send_name,
            //     send_description = :send_description,
            //         message_id = :message_id
            //     ";
            //     // print "colonnes : $colonnes";
            //     // var_dump($retour);


            //     # enregistrer le statut de la requete
            //     $sql =  "update outgoing set $colonnes where message_id = :message_id ";
            //     // print($sql);
            //     $stmt = $dbh->prepare($sql);
            //     try {
            //         $res_update = $stmt->execute($retour);
            //         // var_dump($res_update);
            //         $count = $stmt->rowCount();
            //         // print("<br/>\n update count " . $count);

            //         if ($count == '0') {
            //             $retourErreur = '1';
            //             $retourMessage =  'Echec [update] outgoing';
            //             // retour_json($retourErreur, $retourMessage, $retour);
            //         } else {

            //             $retourErreur = '0';
            //             $retourMessage = '';
            //             // retour_json($retourErreur, $retourMessage, $retour);
            //         }
            //         //code...
            //     } catch (\Throwable $th) {
            //         print_r($th);
            //     }


            //     //------------------------------------------------
            //     # recuperer l'abonnement et faire la mise à jour dans la table chargesms , mettre traite à 1
            //     //$sql = "select a.compte from abonnement a where a.phone= :phone";
            //     $sql = "select a.compte FROM chargesms s , abonnement a  where s.traite=0 and s.compte = a.compte  and a.phone= :phone;";
            //     // var_dump($sql);
            //     print "<br/>\n<br/>\n phone => " . $retour['to'];
            //     $stmt = $dbh->prepare($sql);

            //     try {
            //         $stmt->execute(['phone' => $retour['to']]);
            //     } catch (\Throwable $th) {
            //         $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
            //     }
            //     $abonnement = $stmt->fetch();
            //     // var_dump($abonnement);


            //     $colonnes = "traite = :traite, datetrt = :datetrt ";

            //     $data = [
            //         'compte' => $abonnement['compte'],
            //         'traite' => '1',
            //         'datetrt' => $now->format('Y/m/d H:i:s')
            //     ];

            //     $conditions = "compte = :compte";

            //     $sql = "update chargesms set $colonnes where $conditions ";
            //     // var_dump($sql);
            //     // print "<br/>\n compte => " . $abonnement['compte'];

            //     // print "<br/>\n <pre>";
            //     // print_r($colonnes);
            //     // print_r($conditions);
            //     // print_r($data);

            //     $stmt = $dbh->prepare($sql);
            //     try {
            //         $res_update = $stmt->execute($data);
            //         // var_dump($res_update);
            //         $count = $stmt->rowCount();

            //         if ($count == '0') {
            //             echo "Failed !";
            //         } else {
            //             echo "Success !";
            //         }
            //         // var_dump($count);
            //         print "$count <br/>\n";
            //     } catch (\Throwable $th) {
            //         $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
            //     }
            //     //-----------------------------

            // } //foreach ($tab_message['messages'] as $messages) {




            //-----------------------------------------------------
        }

        var_dump($res_send);
        return $res_send;
    } //getMessageScheduleInfo




    public function getDelivreryReports($messageId = null)
    {
        // $str='2019-02-20T22:47:25.573+0000';
        // //var_dump($str);
        //  $now =  new DateTime($str, new DateTimeZone(('UTC')));
        //  $now_str = $now->format('Y-m-d H:i:s');
        // //var_dump($now_str);
        // // //var_dump($now->format('d-m-Y H:i:s'));


        // exit("<br/>\n----------");
        //  $now = new DateTime('NOW', new DateTimeZone(('UTC')));
        //  $now = new DateTime('NOW', new DateTimeZone(('UTC')));


        //  exit("<br/>\n-------------quitter-------");
        $dbh = $this->my_pdo->getConn();

        // $url = 'http://193.105.74.159/sms/1/reports';
        $url =  $this->base_url . '/sms/1/reports';
        //  CURLOPT_URL => "http://193.105.74.159/sms/1/reports",

        if ($messageId == null) {
        } else {
            $url = $url . '?messageId=' . $messageId;
        }
        //print "<br/>\n url: $url <br/>\n";


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass") . "",
            ),
        ));



        $response = curl_exec($curl);
        $err = curl_error($curl);


        curl_close($curl);


        print "<br/>\n-------------------response---------------";

        if ($err) {
            echo "cURL Error #:" . $err;
            return $err;
        } else {

            $obj = json_decode($response);
            // print "<pre>";
            // print_r($obj);


            if ($obj->results) {


                $results = $obj->results;
                //print_r($results);

                $tab = json_decode($response, true);
                print "<pre>";
                print_r($tab);
                // //var_dump($tab);



                // $sms_count = $results[0]->smsCount;
                // //var_dump($sms_count);
                $messageId = $results[0]->messageId;
                $to = $results[0]->to;
                $from = $results[0]->from;
                $sentAt = $results[0]->sentAt;
                $doneAt = $results[0]->doneAt;
                $smsCount = $results[0]->smsCount;
                $mccMnc = $results[0]->mccMnc;
                $price = $results[0]->price;
                $pricePerMessage = $price->pricePerMessage;
                $currency = $price->currency;

                $status = $results[0]->status;
                $status_groupId = $status->groupId;
                $status_groupName = $status->groupName;
                $status_id = $status->id;
                $status_name = $status->name;
                $status_description = $status->description;

                $error = $results[0]->error;
                $error_groupId = $error->groupId;
                $error_groupName = $error->groupName;
                $error_id = $error->id;
                $error_name = $error->name;
                $error_description = $error->description;
                $error_permanent = $error->permanent;



                ### faire la mise à jour
                $sentAt =  new DateTime($sentAt, new DateTimeZone(('UTC')));
                $sentAt_str = $sentAt->format('Y-m-d H:i:s');

                $doneAt =  new DateTime($doneAt, new DateTimeZone(('UTC')));
                $doneAt_str = $doneAt->format('Y-m-d H:i:s');





                // $data = [
                //     'results_reports' => true,
                //     'message_id' => $messageId,
                // ];

                // $sql = "UPDATE outgoing SET results_reports= :results_reports WHERE message_id= :message_id ";
                // $stmt = $dbh->prepare($sql);

                // $sql = "UPDATE outgoing SET results_reports= :results_reports WHERE message_id= :message_id ";
                // $stmt = $dbh->prepare($sql);

                // $res = $stmt->execute($data);

                // $res = $this->my_pdo->__update('outgoing', $data, "message_id = '$messageId' ");
                // var_dump($res);


                $data = [
                    'message_id' => $messageId,
                    'results_reports' => true,
                    'report_sentat' => $sentAt_str,
                    'report_doneat' => $doneAt_str,
                    'sms_count' => $smsCount,
                    'report_mccmnc' => $mccMnc,
                    'report_pricepermessage' => $pricePerMessage,
                    'report_currency' => $currency,

                    'report_status_groupid' => $status_groupId,
                    'report_status_groupname' => $status_groupName,
                    'report_status_id' => $status_id,
                    'report_status_name' => $status_name,
                    'report_status_description' => $status_description,

                    'report_error_groupid' => $error_groupId,
                    'report_error_groupname' => $error_groupName,
                    'report_error_id' => $error_id,
                    'report_error_name' => $error_name,
                    'report_error_description' => $error_description,
                    'report_error_permanent' => $error_permanent,
                ];


                $colonnes = "
                    results_reports = :results_reports,
                    report_sentat = :report_sentat,
                    report_doneat = :report_doneat,
                    sms_count = :sms_count,
                    report_mccmnc = :report_mccmnc,
                    report_pricepermessage = :report_pricepermessage,
                    report_currency = :report_currency,
                    report_status_groupid = :report_status_groupid,
                    report_status_groupname = :report_status_groupname,
                    report_status_id = :report_status_id,
                    report_status_name = :report_status_name,
                    report_status_description = :report_status_description,
                    report_error_groupid = :report_error_groupid,
                    report_error_groupname = :report_error_groupname,
                    report_error_id = :report_error_id,
                    report_error_name = :report_error_name,
                    report_error_description = :report_error_description,
                    report_error_permanent = :report_error_permanent
                ";
                print_r($data);
                print_r($colonnes);



                $sql =  "update outgoing set $colonnes where message_id = :message_id ";
                // var_dump($sql);
                //print  "<br/>\n compte => " . $abonnement['compte'];
                $stmt = $dbh->prepare($sql);
                try {
                    $res_update = $stmt->execute($data);
                    var_dump($res_update);
                    $count = $stmt->rowCount();

                    if ($count == '0') {
                        echo "Failed !";
                    } else {
                        echo "Success !";
                    }
                    var_dump($count);
                } catch (\Throwable $th) {
                    $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                }
            } // if ($obj->results) {


            return $response;
        }
    } //getDelivreryReports

    public function getDelivreryReportsV2($messageId)
    {

        //  exit("<br/>\n-------------quitter-------");
        // $dbh = $this->my_pdo->getConn();

        // $url = 'http://193.105.74.159/sms/1/reports';
        $url =  $this->base_url . '/sms/1/reports';
        //  CURLOPT_URL => "http://193.105.74.159/sms/1/reports",

        // if ($messageId == null) { } else {
        $url = $url . '?messageId=' . $messageId;
        // }
        //print "<br/>\n url: $url <br/>\n";


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass") . "",
            ),
        ));


        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        // print "<br/>\n-------------------response---------------";

        if ($err) {
            // echo "cURL Error #:" . $err;
            return $err;
        } else {
            return $response;
        }
    } //getDelivreryReportsV2


    // public function sgetSmsLogs($from = null, $to = null)
    // public function getSmsLogs($messageId = null)
    public function getSmsLogs($limit = null)
    {

        // $url = 'https://193.105.74.159/sms/1/logs';
        $url =  $this->base_url . '/sms/1/logs';

        // if ($from == null and $to == null) {

        // } elseif ($from != null) {
        //     $url = "$url?from=$from";
        // } elseif ($to != null) {
        //     $url = "$url?to=$to";
        // } else {
        //     $url = "$url?from=$from&to=$to";
        // }

        // if ($messageId != null) {
        //     $url = "$url?messageId=$messageId";
        // }
        if ($limit != null) {
            $url = "$url?limit=$limit";
        }
        // print "<br/>\n url: $url";




        $curl = curl_init();

        curl_setopt_array($curl, array(
            //   CURLOPT_URL => "http://193.105.74.159/sms/1/logs",
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass") . "",
            ),
        ));



        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);



        // if ($err) {
        //     //echo "cURL Error #:" . $err;
        // } else {
        //     // echo $response;
        // }
        //var_dump($err);
        //var_dump($response);
        // exit("<br/>\n-----------quit---------GET SMS LOG");



        if ($err) {
            echo "cURL Error #:" . $err;
            return $err;
        } else {
            echo '<pre>';
            var_dump($response);
            print("<br/>\n");
            $tab = json_decode($response, true);
            print_r($tab);
            $obj = json_decode($response);
            print_r($obj);

            // foreach ($tab as $t) {
            //     //var_dump($t);
            // }



            $dbh = $this->my_pdo->getConn();


            $results = $obj->results;
            foreach ($results as $result) {

                $messageId = $result->messageId;
                $to = $result->to;
                $from = $result->from;
                $sentAt = $result->sentAt;
                $doneAt = $result->doneAt;
                $smsCount = $result->smsCount;
                $mccMnc = $result->mccMnc;
                $price = $result->price;
                $pricePerMessage = $price->pricePerMessage;
                $currency = $price->currency;

                $status = $result->status;
                $status_groupId = $status->groupId;
                $status_groupName = $status->groupName;
                $status_id = $status->id;
                $status_name = $status->name;
                $status_description = $status->description;

                $error = $result->error;
                $error_groupId = $error->groupId;
                $error_groupName = $error->groupName;
                $error_id = $error->id;
                $error_name = $error->name;
                $error_description = $error->description;
                $error_permanent = $error->permanent;

                ### faire la mise à jour
                $sentAt =  new DateTime($sentAt, new DateTimeZone(('UTC')));
                $sentAt_str = $sentAt->format('Y-m-d H:i:s');

                $doneAt =  new DateTime($doneAt, new DateTimeZone(('UTC')));
                $doneAt_str = $doneAt->format('Y-m-d H:i:s');

                $data = [
                    // 'message_id' => $messageId,
                    'results_logs' => true,
                    'report_sentat' => $sentAt_str,
                    'report_doneat' => $doneAt_str,
                    'sms_count' => $smsCount,
                    'report_mccmnc' => $mccMnc,
                    'report_pricepermessage' => $pricePerMessage,
                    'report_currency' => $currency,

                    'report_status_groupid' => $status_groupId,
                    'report_status_groupname' => $status_groupName,
                    'report_status_id' => $status_id,
                    'report_status_name' => $status_name,
                    'report_status_description' => $status_description,

                    'report_error_groupid' => $error_groupId,
                    'report_error_groupname' => $error_groupName,
                    'report_error_id' => $error_id,
                    'report_error_name' => $error_name,
                    'report_error_description' => $error_description,
                    'report_error_permanent' => $error_permanent,

                ];

                //print "<br/>\n-----getSmsLogs-----------<pre>";
                //print_r($data);

                $res = $this->my_pdo->__update('outgoing', $data, "message_id = '$messageId' ");
                // var_dump($res);
            } //for



            return $response;
        } //$err


    } //getSmsLogs





    public function getSmsLogsV2($from, $to, $bulkId, $messageId)
    {

        // $url = "https://193.105.74.159/sms/1/logs?from=$from&to=$to&bulkId=$bulkId&messageId=$messageId";
        // $url = "https://193.105.74.159/sms/1/logs?to=$to&bulkId=$bulkId&messageId=$messageId";
        $url =  $this->base_url . "/sms/1/logs?to=$to&bulkId=$bulkId&messageId=$messageId&limit=1";
        print "<br/>\n url : $url";

        // if ($limit != null) {
        //     $url = "$url?limit=$limit";
        // }
        // print "<br/>\n url: $url"; 




        $curl = curl_init();

        curl_setopt_array($curl, array(
            //   CURLOPT_URL => "http://193.105.74.159/sms/1/logs",
            CURLOPT_URL => "$url",
            //CURLOPT_URL => "https://193.105.74.159/sms/1/logs?to=$to&bulkId=$bulkId&messageId=$messageId&limit=1",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass") . "",
            ),
        ));



        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        print "<br/>\n ------------ RETOUR ---------------";


        if ($err) {
            echo "cURL Error #:" . $err;
            return $err;
        } else {
            echo '<pre>';
            // var_dump($response);
            print("<br/>\n");
            $tab = json_decode($response, true);
            print_r($tab);
            $obj = json_decode($response);
            print_r($obj);


            if ($obj) {
                if (array_key_exists('results', $tab)) {
                    if ($obj->results) {

                        $dbh = $this->my_pdo->getConn();


                        $results = $obj->results;
                        foreach ($results as $result) {

                            $messageId = $result->messageId;
                            $to = $result->to;
                            $from = $result->from;
                            $sentAt = $result->sentAt;
                            $doneAt = $result->doneAt;
                            $smsCount = $result->smsCount;
                            $mccMnc = $result->mccMnc;
                            $price = $result->price;
                            $pricePerMessage = $price->pricePerMessage;
                            $currency = $price->currency;

                            $status = $result->status;
                            $status_groupId = $status->groupId;
                            $status_groupName = $status->groupName;
                            $status_id = $status->id;
                            $status_name = $status->name;
                            $status_description = $status->description;

                            $error = $result->error;
                            $error_groupId = $error->groupId;
                            $error_groupName = $error->groupName;
                            $error_id = $error->id;
                            $error_name = $error->name;
                            $error_description = $error->description;
                            $error_permanent = $error->permanent;

                            ### faire la mise à jour
                            $sentAt =  new DateTime($sentAt, new DateTimeZone(('UTC')));
                            $sentAt_str = $sentAt->format('Y-m-d H:i:s');

                            $doneAt =  new DateTime($doneAt, new DateTimeZone(('UTC')));
                            $doneAt_str = $doneAt->format('Y-m-d H:i:s');



                            $data = [
                                'report_pricepermessage' => $pricePerMessage,
                                'report_status_description' => $status_description,
                                'report_error_groupid' => $error_groupId,
                                'report_error_groupname' => $error_groupName,
                                'report_error_id' => $error_id,
                                'report_error_name' => $error_name,
                                'report_error_description' => $error_description,
                                'report_error_permanent' => $error_permanent,
                                'report_status_id' => $status_id,
                                'report_status_groupname' => $status_groupName,
                                'report_status_groupid' => $status_groupId,
                                'report_currency' => $currency,
                                'report_mccmnc' => $mccMnc,
                                'sms_count' => $smsCount,
                                'report_doneat' => $doneAt_str,
                                'results_logs' => true,
                                'report_sentat' => $sentAt_str,
                                'message_id' => $messageId,
                                'report_status_name' => $status_name
                            ];


                            $colonnes = "
                            report_pricepermessage = :report_pricepermessage, 
                            report_status_description = :report_status_description,
                            report_error_groupid =  :report_error_groupid, 
                            report_error_groupname = :report_error_groupname, 
                            report_error_id = :report_error_id,
                            report_error_name = :report_error_name,
                            report_error_description = :report_error_description, 
                            report_error_permanent = :report_error_permanent ,
                            report_status_id = :report_status_id, 
                            report_status_groupname = :report_status_groupname, 
                            report_status_groupid = :report_status_groupid, 
                            report_currency = :report_currency,
                            report_mccmnc = :report_mccmnc, 
                            sms_count = :sms_count,
                            report_doneat = :report_doneat,
                            report_sentat = :report_sentat, 
                            results_logs = :results_logs, 
                            report_status_name = :report_status_name";



                            //print " < br / > \n ----- getSmsLogs ----------- < pre > ";
                            //print_r($data);

                            //$res = $this->my_pdo->__update('outgoing', $data, " message_id = '$messageId' ");

                            // $sql =  "update outgoing set $colonnes where message_id = :message_id";
                            $sql =  "update outgoing set $colonnes where message_id = :message_id ";
                            // var_dump($sql);
                            //print  "<br/>\n compte => " . $abonnement['compte'];
                            $stmt = $dbh->prepare($sql);
                            try {
                                $res_update = $stmt->execute($data);
                                var_dump($res_update);
                                $count = $stmt->rowCount();

                                if ($count == '0') {
                                    echo "Failed !";
                                } else {
                                    echo "Success !";
                                }
                                var_dump($count);
                            } catch (\Throwable $th) {
                                $this->returnError(null, $th->getFile() . ' ' . $th->getLine() . ' ' . $th->getMessage());
                            }



                            // var_dump($res);
                        } //for

                    } // if ($obj->results) {
                } //-----------if (array_key_exists('results', $tab)) {
            } //obj



            return $response;
        } //$err


    } //getSmsLogsV2



    public function getAccountBalance()
    {

        $url =  $this->base_url . '/account/1/balance';
        //$url = 'http://193.105.74.159/account/1/balance';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: Basic " . base64_encode("$this->user:$this->pass") . "",
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            var_dump($err);
        } else {
            var_dump($response);
            $response_tab = json_decode($response, true);
            print_r($response_tab);
            return $response_tab['balance'];
        }



        // $request = new HttpRequest();
        // $request->setUrl($url);
        // $request->setMethod(HTTP_METH_GET);

        // $request->setHeaders(array(
        //     'accept' => 'application/json',
        //     'authorization' => 'Basic ' . base64_encode("$this->user:$this->pass")
        // ));

        // //"authorization: Basic " . base64_encode("$this->user:$this->pass") . "",

        // try {
        //     $response = $request->send();

        //     echo $response->getBody();
        // } catch (HttpException $ex) {
        //     echo $ex;
        // }


    } //getAccountBalance











    /**
     * generer code aleatoire
     * @param string $car  longueur du mot de passe
     * @return string $password retourne le mot passe 
     */
    function genererPassword($car)
    {

        $string = "";
        $chaine = strtoupper("abcdefghijklmnpqrstuvwxy123456789");
        srand((float) microtime() * 1000000);
        for ($i = 0; $i < $car; $i++) {
            $string .= $chaine[rand() % strlen($chaine)];
        } //for
        return $string;
    } //genererPassword


    function getSalt()
    {
        return md5(time());
    } //getSalt

    /**
     * crypter le mot de passe
     * @param string $password  plain password
     * @param string $salt  pour saler le password
     * @return string $password retourne le mot passe crypté
     */
    function crypterPassword($password, $salt = null)
    {
        //get encoder
        $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);

        //crypter password 
        return $password = $encoder->encodePassword(strtoupper($password), ($salt == null) ? md5(time()) : $salt);
    } //crypterPassword


    //retourner le message d'erreur
    function returnError($to, $message)
    {
        if ($to == null) $to = $this->my_tel;
        $this->sendSMS($to, $message);
        sleep(60);
        exit(0);
    } //


    //retourner le message d'erreur
    function returnError2($to, $message)
    {
        if ($to == null) $to = $this->getMyTel();
        $this->sendSMS($to, $message);
        sleep(60);
        //exit(0);
    } //



    /**
     * retourner json
     *  retourErreur = 1 ou 0 . 1 si presence d'erreur sinon 0
     *  retourMessage = message d erreur
     * retourTab =  données a retourner
     * fichier = le fichier ou ercrire la log
     * content = le contenu du fichier 
     */
    function retour_json($retourErreur, $retourMessage, $retourTab, $fichier = null, $content = null)
    {
        header('Content-type: application/json');
        // echo $message;
        // var_dump($retour);
        echo $retour = json_encode([
            'erreur' => $retourErreur,
            'message' => $retourMessage,
            'retour' => $retourTab
        ]);


        //--------------------------------------------------------------
        //$content = ob_get_contents();

        // $myfile = file_put_contents('/var/www/html/test/webservice/rest/log/sendsms.log', $content . PHP_EOL, FILE_APPEND | LOCK_EX);
        if ($fichier != null and $content != null)
            file_put_contents($fichier, $content . PHP_EOL, FILE_APPEND | LOCK_EX);

        exit(0);
    } //retour_json



    public function replaceSpecialChar($name)
    {

        $name = $this->supprimerRetourChariot($name);


        //$name = strtr($name, 'Ã€Ã?Ã‚ÃƒÃ„Ã…Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃ?ÃŽÃ?Ã’Ã“Ã”Ã•Ã–Ã™ÃšÃ›ÃœÃ?Ã Ã¡Ã¢Ã£Ã¤Ã¥Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã°Ã²Ã³Ã´ÃµÃ¶Ã¹ÃºÃ»Ã¼Ã½Ã¿', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
        //$name = preg_replace('/([^.a-z0-9]+)/i', ' ', $name);

        //https://fr.wikipedia.org/wiki/Aide:Liste_de_caract%C3%A8res_sp%C3%A9ciaux
        $trans = array(
            "à" => "a", "â" => "a", "ç" => "c", "è" => "e", "é" => "e", "ê" => "e", "ô" => "o", "ù" => "u", "û" => "u",
            "À" => "A", "Â" => "A", "Ã" => "A", "Ç" => "C", "È" => "E", "É" => "E", "Ê" => "E", "Ô" => "O", "Ù" => "U", "Û" => "U"
        );  
        $name = strtr($name, $trans);

        // $name = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);


        return $name;
    } //replaceSpecialChar
    


    public function supprimerRetourChariot($name)
    {

        # supprimer les retour chariots, tabulation ,etc
        $name = str_replace("\n", "", $name);
        $name = str_replace("\r", "", $name);
        $name = str_replace("\t", "", $name);


        return $name;
    } //replaceRetourChariot



}//Fonction
