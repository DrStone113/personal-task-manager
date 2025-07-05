<?php

use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;

require './vendor/autoload.php';

$credential = new ServiceAccountCredentials(
  "https://www.googleapis.com/auth/firebase.messaging",
  json_decode(file_get_contents("./php/pvKey.json"), true)
);

$token = $credential->fetchAuthToken(HttpHandlerFactory::build());

$ch = curl_init("https://fcm.googleapis.com/v1/projects/task-manager-c5c37/messages:send");

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer '.$token['access_token']
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, '{
  "message": {
    "token": "drpZWFcB1sejrB8TDl3Vu3:APA91bEiT0e4nF1zzmmJf8QkGfBDL5Zv4LftdpvzC-lU3zKVSj5qNi-eOmnJxoQzNOEPIbcyxjpRN38b5CFLv--f6_3bKhlXA-GjHG1KKYzOZBHbOlvQ_tg",
    "notification": {
      "title": "Planex",
      "body": "Background message body"
    },
    "webpush": {
      "fcm_options": {
        "link": "http://localhost/personal-task-manager/index.php"
      }
    }
  }
}');

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "post");

$response = curl_exec($ch);

curl_close($ch);

echo $response;
?>