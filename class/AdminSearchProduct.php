<?php

class AdminSearchProduct {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*admin search product*/
    function adminSearchProduct($product_name){
        
        $search = $this->connection->real_escape_string( $product_name );
        $search = preg_replace( "/[^A-Za-z0-9 ]/", '', $search );
        $search = "'%".$product_name."%'";
        
        $search_product = $this->connection->prepare("
            SELECT product_id, product_name, product_color, file, weight 
            FROM product
            WHERE product_name LIKE $search
            ORDER BY timestamp DESC
            LIMIT 5
        ");
        $search_product->execute();
        $search_product->bind_result( $product_id, $searched_product_name, $product_color, $file, $weight );
        $count=0;
        while($search_product->fetch()){
            $count++;
            echo "<div>".
                    "<a href='products.php?retire_id=all&&product=all&&action=0&&category=all&&option=1&search=$searched_product_name'>".
                    $searched_product_name.
                    "</a>".
                "</div>";
        }
        if($count==0){
            echo "Product not found";
        }
    }
    
}
