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
  
    require_once 'vendor/autoload.php';

    $htmlbody = 'แสดงเนื้อหาเมลเป็นแบบ <b>html</b>';
    
    $Mailer = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        $Mailer->SMTPDebug = 4;
        // smtp authentication data.
        $Mailer->isSMTP();
        $Mailer->SMTPSecure = 'tls';
        $Mailer->Port = 587;
        $Mailer->SMTPAuth = true;
        $Mailer->Host = 'smtp.gmail.com';
        $Mailer->Username = 'learningspace.ipst@gmail.com';
        $Mailer->Password = 'ipst1234';
        // sender and receipient.
        $Mailer->setFrom('cartoon_toon29@hotmail.com');
        $Mailer->addAddress('cartoon_toon29@hotmail.com');
        // $Mailer->addAddress('name2@domain.tld');
        // subject and content.
        $Mailer->isHTML(true);
        $Mailer->CharSet = 'utf-8';
        $Mailer->Subject = 'หัวข้ออีเมลภาษาใดๆก็อ่านได้';
        $Mailer->Body = $htmlbody;
        $Mailer->AltBody = 'ข้อความสำหรับผู้รับที่อ่านได้แต่อีเมลแบบ text อย่างเดียว';
        // send it.
        $Mailer->send();
        echo 'Message has been sent.';
    } catch (\Exception $e) {
        echo 'Message could not be sent. Mailer Error: ', $Mailer->ErrorInfo;
    }
}

?>