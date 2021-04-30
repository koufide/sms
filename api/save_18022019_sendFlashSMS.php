<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();


$user = 'Bridge-bank';
$pass = 'Bridge2018';


// $res = array(
// 			 "to"=>  ['22503612783'],
// 			 "text"=>"FLASH",
// 			 "from"=>"BRIDGE BANK",
// 			 "flash"=>true
// 			 );

//        $json = json_encode($res);
// echo "<pre>";
// print_r($json);


$json = '{
  "messages":[
    {
      "from":"BRIDGE BANK",
      "destinations":[
        {
          "to":"22503612783"
        }
      ],
      "text":"FLASH TEST",
      "flash":true
    }
  ]
}';

/*
$json = '{
  "messages":[
    {
      "from":"BRIDGE BANK",
      "destinations":[
        {
          "to":"22503612783"
        }
      ],
      "text":"FLASH TEST",
      "flash":true
    },
    {
      "from":"BRIDGE BANK",
      "destinations":[
        {
          "to":"22575201581"
        }
      ],
      "text":"FLASH TEST",
      "flash":true
    },
    {
      "from":"BRIDGE BANK",
      "destinations":[
        {
          "to":"22557567344"
        }
      ],
      "text":"FLASH TEST",
      "flash":true
    },
    {
      "from":"BRIDGE BANK",
      "destinations":[
        {
          "to":"22557790421"
        }
      ],
      "text":"FLASH TEST",
      "flash":true
    },
    {
      "from":"BRIDGE BANK",
      "destinations":[
        {
          "to":"22507204869"
        }
      ],
      "text":"FLASH TEST",
      "flash":true
    }
  ]
}';
 */


//  $json = ' {  
//   "from":"BRIDGE BANK",
//   "to":[  
//     "22503612783","22575201581","22557567344","22557790421","22507204869","22505053732"
//   ],
//   "text":"FLASH TEST",
//   "flash":true
// }	';




echo "<pre>";
var_dump($json);

$res = json_decode($json, true);
var_dump($res);

//  exit("<br/>\n-----------quitter--------------");


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://193.105.74.159/sms/1/text/advanced",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "$json",
  CURLOPT_HTTPHEADER => array(
    "accept: application/json",
    "authorization: Basic " . base64_encode("$user:$pass") . "",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
  echo '<pre>';
  var_dump($response);
}
