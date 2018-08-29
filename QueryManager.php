<?php


require("json-query-manager.php");

class QueryManager {

    private $api;
    private $username = array( "416" => "Fivelstad", "721" => "Hermansson");

    function __construct($username,$password){
    //   $this->api =  $this::getInstance('default', array('webshop' => 76024));
    //   $this->Login($username,$password);
       // parent::__construct($username,$password);

       
        $this->api = new JsonQueryManager($username,$password);    



    }

    // function Login($username,$password){
    //     $this->api->Admin->login($username,$password);
    // }

    function GetCustomerList(){
    //    return  $this->api->Customer->list(array( "uid" => true, "address" => true ,"info"=>array("email"=> true,"customerNumber" => true)),true);
        
        return  $this->api->execute('Customer.list','[{"uid" : true, "address" : true, "info" : { "email" : true , "customerNumber" : true} },true]');
    }

    function GetCustomerNumberByEmail($email){
        $customerList = $this->GetCustomerList();
        $customerNumber = "";

        foreach ($customerList as $key => $value) {
            
            if ($value["info"]["email"] == $email) {
                $customerNumber = $value["info"]["customerNumber"];
            }
        }

        return $customerNumber;

    }


    function GetOrders(){
        // return  $this->api->Order->list(
        //     array("customer" =>  array('info' => true, "address" => true, "comment"=> true), "uid" => true,"delivery" => true, "items" => true, "costs" => true,"paymentStatus" => true, "ordered" => true),true
        //   );

        return  $this->api->execute('Order.list','[{"customer" : { "info" : true, "address" : true, "comment" : true } , "uid" : true,"delivery" : true, "items" : true, "costs" : true,"paymentStatus" : true, "ordered" : true },true]');
    }

    function GetOrderDate($id){
        //return $this->api->Order->get($id, array("ordered"));
        return $this->api->execute("Order.get",'['.$id.',["ordered"]]');
    }

    //112304194
    function GetItem($itemId){
        //return $this->api->OrderItem->get($itemId);
        return $this->api->execute("OrderItem.get",'['.$itemId.']');
    }

    function GetDeliveryMethod($id){


        $methods = $this->api->execute("DeliveryMethod.get",'['.$id.',"name"]');
        
        return $methods["name"]["sv"];

    }

    function GetArticleGroupId($articleId){
      //$result =  $this->api->Article->get($articleId,array("articlegroup"));
      $result = $this->api->execute("Article.get",'['.$articleId.',["articlegroup"]]');

      return $result["articlegroup"];
    }

    function GetParentArticleGroupId($childArticleGroupId){
        //$result =  $this->api->Articlegroup->get($childArticleGroupId, array("parent" => true) );
       $result =  $this->api->execute('Articlegroup.get','['.$childArticleGroupId.',{"parent" : true}]');
        return $result["parent"];
    }


    function GetParentArticleGroupName($articleGroupId){

        //$result = $this->api->Articlegroup->get($articleGroupId, array("name" => array("sv")));
        $result = $this->api->execute('Articlegroup.get','['.$articleGroupId.',{"name" : ["sv"]}]');
        $name = "";

        foreach ($result as $key => $value) {
            $name =  $value["sv"];
        }

        return $name;

    }


    function GetFreight($methodId){
        $freights = array(
                            '103780' => 'A', 
                            '103782' => 'A', 
                            '104080' => 'A', 
                            '104082' => 'E', 
                            '104084' => 'A', 
                            '104086' => 'A',
                            '102228' => '' 

                        );

        return $freights[$methodId];
    }

  
   

    function GetSuppliers(){
        //return $this->api->Supplier->list(array("company"=>true,"uid" => true), true);
        //return $this->api->Supplier->get(132995760,array("company"));

        return $this->api->execute('Supplier.list','[{"company": true, "uid" : true},true]');

    }

    function LoginSupplier($username, $password){
        $suppliers = $this->GetSuppliers();
        $session =  array();

        $key1 = array_search($username, array_reverse($this->username, true));

        foreach ($suppliers as $key => $supplier) {
           if( strpos(strtoupper($supplier["company"]) , strtoupper($key1)) !== false && $supplier["uid"] == $password){
                $session = $supplier;
           }
        }

        return $session;
    }

    function GetItemList(){

        $orders = $this->GetOrders();
        $itemIds = array();
        $method = "";

        foreach ($orders as $key => $order) {

             foreach ($order as $ke => $val) {

                   if ($ke == "items") {

                       foreach ($val as $k => $item) {
                           array_push($itemIds, $item);
                       }
                   }
                }
            }


             return $itemIds;
    }

