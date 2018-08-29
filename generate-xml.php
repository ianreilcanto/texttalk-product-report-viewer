<?php 


require("QueryManager.php");

$queryData = new QueryManager('moek@linohalm.se','Pepsi2017');
//$queryData->GetOrders();


//echo "<pre>";
//print_r($queryData->GetCustomerNumberByEmail('anniewestlin@gmail.com'));
//134494391
$queryData->FirstReport();



//echo $queryData->GetParentArticleGroupId(5071514);
