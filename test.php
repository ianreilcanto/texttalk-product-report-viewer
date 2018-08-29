<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// require('websocket-php/vendor/autoload.php');
// use WebSocket\Client;

// $client = new Client("http://shop.textalk.se/backend/jsonrpc/v1/?webshop=76024&language=sv");
//         $client->send('{
//             "jsonrpc": "2.0",
//             "method":  "Admin.login",
//             "id":      1,
//             "params":  ["moek@linohalm.se","Pepsi2017"]
//         }');

// $client = new Client("https://shop.textalk.se/backend/jsonrpc/v1/?webshop=76024&auth=YXV0aDoSEaV+vY/TQ0OGVLS08AoW3mUDjLdVlBuQRruIvaX2H2Kc");
// $client->send('{
//     "jsonrpc": "2.0",
//     "method":  "Supplier.list",
//     "id":      1,
//     "params": [{"company": true, "uid" : true},true]
// }');

// $result = $client->receive();
// $decode = json_decode($result, true);

// print_r($decode);

$url = "https://qrcode-monkey.p.mashape.com/qr/custom"; 

$test = '{
  "data":"https://www.qrcode-monkey.com",
  "config":{
  "body":"circle",
  "logo":""
  },
  "size":300,
  "download":false,
  "file":"svg"
  }';

$array = json_decode($test,true);

$postData = urldecode(http_build_query($array));

//open connection
$ch = curl_init();

curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($array));
curl_setopt($ch,CURLOPT_POSTFIELDS, $postData);


//execute post
$result = curl_exec($ch);

print_r($result);

//close connection
curl_close($ch);