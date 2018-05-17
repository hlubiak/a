<?php

class RetireProduct {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*retire product*/
    function retireProduct($product_id){
        
        $update_product = $this->connection->prepare("
            UPDATE product 
            SET retire_id=? 
            WHERE product_id=? 
            ");
        $retire_id = 1;
        $update_product->bind_param( "ii", $retire_id, $product_id );
        if( $update_product->execute() == true){
            echo "RETIRED";;
        }else{
            echo "ERROR";
        }
        /*close database*/
        $this->connection->close();
    }/*close retire function*/
    
}
