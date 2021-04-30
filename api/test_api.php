<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

ob_start();

$now = new DateTime('NOW', new DateTimeZone(('UTC')));
print "\n\n\n ========== EXECUTION TEST API ====== \n" . $now->format('d/m/Y H:i:s') . "\n";



define("API_REP",     '/var/www/html/sms/api');

chdir(API_REP);

$chemin = API_REP;
// $chemin_params = 'params.xml';

// if (!file_exists($chemin_params)) {
//     exit("\n chemin : $chemin_params introuvable");
// }

// require_once($chemin . '/MyPDO.php');

// $conn = new MyPDO();
// $dbh = $conn->getConn();


require_once($chemin . '/Fonction.php');
$fct = new Fonction();



//------------------------------------------------------------------------
//------------------------------------------------------------------------

$to = '22509327626';
$message = uniqid();

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

//$fct->sendSmsWithNotifyUrl($to, $message);
// $fct->testPhone($to);

//------------------------------------------------------------------------
//------------------------------------------------------------------------
//        //BBG-BULK-ID-5d4c23d3b85ce
//BBG-MSG-ID-5d4c23d3b859a


//$bulkId = 'BBG-BULK-ID-5d4c23d3b85ce';
//$fct->getMessageScheduleInfo($bulkId);



//print "<br/>\n quitter";
//exit(0);
//------------------------------------------------------------------------
//------------------------------------------------------------------------



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


// $messages = '{
// "messages":[
//     {
//         "from":"41793026700",
//         "destinations":[
//             {
//             "to":"41793026785"
//             }
//         ],
//         "text":"A long time ago, in a galaxy far, far away... It is a period of civil war. Rebel spaceships, striking from a hidden base, have won their first victory against the evil Galactic Empire.",
//         "sendAt":"2015-07-07T17:00:00.000+01:00"
//     }
// ]
// }';


if (!empty($tab_messages)) {
    $applic = 'CGB';
    $res = $fct->sendMultiSMStoMultiDestScheduleInfo($tab_messages, $applic);
}

print "<br/>\n quitter";
exit(0);






//------------------------------------------------------------------------
//------------------------------------------------------------------------
$tab_messages = [
    "bulkId" => $fct->getBulkId(),
    "messages" => [
        [
            "from" => $fct->getFrom(),
            "destinations" => [
                [
                    "to" => "22509327626",
                    "messageId" => $fct->getMessageId()
                ],
                // [
                //     "to" => "22503612783",
                //     "messageId" => $fct->getMessageId()
                // ],
            ],
            "text" => "$message",
            "notifyUrl" => "http://192.168.3.100/sms/api/notifyurl.php",
            "notifyContentType" => "application/json",
            // "callbackData" => "There's no place like home."
        ],
        [
            "from" => $fct->getFrom(),
            "destinations" => [
                // [
                //     "to" => "22509327626",
                //     "messageId" => $fct->getMessageId()
                // ],
                [
                    "to" => "22503612783",
                    "messageId" => $fct->getMessageId()
                ],
            ],
            "text" => "$message",
            "notifyUrl" => "http://192.168.3.100/sms/api/notifyurl.php",
            "notifyContentType" => "application/json",
            // "callbackData" => "There's no place like home."
        ]
    ]

];

print "REQUEST <br/>\n <pre>";
print_r($tab_messages);
print "<br/>\n";
$res = $fct->sendMultiSMStoMultiDestV5($tab_messages, $applic = 'CGB');
if ($res) {
    $tab_messages = json_decode($res, true);
    print "RESPONSE <br/>\n <pre>";
    print_r($tab_messages);
    print "<br/>\n";
}


$out1 = ob_get_contents();
file_put_contents('/var/www/html/sms/api/log/tes_api.log', $out1 . PHP_EOL, FILE_APPEND | LOCK_EX);
