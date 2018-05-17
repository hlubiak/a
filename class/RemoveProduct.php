<?php

class RemoveProduct {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function removeProduct( $product_array_position ){
        
        session_start();
        if( isset($_SESSION['product_cart_id']) && isset($_SESSION['product_table_id']) ){
            $_SESSION['product_price'][$product_array_position] = 0; 
        }else{
            echo "Error occured. Product not removed.";
        }  
    }
    
}
