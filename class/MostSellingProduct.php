<?php

class MostSellingProduct {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    //most selling products
    function mostSellingProduct(){
        
        $paid=1;
        $get_sold_product = $this->connection->prepare ("
                SELECT product_id 
                FROM sales 
                WHERE paid=?
                ");
            $get_sold_product->bind_param("i", $paid);
            $get_sold_product->execute();
            $get_sold_product->bind_result( $product_id );
            $sold_product_id=null;
            $count = 0;
            $initial_value=0;
            while ($get_sold_product->fetch()){
                
                $sold_product_id .= $product_id;
                $arr_sold_products = explode(",", $sold_product_id);
                $total_products_sold = count($arr_sold_products);
            }
            
            //show total products sold
            echo "".
                "<h4>"."Products sold: ".$total_products_sold."</h4>".
                "";
            
                /* Our return values 
                 * show the products
                 */
                $max = 0;
                //count how many similar product have been purchased
                $counts = array_count_values($arr_sold_products);
                foreach ($counts as $value => $amount) {
                    if ($amount > $max) {
                        /*show the product
                         * rank the in descending order
                         * the most purchsed at the top number 1 
                         */
                        $show_product = $this->connection->prepare("
                            SELECT product_id, product_name, file 
                            FROM product
                            WHERE product_id = ?
                            ORDER BY timestamp DESC
                        ");
                        $show_product->bind_param("i",$value);
                        $show_product->execute();
                        $show_product->bind_result( $product_id, $product_name, $file );
                        $count=0;
                        while($show_product->fetch()){
                            $count++;
                            
                            if( $file  == "" ){
                                $img  = "<img src='/ecommerce/upload/profile icon.png' class='img_fit' />";
                            }else{
                                $img = "<img src='/ecommerce/upload/".$file ."' class='img_fit' />";
                            }
                            echo
                                "<div  class='feed_holder'>".
                                    "<div  class='img_holder' >".
                                        $img.
                                    "</div>".
                                    "<div class='product_name_holder'>".
                                        $product_name.
                                     "</div>" .  
                                "</div>";
                                //show product and times it has been purchased
                                echo "<span class='times'>".$amount."</span>";
                        }
                        
                        $show_product->close();
                    }
                }
        
            //close connection
            $this->connection->close();
    }
    
}
