<?php

namespace App\Service;

class MyFonction
{
    private $user;
    private $pass;
    private $pass_encode;
    private $indice;
    private $from;
    private $my_pdo;

    public function __construct()
    {
        $this->user = 'Bridge-bank';
        $this->pass = 'Bridge2018';
        $this->pass_encode = base64_encode("$this->user:$this->pass");
        //var_dump($this->pass_encode);
        $this->indice = '225';
        $this->from = 'TEST';

        //$this->my_pdo = new MyPDO();
    } //construct


    public function getTel($tel)
    {
        // print("<br/>\n------------getTel(tel)");
        // print("<br/>\n tel: $tel");

        $tel = trim($tel);
        $tel = str_replace(' ', '', $tel);
        $tel = substr($tel, -8); //les 8 derniers caractères

        // print("<br/>\n tel: $tel");

        $n = strlen(($tel));
        //if ($n == 8) {
        $tel = $this->indice . $tel;
        //}
        // print("<br/>\n getTel----tel: $tel");
        return $tel;
    } //getTel



    public function sendSMS($to, $message = null)
    {
        $url = ' http ://g3lq8.api.infobip.com/sms/2/text/single';

        //$message = uniqid();

        if ($message == null) {
            $message = "VOTRE SOUSCRIPTION AU SERVICE BRIDGE-SMS A ETE EFFECTUEE AVEC SUCCES";
        }

        $to = $this->indice . $to;
        $text = $message;
        $from = $this->from;

        $res = array(
            "from" => $from,
            "to" => "$to",
            "text" => "$text"
        );


        // var_dump($res);
        $json = json_encode($res);
        // var_dump($json);



        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
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
            // var_dump($err);
            // return $err;
            $retour = $err;
        } else {
            // echo '<pre>';
            // var_dump($response);
            // return $response;
            $retour = $response;
            $response = json_decode($response);
        }

        // print "RETOUR / <pre>";
        // print_r($retour);

        $now = new DateTime('NOW', new DateTimeZone(('UTC')));


        $data = [
            $this->getFrom(),
            $to,
            $message,
            $retour,
            (($err) ? null : $response->messages[0]->messageId),
            ($now->format('Y-m-d H:i:s'))
        ];

        // var_dump($now->format('Y-m-d H:i:s'));

        #$sth = $this->my_pdo->getConn()->prepare('INSERT INTO outgoing (de, a, text, status_sendsms, message_id, sendsms_at)  VALUES (?,?,?,?,?,?)');
        #$sth->execute($data);


        //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails

        return $retour;
    } //sendSMS
}
