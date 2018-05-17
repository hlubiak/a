<?php

class AddToCart {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    public function addToCart($product_id, $table_id, $product_size, $product_price, $product_quantity){
        
        /*start session*/
        session_start();
        
        if(isset($_SESSION['product_cart_id'])){
            
            $current_no = $_SESSION['counter'] + 1;
            
            $_SESSION['product_cart_id'][$current_no] = $product_id;
            $_SESSION['product_table_id'][$current_no] = $table_id;
            $_SESSION['product_price'][$current_no] = $product_price;
            $_SESSION['product_size'][$current_no] = $product_size;
            $_SESSION['product_quantity'][$current_no] = $product_quantity;
            $_SESSION['counter'] = $current_no;
            
            /*if product is successfully added to cart
             * suggest other products a user would probably buy
            */
            header("Location:index.php?suggestion=1&&product=$product_id");
            exit;
            
        }else{
            $product_cart_id = array();
            $product_table_id = array();
            
            $_SESSION['product_cart_id'][0] = $product_id;
            $_SESSION['product_table_id'][0] = $table_id;
            $_SESSION['product_price'][0] = $product_price;
            $_SESSION['product_size'][0] = $product_size;
            $_SESSION['product_quantity'][0] = $product_quantity;
            $_SESSION['counter'] = 0;
            
            header("Location:index.php?suggestion=1&&product=$product_id");
            exit;
            
        }
        
    }
}
