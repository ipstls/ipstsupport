<?php

// บันทึกภาพใน Server PHP
function getContent($datas){
    $datasReturn = [];
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://api.line.me/v2/bot/message/".$datas['messageId']."/content",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => "",
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer ".$datas['token'],
        "cache-control: no-cache"
      ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    if($err){
      $datasReturn['result'] = 'E';
      $datasReturn['message'] = $err;
    }else{
      $datasReturn['result'] = 'S';
      $datasReturn['message'] = 'Success';
      $datasReturn['response'] = $response;
    }
    return $datasReturn;
}

// ส่งอีเมลไปยังปลายทาง
function sendEmail(){
  $emailto = 'cartoon_toon29@hotmail.com';
  $subject = '111';
  $header .= "Content-type: text/html; charset=utf-8\n";
  $header .= "from: 222 E-mail : 333";
  $messages.= "4444"; 
  $messages.= "จาก 555"; 
  $send_mail = mail($emailto,$subject,$messages,$header);
}

?>