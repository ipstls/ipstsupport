<?php

function callDialogFlow(){
    $url = "https://bots.dialogflow.com/line/842b28ac-b26d-411d-935c-55b7e3855b45/webhook";
    $headers = getallheaders();
    $headers['host'] = "bots.dialogflow.com";
    $json_headers = array();
    foreach($headers as $k=>$v){
        $json_headers[]=$k.":".$v;
    }
    file_put_contents('test.txt',json_encode($json_headers, JSON_PRETTY_PRINT)); 
    $inputJSON = file_get_contents('php://input');
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url);
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $inputJSON);
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $json_headers);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2); // 0 | 2 ถ้าเว็บเรามี ssl สามารถเปลี่ยนเป้น 2
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 1); // 0 | 1 ถ้าเว็บเรามี ssl สามารถเปลี่ยนเป้น 1
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    $result = curl_exec( $ch );
    $err = curl_error($ch);
    curl_close( $ch );
}

?>