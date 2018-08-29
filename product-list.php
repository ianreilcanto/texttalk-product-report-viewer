<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require("QueryManager.php");
$queryData = new QueryManager('moek@linohalm.se','Pepsi2017');

$supplier = isset($_GET['name']) ? $_GET["name"] : "All";

$data = $queryData->ProductList($supplier);

//echo json_encode($data, JSON_PRETTY_PRINT);

echo '{"data":'.json_encode($data).'}';