    function ProductList($supName){

        $items = $this->GetItemList();
        $products= array();
        $data = array();

        foreach ($items as $key => $value) {          
              $item = $this->GetItem($value);

             

              if(isset($item["article"])){

              $childArticleGroupId = $this->GetArticleGroupId($item["article"]);
              $parentArticleId = $this->GetParentArticleGroupId($childArticleGroupId);
              $supplierName = $this->GetParentArticleGroupName($parentArticleId);

              //do checking here by supplier name

              if(strpos($supplierName, $supName) !== false){
                 array_push($products,  array( "supplier" => $supplierName, "quantity" => $item["choices"]["quantity"], "ordered" => explode("T", $this->GetOrderDate($item["order"])["ordered"])[0] ,"productName" => $item["articleName"] , "articleNumber" => $item["articleNumber"] ) );
                }
              }

             
        }

       // $data["data"][]=$products;
        return $products;
     
    }



    function FirstReport(){
        $orderList = $this->GetOrders();

        $this->ArrayToXml($orderList);

    }

    function ArrayToXml($orders){
            $xmlDoc = new DOMDocument();
            $root = $xmlDoc->appendChild($xmlDoc->createElement("orders"));

             foreach($orders as $order){
                if(!empty($order)){
                    $tabOrder = $root->appendChild($xmlDoc->createElement('order'));
                    foreach($order as $key=>$val){           

                        if($key == "uid"){
                            $tabOrder->appendChild($xmlDoc->createElement("orderId", $val));
                        }

                        if($key == "ordered"){
                            $tabOrder->appendChild($xmlDoc->createElement("dateTimeOrdered", $val));
                        }

                       if($key == "customer"){
                            $tabOrder->appendChild($xmlDoc->createElement("comment", $val["comment"]));
                       }

                       if($key == "delivery"){
                            $tabOrder->appendChild($xmlDoc->createElement("deliveryMethod", $this->GetDeliveryMethod($val["method"]) ));

                            $tabOrder->appendChild($xmlDoc->createElement("freight", $this->GetFreight($val["method"]) ));
                       }

                       if($key == "customer"){
                        $tabOrder->appendChild($xmlDoc->createElement("customer_number", $this->GetCustomerNumberByEmail($val["info"]["email"]) ));
                       }

                       if($key == "items"){

                            $itemsRow = $tabOrder->appendChild($xmlDoc->createElement('items'));

                            foreach ($val as $k => $value) {
                                 $itemRow = $itemsRow->appendChild($xmlDoc->createElement("item"));
                                 $item = $this->GetItem($value);

                                 foreach ($item as $ke => $itemDetail) {        
                                    if($ke == "articleNumber"){
                                         $itemRow->appendChild($xmlDoc->createElement("articleNumber", $itemDetail));
                                    }
                                    if($ke == "articleName"){
                                         $itemRow->appendChild($xmlDoc->createElement("itemName", $itemDetail));
                                    }   
                                    if($ke == "choices"){
                                         $itemRow->appendChild($xmlDoc->createElement("quantity", $itemDetail["quantity"]));
                                    }

                                    if($ke == "costs"){
                                        $itemRow->appendChild($xmlDoc->createElement("price-exVat", $itemDetail["unit"]["exVat"]));
                                        $itemRow->appendChild($xmlDoc->createElement("price-inVat", $itemDetail["unit"]["incVat"]));
                                    }

                                    if($ke == "article"){
                                        $childArticleGroupId = $this->GetArticleGroupId($itemDetail);
                                       $parentArticleId = $this->GetParentArticleGroupId($childArticleGroupId);
                                        $supplierName = $this->GetParentArticleGroupName($parentArticleId);
                                        $supplierNumner = "";
                                        foreach ($this->username as $key => $value) {

                                            if(strpos($supplierName, $value) !== false){
                                                $supplierNumner = $key;
                                            }
                                        }
                                        
                                            
                                        $itemRow->appendChild($xmlDoc->createElement("supplierNumber",$supplierNumner));

                                    }               
                                 }

                            }
                       }
                    }
                }
            }


         //header("Content-Type: text/plain");

         //make the output pretty
        $xmlDoc->formatOutput = true;

        $file_name = 'orders.xml';
        $xmlDoc->save("file/" . $file_name);
    
        //return xml file name
        return $file_name;
    }



}