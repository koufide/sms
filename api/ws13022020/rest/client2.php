<?php

$user = 'powercard';
// $pass = '1P3QM4H4';
$pass = 'BQCPDWX3';
// var_dump(base64_encode("$user:$pass"));




$messageId = $_GET['messageId'];

//BBG-MSG-MON-5d24f0b202f59
//BBG-MSG-MON-5d24f16e9cdda

$res = array(
    "messageId" => $messageId,
);


$json = json_encode($res);

// $url = 'http://192.168.200.22/test/webservice/rest/sendsms.php';
// $url = 'https://192.168.200.42:8243/pushsms/v1.0.0/sendSms_notification';



// $url = 'http://192.168.200.42:8280/pushsms/v1.0.0/sendSms_notification';
$url = 'http://192.168.200.42:8280/pushsms/delivery/v1.0.0/getDelivery_report';

// $url = 'http://192.168.56.220/test/webservice/rest/getdelivreryreport.php';



$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => "$url",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    // CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
    CURLOPT_POSTFIELDS => "$json",
    CURLOPT_HTTPHEADER => array(
        "accept: application/json",
        "Authorization: Bearer b45f3329-0d6c-3d17-903f-4049726fc077",
        "Authorization: Basic " . base64_encode("$user:$pass"),
        "content-type: application/json"
    ),
));


// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

// var_dump($response);
// echo ($response);
// var_dump($err);

$retour = json_decode($response, true);
print "<pre>";
print_r($retour);
var_dump($err);
