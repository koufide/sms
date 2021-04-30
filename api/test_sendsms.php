<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "\n\n\n ========== EXECUTION TEST SMS ====== \n" . $now->format('d/m/Y H:i:s') . "\n";



define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin = API_REP;

require_once($chemin . '/Fonction.php');
$fct = new Fonction();



//------------------------------------------------------------------------
//------------------------------------------------------------------------

// $compte = '11042050008';
// // // 11042050008 ==> 
// // // 1104*****08
// // print "<br/>\n compte: $compte";
// // print "<br/>\n compte: $compte";
// // $tab_compte = str_split($compte);
// // var_dump($tab_compte);
// // $compte_hash = $tab_compte[0] . $tab_compte[1] . $tab_compte[2] . $tab_compte[3] . '*****' . $tab_compte[9] . $tab_compte[10];
// // print "<br/>\n compte_hash: $compte_hash";

// print "<br/>\n compte_hash:" . $fct->getCompteHash(($compte));
// exit(0);


//------------------------------------------------------------------------
//------------------------------------------------------------------------


$message = uniqid();


/*
//------------------------------------------------------------------------
//------------------------------------------------------------------------

$to = '22509327626';


$res_send = $fct->sendSMS($to, $message, true);
print "<br/>\n res_send: $res_send quitter";
exit(0);



$fct->getScheduledSMS();


exit(0);
//------------------------------------------------------------------------
//------------------------------------------------------------------------

$to = '22509327626';
$message = uniqid();

$res_send = $fct->sendSMS($to, $message, true);
print "<br/>\n res_send: $res_send quitter";
exit(0);
//-------------




print "<br/>\n quitter";
exit(0);


$tab_messages = [];


$destinations = [
    "to" => $to,
    "messageId" => $fct->getMessageId()
];


$tab_destinations = [];

$tab_destinations['from'] = $fct->getFrom();
$tab_destinations['destinations'] = $destinations;
$tab_destinations['text'] = $message;
$tab_destinations['flash'] = $fct->getFlash();
$tab_destinations['language'] = ["languageCode" => $fct->getLanguageCode()];
$tab_destinations['transliteration'] = $fct->getTransliteration();
$tab_destinations['sendAt'] = "2020-03-19T20:15:00.000+00:00";

$tmp_destinations[] = $tab_destinations;


$tab_messages["bulkId"] = $fct->getBulkId();
$tab_messages["messages"] = $tmp_destinations;




if (!empty($tab_messages)) {
    $applic = 'CGB';
    $res = $fct->sendMultiSMStoMultiDestScheduleInfo($tab_messages, $applic);
}

print "<br/>\n quitter";
exit(0);



*/


//------------------------------------------------------------------------
//------------------------------------------------------------------------
// $tab_messages = [
//     "bulkId" => $fct->getBulkId(),
//     "messages" => [
//         [
//             "from" => $fct->getFrom(),
//             "destinations" => [
//                 [
//                     "to" => "22509327626",
//                     "messageId" => $fct->getMessageId()
//                 ],
//                 // [
//                 //     "to" => "22503612783",
//                 //     "messageId" => $fct->getMessageId()
//                 // ],
//             ],
//             "text" => "$message",
//             "notifyUrl" => "http://192.168.3.100/sms/api/notifyurl.php",
//             "notifyContentType" => "application/json",
//             // "callbackData" => "There's no place like home."
//         ],
//         [
//             "from" => $fct->getFrom(),
//             "destinations" => [
//                 // [
//                 //     "to" => "22509327626",
//                 //     "messageId" => $fct->getMessageId()
//                 // ],
//                 [
//                     "to" => "22503612783",
//                     "messageId" => $fct->getMessageId()
//                 ],
//             ],
//             "text" => "$message",
//             "notifyUrl" => "http://192.168.3.100/sms/api/notifyurl.php",
//             "notifyContentType" => "application/json",
//             // "callbackData" => "There's no place like home."
//         ]
//     ]

// ];


$tab_messages = [
    "bulkId" => $fct->getBulkId(),
    "messages" => [
        [
            "from" => $fct->getFrom(),
            "destinations" => [
                [
                    "to" => "22503612783",
                    "messageId" => $fct->getMessageId()
                ],

            ],
            "text" => "$message",
        ],

    ]

];



//$url =  $this->base_url . '/sms/2/text/advanced';
$url =  "https://g3lq8.api.infobip.com/sms/2/text/advanced";




print "REQUEST <br/>\n <pre>";
print_r($tab_messages);
print "<br/>\n";
$res = $fct->sendMultiSMStoMultiDestV5_test($tab_messages, $applic = 'CGB', $url);
if ($res) {
    $tab_messages = json_decode($res, true);
    print "RESPONSE <br/>\n <pre>";
    print_r($tab_messages);
    print "<br/>\n";
}


$out1 = ob_get_contents();
file_put_contents('/var/www/html/sms/api/log/tes_api.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
