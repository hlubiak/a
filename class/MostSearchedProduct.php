<?php

class MostSearchedProduct {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*get most searched products*/
 function mostSearchedProduct(){
     
    /*get current date, year*/
    $current_yr = date("Y");
    $prg_current_yr = $this->connection->real_escape_string( $current_yr );
    $prg_current_yr = preg_replace( "/[^A-Za-z0-9 ]/", '', $prg_current_yr );
    $prg_current_yr = "'%".$current_yr."%'";
    
    /*get searched products for only the current year*/
    $get_searched_product = $this->connection->prepare("
            SELECT product_id, search_no,timestamp 
            FROM searched_product
            WHERE timestamp LIKE $prg_current_yr
            ORDER BY search_no DESC
        ");
        $get_searched_product->execute();
        $get_searched_product->bind_result( $product_id, $search_no, $timestamp );
        //initialise total sales
        $total_searches_current_yr=0;
        $searched_product = 0;
        $searched_product_amount = 0;
        while($get_searched_product->fetch()){
            $total_searches_current_yr += $search_no;
            $searched_product .= $product_id.",";
            $searched_product_amount .= $search_no.",";
        }
        
        echo "<h4>".
                "Total Searches".
                " ".$current_yr." : ".$total_searches_current_yr.
            "</h4>";
        
        /*show products most search
         *ranked by descending order
        */
        $arr_searched_product = explode(",",$searched_product);
        $arr_searched_product_amount = explode(",",$searched_product_amount);
        for($i=0; $i<count($arr_searched_product); $i++){
            $show_product = $this->connection->prepare("
                SELECT product_id, product_name, file 
                FROM product
                WHERE product_id = ?
                ORDER BY timestamp DESC
            ");
            $show_product->bind_param("i",$arr_searched_product[$i]);
            $show_product->execute();
            $show_product->bind_result( $searched_product_id, $product_name, $file );
            $count=0;
            while($show_product->fetch()){
                $count++;
                if( $file  == "" ){
                    $img  = "<img src='/ecommerce/upload/profile icon.png' class='img_fit'/>";
                }else{
                    $img = "<img src='/ecommerce/upload/".$file ."' class='img_fit'/>";
                }
                echo
                    "<div  class='feed_holder'>".
                        "<span class='times'>".$arr_searched_product_amount[$i]."</span>".
                        "<div  class='img_holder' >".
                            $img.
                        "</div>".
                        "<div class='product_name_holder'>".
                            $product_name.
                         "</div>" .  
                    "</div>";
            }
        }

        $show_product->close();
      
        //close connection
        $this->connection->close();
    }
}
