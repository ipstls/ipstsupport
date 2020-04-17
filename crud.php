<?php

//Referer : https://www.twilio.com/blog/2017/03/google-spreadsheets-and-php.html

require __DIR__ . '/vendor/autoload.php';

use Google\Spreadsheet\DefaultServiceRequest;
use Google\Spreadsheet\ServiceRequestFactory;

$serviceRequest = new DefaultServiceRequest($accessToken);
ServiceRequestFactory::setInstance($serviceRequest);

putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/client_secret.json');
$client = new Google_Client;
$client->useApplicationDefaultCredentials();

$client->setApplicationName("Something to do with my representatives");
$client->setScopes(['https://www.googleapis.com/auth/drive','https://spreadsheets.google.com/feeds']);

if ($client->isAccessTokenExpired()) {
    $client->refreshTokenWithAssertion();
}

$accessToken = $client->fetchAccessTokenWithAssertion()["access_token"];
ServiceRequestFactory::setInstance(
    new DefaultServiceRequest($accessToken)
);

//Json Activities
// // Get our spreadsheet
// $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)->getSpreadsheetFeed()->getByTitle('Google Sheet Line');

// // Get the first worksheet - Register Tab
// $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
// $worksheet = $worksheets[1];
// $listFeed = $worksheet->getListFeed();
// $datax = Array(); 
// foreach ($listFeed->getEntries() as $i => $entry) {
//         $datax['thumbnailImageUrl'] = $entry->getValues()['image'];
//         $datax['title'] = $entry->getValues()['name'];
//         $datax['text'] = $entry->getValues()['description'];
//         $datax['actions'][0]['type'] = "message";
//         $datax['actions'][0]['label'] = "Action 1";
//         $datax['actions'][0]['text'] = "Action 1";
//         $datax['actions'][1]['type'] = "message";
//         $datax['actions'][1]['label'] = "Action 2";
//         $datax['actions'][1]['text'] = "Action 2";
// }
// $myJSON = json_encode($datax);
// $str = addslashes($myJSON);
// print_r($str);

//Select All Value from DB Tab
function getAllValueDB(){
    // Get our spreadsheet
    $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)->getSpreadsheetFeed()->getByTitle('Google Sheet Line');
 
    // Get the first worksheet - Register Tab
    $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
    $worksheet = $worksheets[1];
 
    $listFeed = $worksheet->getListFeed();
    return $listFeed;
 }

//Select All Value from Register Tab
function getAllValueRegister(){
   // Get our spreadsheet
   $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)->getSpreadsheetFeed()->getByTitle('Google Sheet Line');

   // Get the first worksheet - Register Tab
   $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
   $worksheet = $worksheets[0];

   $listFeed = $worksheet->getListFeed();
   return $listFeed;
}

function insertValueRegister($userId, $referer, $activities, $person, $age, $height, $weight, $food, $contact, $parent, $price, $status, $moneytransfer){
    // Get our spreadsheet
   $spreadsheet = (new Google\Spreadsheet\SpreadsheetService)->getSpreadsheetFeed()->getByTitle('Google Sheet Line');

   // Get the first worksheet - Register Tab
   $worksheets = $spreadsheet->getWorksheetFeed()->getEntries();
   $worksheet = $worksheets[0];

    $listFeed = $worksheet->getListFeed();
    $listFeed->insert([
        'uid' => $userId,
        'referer' => $referer,
        'campname' => $activities,
        'studentname' => $person,
        'age' => $age,
        'height' => $height,
        'weight' => $weight,
        'food' => $food,
        'contact' => $contact,
        'parent' => $parent,
        'price' => $price,
        'status' => $status,
        'moneytransfer' => $moneytransfer
    ]);
}
?>