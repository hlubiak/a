<?php

class UpdateProduct {
    
    private $connection;

    //constructor
    function __construct(){

        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();

    }
    
    /*update product data*/
    function updateProduct($update_product_id, $product_category_id, $product_brand_id, $product_name, $product_photo, $product_weight, $product_description, $product_color, $product_price, $product_type_id, $gender){
        
        /*check if product update is not empty*/
        if(!empty($product_photo)){
            $update_product = $this->connection->prepare("
                        UPDATE product 
                        SET category_id=?, product_name=?, price=?, product_type=?, weight=?, product_description=?, product_color=?, brand_id=?, gendar=?, file=? 
                        WHERE product_id=? 
                        ");
            $update_product->bind_param( "sssssssssss", $product_category_id, $product_name, $product_price, $product_type_id, $product_weight, $product_description, $product_color, $product_brand_id, $gender, $product_photo, $update_product_id );
            if( $update_product->execute() == true){
                echo "Product updated successfully."."<a href='products.php?retire_id=0&&product=all&&action=0&&category=all&&option=1'><button>"."OK"."</button></a>";;
            }else{
                echo "Sorry! something went wrong. Product not updated.";
            }
        }else{
            echo "Please fill in all details.";
        }
        /*close connection*/
        $this->connection->close();
    }/*close update product function*/
    
    
}
