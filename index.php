<?php

//composer require google/apiclient:"^2.0"
//composer require asimlqt/php-google-spreadsheet-client:"3.0.*"

date_default_timezone_set("Asia/Bangkok");
header("content-type:text/javascript;charset=utf-8");  
include "crud.php";
include "linemessage.php";
include "function.php";
include "dialogflow.php"; 

//Logs
// $headers = getallheaders(); // ได้ค่า array ของส่วน Headers ทั้งหมดที่ Line ส่งมา
// file_put_contents('headers.txt',json_encode($headers, JSON_PRETTY_PRINT)); 
// file_put_contents('body.txt',file_get_contents('php://input')); // ส่วนของ body

if(count($deCode) == 2){
    //ดึงข้อมูลครั้งแรกเมื่อมีข้อความเข้ามาจาก Line
    $messageType = $deCode['events'][0]['message']['type'];
    $type = $deCode['events'][0]['type'];
    $userId = $deCode['events'][0]['source']['userId'];   
}
else if(count($deCode) == 4){
    //ดึงข้อมูลเมื่อ DialogFlow ส่งกลับมา
    $messageType = $deCode["originalDetectIntentRequest"]["payload"]['data']["message"]["type"];
    $queryText = $deCode["queryResult"]["queryText"];
    $action = $deCode["queryResult"]["action"];
    $userId = $deCode['originalDetectIntentRequest']['payload']['data']['source']['userId'];
}

//กำหนด Path เก็บภาพที่ Server
$datas = file_get_contents('php://input');
/*Decode Json From LINE Data Body*/
$deCode = json_decode($datas,true);
file_put_contents('chat-log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

$token = "UjEiVgTUwEsD4D5FHSGLwY6+zVOR/bYumrBuvmFcgaPATMb/Lo4AmztbT95ygiPIt1/K9lHhm03EGlAnOR4HQ0yv0ltvcjP/c6HkejOwl+46KtzIfJI11BbpVoiWbjuVKqKTkdbomzF9SR2Zj7YM3gdB04t89/1O/w1cDnyilFU=";

$LINEDatas['token'] = $token;

//ตรวจสอบประเภทของข้อมูลที่ถูกส่งมาจาก Line
if($messageType == 'text'){
    switch ($action) {
    case "OtherTypeConfirm":  
        $wait = "ระบบกำลังส่งอีเมลไปยังผู้ที่เกี่ยวข้องเรียบร้อยแล้ว เจ้าหน้าที่จะแจ้งข้อมูลผ่านทางอีเมลเท่านั้น ขอบคุณครับ";
        sendAlert($wait, $token, $userId);       
        break;    
    default:
        //กรณีที่ไม่มีข้อความตรงกับ Server จะถูกส่งข้อมูล JSON ไปที่ DialogFlow
        callDialogFlow();
    }

} else {
    //กรณีที่ไม่มีข้อความตรงกับ Server จะถูกส่งข้อมูล JSON ไปที่ DialogFlow
    callDialogFlow();
}