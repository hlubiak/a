<?php

class ProductNumberInCart {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*show number of cart products*/
    function productNumberInCart(){
        //start session
        session_start();
        $count=0;
        if(isset($_SESSION['product_cart_id'])){
            for( $i=0; $i < count($_SESSION['product_cart_id']); $i++ ){
                if($_SESSION['product_price'][$i] > 0){
                    $count = $count + 1;
                }
            }
            echo "Cart: ".$count;
        }else{
            echo 0;
        }
    }
}
