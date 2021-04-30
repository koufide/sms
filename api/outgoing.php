<?php
error_reporting(E_ALL);
ini_set('display_errors','On');

$csv_rep='/var/www/html/sms/public/outgoing';
$arch_outgoing='/var/www/html/sms/public/arch_outgoing';


if(!is_dir($csv_rep)){
	  mkdir($csv_rep);
}

if(!is_dir($arch_outgoing)){
	  mkdir($arch_outgoing);
}

$messages = array();
$tab_messages = array();
$final_messages = array();

if($dossier = opendir($csv_rep))
{
    while(false !== ($fichier = readdir($dossier)))
    {
        if($fichier != '.' && $fichier != '..' && $fichier != 'index.php')
        {
            var_dump($fichier);
            $pathinfo = pathinfo($fichier);
            var_dump($pathinfo);
            $ext = $pathinfo['extension'];
            $basename = $pathinfo['basename'];
            $filename = $pathinfo['filename'];
            
            
            if(  preg_match("/^PUSH/i",$filename ) ){
                print"<br/>\n echo trouve ==> $filename";
                
                
                
                 $handle = fopen("$csv_rep/$fichier", "r");
                    if ($handle) {
                        while (($line = fgets($handle)) !== false) {
                            // process the line read.
                            var_dump("fichier $fichier line : ".$line);
                            
                            
                            
                            $tab_line =  explode('#',$line);
                            var_dump($tab_line);
                            $code=$tab_line[0];
                            $compte=$tab_line[1];
                            $tel=$tab_line[2];
                            $message=$tab_line[3];
                            
                            ###sendSMS($tel,$message);
                            
                            
                            
                            //$res = array(
                            //    "from"=>"BRIDGE BANK",
                            //    "to"=>  $tel,
                            //    "text"=>"$message",
                            //);
                            
                             $tab_messages['from'] = "BRIDGE BANK"  ;
                             $tab_messages['to'] = "$tel"  ;
                             $tab_messages['text'] = "$message"  ;
                            
                                                        
                            $messages[]=$tab_messages;
                            
                           
                            
                            
                        }
                    
                        fclose($handle);
                    } else {
                        // error opening the file.
                    }
                
                 ///$messages[]=$tab_messages;
                 
            }else{
                //print"<br/>\n echo non trouve";
            }
            
            
            
            //if($ext == $csv_ext){
                //$filename=str_replace(' ','',$filename);
                //$filename=str_replace('\'','',$filename);
                
                //rename($csv_rep.$fichier, $csv_rep.$filename.'.'.$ext);
                
                //$res = creerTable($dbh,$schema,$table=$filename);
                
                //$res = nbreLigneTable($dbh, $table=$filename);
                
                //if($res != 0)
                //$res = viderTable($dbh, $table);
                
                //$res = nbreLigneTable($dbh, $table=$filename);
                
                //chargerDonneesTable($dbh, $table , $csv_rep.$filename.'.'.$ext);
                
            //}
            
             #deplacer lz fichier
            rename($csv_rep.'/'.$fichier, $arch_outgoing.'/arch_'.$filename.'.'.$ext);
        
        }
    
    }
    closedir($dossier);
}else{
    echo 'Le dossier n\' a pas pu être ouvert';
}

 
$final_messages['messages']=$messages;
###sendSMSMultiple($messages);
//sendSMSMultiple($tab_messages);
sendSMSMultiple( (array)  $final_messages);



function sendSMSMultiple( $final_messages){
    
    print"<pre>";
    print_r($final_messages);
    $res_tab = json_encode($final_messages);
    print_r($res_tab);
    

    var_dump($res_tab);
    $json=$res_tab;
//exit("<br/>\n--------quitter-----------");
    
    

    
    $user='Bridge-bank';
    $pass='Bridge2018';

    //var_dump(base64_encode("$user:$pass"));


    $curl = curl_init();

    curl_setopt_array($curl, array(
//      CURLOPT_URL => "http://g3lq8.api.infobip.com/sms/2/text/single",
//      CURLOPT_URL => "https://g3lq8.api.infobip.com/sms/2/text/single",
      //CURLOPT_URL => "https://g3lq8.api.infobip.com/sms/1/text/multi",
//      CURLOPT_URL => "https://g3lq8.api.infobip.com/sms/1/text/multi",
      CURLOPT_URL => "http://193.105.74.159/sms/1/text/multi",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
    //  CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
      CURLOPT_POSTFIELDS => "$json",
      CURLOPT_HTTPHEADER => array(
        "accept: application/json",
    //    "authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==",
        "authorization: Basic ".base64_encode("$user:$pass")."",
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

    //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails
    
    //https://dev.infobip.com/send-sms/single-sms-message-v1
    
}//sendSMSMultiple





        
function sendSMS($tel,$message){
    
    $res = array(
    "to"=>  [$tel],
    "text"=>"$message",
    "flash"=>true,
    "from"=>"BRIDGE BANK"
    //,"sendAt"=>"2018-10-16T19:25:00.000+00:00"
    );
    
    $json = json_encode($res);
    var_dump($json);
    
    
    
    $user='Bridge-bank';
    $pass='Bridge2018';

    //var_dump(base64_encode("$user:$pass"));


    $curl = curl_init();

    curl_setopt_array($curl, array(
//      CURLOPT_URL => "http://g3lq8.api.infobip.com/sms/2/text/single",
      CURLOPT_URL => "http://193.105.74.159/sms/2/text/single",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
    //  CURLOPT_POSTFIELDS => "{ \"from\":\"InfoSMS\", \"to\":\"22503612783\", \"text\":\"Test SMS.\" }",
      CURLOPT_POSTFIELDS => "$json",
      CURLOPT_HTTPHEADER => array(
        "accept: application/json",
    //    "authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==",
        "authorization: Basic ".base64_encode("$user:$pass")."",
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

    //https://dev.infobip.com/send-sms/single-sms-message#section-smsresponsedetails
    //https://dev.infobip.com/send-sms/single-sms-message-v1
    
    
}//sendSMS
