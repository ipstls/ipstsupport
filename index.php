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

//กำหนด Path เก็บภาพที่ Server
$datas = file_get_contents('php://input');
/*Decode Json From LINE Data Body*/
$deCode = json_decode($datas,true);
file_put_contents('log.txt', file_get_contents('php://input') . PHP_EOL, FILE_APPEND);

$token = "UjEiVgTUwEsD4D5FHSGLwY6+zVOR/bYumrBuvmFcgaPATMb/Lo4AmztbT95ygiPIt1/K9lHhm03EGlAnOR4HQ0yv0ltvcjP/c6HkejOwl+46KtzIfJI11BbpVoiWbjuVKqKTkdbomzF9SR2Zj7YM3gdB04t89/1O/w1cDnyilFU=";

$LINEDatas['token'] = $token;

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

//ตรวจสอบประเภทของข้อมูลที่ถูกส่งมาจาก Line
if($messageType == 'text'){
    switch ($action) {
    //แจ้งสรุปและเพิ่มข้อมูลเข้าร่วมกิจกรรม
    case "confirmDetail": 
        //แจ้งเตือนให้รอระบบประมวลผล
        $wait = "กรุณารอสักครู่...";
        sendAlert($wait, $token, $userId);
        $food = $deCode["queryResult"]["outputContexts"][1]["parameters"]["showfood"];
        if($food == ""){
            $activities = $deCode["queryResult"]["outputContexts"][1]["parameters"]["showactivities"];
            $person = $deCode["queryResult"]["outputContexts"][1]["parameters"]["showperson"];
            $age = $deCode["queryResult"]["outputContexts"][1]["parameters"]["showage"];
            $height = $deCode["queryResult"]["outputContexts"][1]["parameters"]["showheight"];
            $weight = $deCode["queryResult"]["outputContexts"][1]["parameters"]["setweight"];
            $contact = $deCode["queryResult"]["outputContexts"][1]["parameters"]["showcontact"];
            $parent = $deCode["queryResult"]["outputContexts"][1]["parameters"]["setparent"];
            $food = "-";
        }
        else{
            $activities = $deCode["queryResult"]["outputContexts"][2]["parameters"]["showactivities"];
            $person = $deCode["queryResult"]["outputContexts"][2]["parameters"]["showperson"];
            $age = $deCode["queryResult"]["outputContexts"][2]["parameters"]["showage"];
            $height = $deCode["queryResult"]["outputContexts"][2]["parameters"]["showheight"];
            $weight = $deCode["queryResult"]["outputContexts"][2]["parameters"]["setweight"];
            $contact = $deCode["queryResult"]["outputContexts"][2]["parameters"]["showcontact"];
            $parent = $deCode["queryResult"]["outputContexts"][2]["parameters"]["setparent"];
            $food = $deCode["queryResult"]["outputContexts"][2]["parameters"]["showfood"];  
        }

        $msg = "ดำเนินการลงทะเบียนเข้าร่วม $activities ให้ $person เรียบร้อยแล้ว";
        sendAlert($msg, $token, $userId);      

        $msg = "หากต้องการลงทะเบียนเพิ่มเติม ให้เลือกเมนู 'ตารางกิจกรรม'";
        sendAlert($msg, $token, $userId); 

        $msg = "หากต้องการชำระเงินให้เลือกเมนู 'สรุปยอดชำระเงิน' เพื่อดูรายละเอียดการชำระเงินทั้งหมด";
        sendAlert($msg, $token, $userId); 

        //ดึงราคากิจกรรม 
        $getData = getAllValueDB();
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['name'] == $activities) {
                $price = $entry->getValues()['price'];
                break;
            }
        }

        $status = "ลงทะเบียนเรียบร้อยแล้ว โปรดดำเนินการชำระเงิน โดยให้เลือกเมนู 'สรุปยอดชำระเงิน' เพื่อดูรายละเอียดการชำระเงินทั้งหมด";
        $moneytransfer = "-";
        
        //หากมี UID เดียวกันให้สร้าง Referer เดียวกัน เพื่อให้สามารถตรวจสอบราคาได้ถูกต้อง
        $getData = getAllValueRegister();
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['uid'] == $userId) {
                $referer = $entry->getValues()['referer'];
                break;
            }
            else{
                $referer = rand(1, 10000);
            }
        }
        
        //Insert Data
        insertValueRegister($userId, $referer, $activities, $person, $age, $height, $weight, $food, $contact, $parent, $price, $status, $moneytransfer);

        //อัปเดตจำนวนผู้เข้าร่วมกิจกรรม
        $getData = getAllValueDB();
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['name'] == $activities) {
                $no = $entry->getValues()['no'];
                $image = $entry->getValues()['image'];
                $name = $entry->getValues()['name'];
                $description = $entry->getValues()['description'];
                $price = $entry->getValues()['price'];
                $total = $entry->getValues()['total'];
                $balance = $entry->getValues()['balance'];
                $discount = $entry->getValues()['discount'];
                $whenmorethan = $entry->getValues()['whenmorethan'];
                $ratefollow = $entry->getValues()['ratefollow'];
                break;
            }
        }
        $entry->update(array_merge($entry->getValues(), ['no' => $no], ['image' => $image], ['name' => $name], ['description' => $description], ['price' => $price], ['total' => $total], ['balance' => $balance-1], ['discount' => $discount], ['whenmorethan' => $whenmorethan], ['ratefollow' => $ratefollow]));
        break;
    //สรุปข้อมูลที่ต้องชำระเงิน
    case "paymentInvoice": 
        //แจ้งเตือนให้รอระบบประมวลผล
        $wait = "กรุณารอสักครู่...";
        sendAlert($wait, $token, $userId);

        //รวมจำนวนผู้ลงทะเบียนอ้างอิง UID
        $getData = getAllValueRegister();
        $sum = 0;
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['uid'] == $userId) {
                $sum++;
            }
        }

        //เช็คว่าเคยลงทะเบียนแล้วหรือยัง
        if($sum == 0){
            $msg = "ท่านยังไม่ได้ลงทะเบียนเข้าร่วมกิจกรรม";
            sendAlert($msg, $token, $userId);
        }
        else{
            $getData = getAllValueRegister();
            $msg .= "ท่านได้ลงทะเบียนให้กับ ";
            $count = 0;
            foreach ($getData->getEntries() as $i => $entry) {
                if ($entry->getValues()['uid'] == $userId) {
                    $count++;
                    $status = $entry->getValues()['status'];
                    $referer = $entry->getValues()['referer'];
                    $parent = $entry->getValues()['parent'];
                    $msg .= $entry->getValues()['studentname'];
                    if ($count < $sum) {
                        $msg .= " และ ";
                    }
                }
            }
            
            //ตัดคำว่า และ หลังสุดออก
            //$msg = mb_substr($msg ,0,-5);

            //ดึงราคาและเงื่อนไขของโปรโมชันของกิจกรรม เพื่อนำมาคำนวนเงินรวม
            $getData = getAllValueDB();
            foreach ($getData->getEntries() as $i => $entryDB) {
                if ($entryDB->getValues()['name'] == $entry->getValues()['campname']) {
                    $price = $entryDB->getValues()['price'];
                    $discount = $entryDB->getValues()['discount'];
                    $whenmorethan = $entryDB->getValues()['whenmorethan'];
                    $ratefollow = $entryDB->getValues()['ratefollow'];
                    break;
                }
            }

            //สมการคำนวนโปรโมชัน
            // if ($count >= $whenmorethan) {
            //     $totalpay = (($count*$price)+($parent*$ratefollow));
            //     $totaldiscount = $count*$price*$discount/100;
            //     $totalpay = floor($totalpay - $totaldiscount);
            //     $promotion = "";
                //$promotion = "(โปรโมชัน เราลดให้ !! ".ceil($totaldiscount)." บาท)";
            //} else {
                $totalpay = (($count*$price)+($parent*$ratefollow));
            //    $promotion = "";
            //}

            //ตรวจสอบว่าชำระเงินแล้วหรือยัง
            if ($status == "ชำระเงินเรียบร้อยแล้ว") {
                $msg = "สถานะการชำระเงินของท่าน ".$status." ท่านสามารถใช้รหัสอ้างอิง ".$referer." ในการเข้าร่วมกิจกรรม";
                sendAlert($msg, $token, $userId);
            } else {
                $msg .= " เข้าร่วม ".$entry->getValues()['campname']. " มีผู้ปกครองที่ต้องการเดินทางไปร่วมกิจกรรม จำนวน ".$parent." คน ราคารวมทั้งสิ้น ".$totalpay." บาท ".$promotion." หากข้อมูลไม่ถูกต้อง กรุณาเลือกเมนู 'ติดต่อเรา' เพื่อให้เจ้าหน้าที่ดำเนินการแก้ไขก่อนโอนเงิน";
                sendAlert($msg, $token, $userId);
                //แจ้งเลขบัญชีโอนเงิน
                sendAccountTransfer($token, $userId);
            }
        }
        break;    
    //แจ้งโอนเงิน
    case "paymentConfirm": 

        //แจ้งเตือนให้รอระบบประมวลผล
        $wait = "กรุณารอสักครู่...";
        sendAlert($wait, $token, $userId);

        //หาจำนวนผู้เข้าอบรมอ้างอิง UID
        $getData = getAllValueRegister();
        $count = 0;
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['uid'] == $userId) {
                $count++;
                $parent = $entry->getValues()['parent'];
            }
        }

        //เช็คว่าเคยลงทะเบียนแล้วหรือยัง
        if($count == 0){
            $msg = "ท่านยังไม่ได้ลงทะเบียนเข้าร่วมกิจกรรม";
            sendAlert($msg, $token, $userId);
        }
        else{
            //ดึงราคาและเงื่อนไขของโปรโมชันของกิจกรรม เพื่อนำมาคำนวนเงินรวม
            $getData = getAllValueDB();
            foreach ($getData->getEntries() as $i => $entryDB) {
                if ($entryDB->getValues()['name'] == $entry->getValues()['campname']) {
                    $price = $entryDB->getValues()['price'];
                    $discount = $entryDB->getValues()['discount'];
                    $whenmorethan = $entryDB->getValues()['whenmorethan'];
                    $ratefollow = $entryDB->getValues()['ratefollow'];
                }
            }

            //สมการคำนวนโปรโมชัน
            // if ($count >= $whenmorethan) {
            //     $totalpay = (($count*$price)+($parent*$ratefollow));
            //     $totaldiscount = $count*$price*$discount/100;
            //     $totalpay = floor($totalpay - $totaldiscount);
            // } else {
                $totalpay = (($count*$price)+($parent*$ratefollow));
            //}

            //ตรวจสอบ Status
            $getData = getAllValueRegister();
            foreach ($getData->getEntries() as $i => $entry) {
                if ($entry->getValues()['uid'] == $userId) {
                    $status = $entry->getValues()['status'];
                    $referer = $entry->getValues()['referer'];
                    break;
                }
            }

            //ตรวจสอบว่าชำระเงินแล้วหรือยัง
            if($status == "ชำระเงินเรียบร้อยแล้ว"){
                $msg = "สถานะการชำระเงินของท่าน ".$status." ท่านสามารถใช้รหัสอ้างอิง ".$referer." ในการเข้าร่วมกิจกรรม";
            }
            else{
                $msg = "ท่านต้องชำระเงินทั้งสิ้น $totalpay บาท หากข้อมูลถูกต้อง ให้อัปโหลดสลิปโอนเงินของท่าน หากข้อมูลไม่ถูกต้อง กรุณาเลือกเมนู 'ติดต่อเรา' เพื่อให้เจ้าหน้าที่ดำเนินการแก้ไขก่อนโอนเงิน";
            }
            sendAlert($msg, $token, $userId);
        }
        break;
    //ตรวจสอบสถานะการโอนเงิน
    case "paymentStatus": 

        //แจ้งเตือนให้รอระบบประมวลผล
        $wait = "กรุณารอสักครู่...";
        sendAlert($wait, $token, $userId);
        
        //หาจำนวนผู้เข้าอบรมอ้างอิง UID
        $getData = getAllValueRegister();
        $count = 0;
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['uid'] == $userId) {
                $count++;
            }
        }

        //เช็คว่าเคยลงทะเบียนแล้วหรือยัง
        if($count == 0){
            $msg = "ท่านยังไม่ได้ลงทะเบียนเข้าร่วมกิจกรรม";
            sendAlert($msg, $token, $userId);
        }
        else{
            $getData = getAllValueRegister();
            foreach ($getData->getEntries() as $i => $entry) {
                if ($entry->getValues()['uid'] == $userId) {
                    $status = $entry->getValues()['status'];
                    $referer = $entry->getValues()['referer'];
                    break;
                }
            }
            if ($status == "ชำระเงินเรียบร้อยแล้ว") {
                $msg = "สถานะการชำระเงินของท่าน ".$status." ท่านสามารถใช้รหัสอ้างอิง ".$referer." ในการเข้าร่วมกิจกรรม";
            } else {
                $msg = "สถานะการชำระเงินของท่าน ".$status;
            }
            sendAlert($msg, $token, $userId);
        }
        break;
    //ส่งกำหนดการ
    case "scheduleAction": 
        sendSchedule($token, $userId);
        break;
    default:
        callDialogFlow();
    }
}
//อัปโหลดสลิปโอนเงิน
else if($messageType == 'image'){
    $LINEDatas['messageId'] = $deCode['events'][0]['message']['id'];
    $results = getContent($LINEDatas);
    if($results['result'] == 'S'){
        //ดึงเลขอ้างอิง
        $getData = getAllValueRegister();
        foreach ($getData->getEntries() as $i => $entry) {
            if ($entry->getValues()['uid'] == $userId) {
                $referer = $entry->getValues()['referer'];
                break;
            }
        }

        $time = $referer.'-'.time();
        $file = 'tmp_image/' . $time . '.jpg';
        $success = file_put_contents($file, $results['response']);

        $msg = "บันทึกภาพของท่านเรียบร้อยแล้ว กรุณารอเจ้าหน้าที่ตรวจสอบและติดต่อกลับ หรือสามารถตรวจสอบสถานะการโอนเงินได้ด้วยตนเอง โดยให้เลือกเมนู 'ตรวจสอบสถานะ'";
        sendAlert($msg, $token, $userId);
        
        //ตรวจสอบข้อมูลเดิม เพื่ออัปเดตสถานะการโอนเงิน
        $getData = getAllValueRegister();
        foreach ($getData->getEntries() as $entry) {
            if($entry->getValues()['uid'] == $userId){
                $uid = $entry->getValues()['uid'];
                $referer = $entry->getValues()['referer'];
                $campname = $entry->getValues()['campname'];
                $studentname = $entry->getValues()['studentname'];
                $age = $entry->getValues()['age'];
                $height = $entry->getValues()['height'];
                $weight = $entry->getValues()['weight'];
                $food = $entry->getValues()['food'];
                $contact = $entry->getValues()['contact'];
                $price = $entry->getValues()['price'];
                $status = "อยู่ระหว่างเจ้าหน้าที่ตรวจสอบสลิปของท่าน";
                $moneytransfer = '=IMAGE("https://ipsa-support.herokuapp.com/tmp_image/'.$time.'.jpg")';
                $entry->update(array_merge($entry->getValues(), ['uid' => $uid], ['referer' => $referer], ['campname' => $campname], ['studentname' => $studentname], ['age' => $age], ['height' => $height], ['weight' => $weight], ['food' => $food], ['contact' => $contact], ['price' => $price], ['status' => $status], ['moneytransfer' => $moneytransfer]));
        
            }
        }
    }
} else {
    //กรณีที่ไม่มีข้อความตรงกับ Server จะถูกส่งข้อมูล JSON ไปที่ DialogFlow
    callDialogFlow();
}