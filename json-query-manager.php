<?php
require('websocket-php/vendor/autoload.php');
use WebSocket\Client;

class JsonQueryManager {

    private $apiKey;

    function __construct($username,$password){
        $this->apiKey = $this->GetApiKey($username,$password);
    }

    function GetApiKey($username,$password){
        $client = new Client("http://shop.textalk.se/backend/jsonrpc/v1/?webshop=76024&language=sv");
        $client->send('{
            "jsonrpc": "2.0",
            "method":  "Admin.login",
            "id":      1,
            "params":  ["'.$username.'","'.$password.'"]
        }');

        $result = $client->receive();
        $decode = json_decode($result);

        return $decode->result;
    }

    function execute($method, $jsonParam){
   
        $client = new Client("http://shop.textalk.se/backend/jsonrpc/v1/?webshop=76024&language=sv&auth=".$this->apiKey);
        $client->send('{
            "jsonrpc": "2.0",
            "method":  "'.$method.'",
            "id":      1,
            "params":  '.$jsonParam.'
        }');

        $result = $client->receive();
        $decode = json_decode($result, true);

        return $decode["result"];
    }
}
