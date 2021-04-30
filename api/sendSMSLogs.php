<?php
error_reporting(E_ALL);
ini_set('display_errors','On');


getDelivreryReports();


function getDelivreryReports(){
	
	$user='Bridge-bank';
	$pass='Bridge2018';
	
	
$curl = curl_init();

curl_setopt_array($curl, array(
//  CURLOPT_URL => "http://g3lq8.api.infobip.com/sms/1/reports",
  CURLOPT_URL => "http://193.105.74.159/sms/1/logs",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "authorization: Basic ".base64_encode("$user:$pass")."",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
	echo "<pre>";
	//var_dump($response);
  //echo $response;
  
  //var_dump(json_decode($response));
  
  //var_dump(json_decode($response, true));
  //var_dump($response);
  
  $json = json_decode($response, true);
  foreach($json as $results){
	foreach($results as $r){
		var_dump($r);
	}
  }


  
  
}

}
