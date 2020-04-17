<?php

//ตารางกิจกรรม - เรียกผ่าน DialogFlow

//กรณีที่ต้องการให้ยืนยันข้อมูล
function sendSchedule($token, $userId){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.line.me/v2/bot/message/push",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{
            \"to\": \"$userId\",
            \"messages\": [{
                \"type\": \"image\",
                \"originalContentUrl\": \"https://sv1.picz.in.th/images/2019/09/26/cKoDSb.jpg\",
                \"previewImageUrl\": \"https://sv1.picz.in.th/images/2019/09/26/cKoDSb.jpg\"
            }]
        }",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer \"$token\"",
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
}

//กรณีที่ต้องการให้แสดงข้อความแจ้งเตือน
function sendAlert($msg, $token, $userId){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.line.me/v2/bot/message/push",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{
            \"to\": \"$userId\",
            \"messages\": [{
                \"type\": \"text\",
                \"text\": \"$msg\"
            }]
        }",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer \"$token\"",
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
}

//ส่งรายละเอียดการโอนเงิน
function sendAccountTransfer($token, $userId){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.line.me/v2/bot/message/push",
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{
            \"to\": \"$userId\",
            \"messages\": [{
                \"type\": \"flex\",
            \"altText\": \"Flex Message\",
            \"contents\": {
                \"type\": \"bubble\",
                \"body\": {
                \"type\": \"box\",
                \"layout\": \"vertical\",
                \"spacing\": \"md\",
                \"contents\": [
                    {
                    \"type\": \"text\",
                    \"text\": \"การชำระเงิน\",
                    \"size\": \"xl\",
                    \"align\": \"center\",
                    \"gravity\": \"center\",
                    \"weight\": \"bold\",
                    \"wrap\": true
                    },
                    {
                    \"type\": \"box\",
                    \"layout\": \"vertical\",
                    \"spacing\": \"sm\",
                    \"margin\": \"lg\",
                    \"contents\": [
                        {
                        \"type\": \"box\",
                        \"layout\": \"baseline\",
                        \"spacing\": \"sm\",
                        \"contents\": [
                            {
                            \"type\": \"text\",
                            \"text\": \"Peak Event Organizer\",
                            \"flex\": 4,
                            \"size\": \"sm\",
                            \"align\": \"center\",
                            \"color\": \"#666666\",
                            \"wrap\": true
                            }
                        ]
                        },
                        {
                        \"type\": \"box\",
                        \"layout\": \"baseline\",
                        \"spacing\": \"sm\",
                        \"contents\": [
                            {
                            \"type\": \"text\",
                            \"text\": \"ไทยพาณิชย์ 414-044856-0\",
                            \"flex\": 4,
                            \"size\": \"sm\",
                            \"align\": \"center\",
                            \"gravity\": \"center\",
                            \"color\": \"#666666\",
                            \"wrap\": true
                            }
                        ]
                        }
                    ]
                    }
                ]
                },
                \"footer\": {
                \"type\": \"box\",
                \"layout\": \"horizontal\",
                \"flex\": 1,
                \"contents\": [
                    {
                    \"type\": \"box\",
                    \"layout\": \"vertical\",
                    \"contents\": [
                        {
                        \"type\": \"text\",
                        \"text\": \"หากโอนเงินเรียบร้อยแล้ว กรุณาเลือกเมนู แจ้งโอนเงิน\",
                        \"size\": \"md\",
                        \"align\": \"center\",
                        \"wrap\": true
                        }
                    ]
                    }
                ]
                }
            }
            }]
        }",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer \"$token\"",
            "cache-control: no-cache",
            "content-type: application/json",
        ),
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
}

?>