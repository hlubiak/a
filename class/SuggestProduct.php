<?php

class SuggestProduct {
   
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function suggestProduct($purchased_product_id){
         
        /*get info on the product purchased*/
        $get_purchased_product_info = $this->connection->prepare("
            SELECT product_description, category_id 
            FROM product
            WHERE product_id=?
            ORDER BY timestamp DESC
        ");
        $get_purchased_product_info ->bind_param("i",$purchased_product_id);
        $get_purchased_product_info ->execute();
        $get_purchased_product_info ->bind_result( $product_description, $category_id );
        $count=0;
        while($get_purchased_product_info ->fetch()){};
        $count++;
        /*get suggested products*/
        $get_suggested_product = $this->connection->prepare("
                SELECT product_id, product_name, price, weight, product_color, file 
                FROM product
                WHERE category_id = ? AND product_id != ?
                LIMIT 3
                    ");
                $get_suggested_product ->bind_param("ii",$category_id, $purchased_product_id);
               $get_suggested_product->execute();
                $get_suggested_product->bind_result( $product_id, $product_name, $price, $weight, $product_color, $file  );
                $found=0;
                while($get_suggested_product->fetch()){
                    $found++;
                    if( $file  == "" ){
                        $img  = "<img src='/ecommerce/upload/profile icon.png' class='img_fit' />";
                    }else{
                        $img = "<img src='/ecommerce/upload/".$file ."' class='img_fit' />";
                    }
                    
                    /*switch purpose id to create activity button for the products
                     * 0 purpose id = activity buttons for users
                     * 1 purpose id = activity button for admin
                     */
                    $activity_btn = "<div id='btn' class='btn'>
                                        <button id='btn-front' class='btn-front' onclick='add_to_cart(\"".$product_id."product_id_holder\",\"".$product_id."table_id\", \"".$product_id."choose_size\", \"".$product_id."price\")'>
                                            Add to cart....
                                        </button>
                                      </div>";
                    
                    echo
                        "<a href='#".$product_name.$product_id."same_product_feed' >".
                            "<div id='".$product_name.$product_id."same_product_feed' class='feed_holder'>".
                                "<div id='".$product_id."same_product_img_holder' class='img_holder' onclick='buy(\"".$product_name."".$product_id ."same_product_feed\", \"".$product_id."same_product_img_holder\", \"".$product_id."same_product_buy_holder\", \"".$product_id."same_product_buy_everything_holder\", \"".$product_id."same_product_filter\")'>".$img."</div>".
                                "<div id='".$product_id."same_product_buy_holder' class='product_name_holder' style='display:block;'>".
                                    "<div id='".$product_id."same_product_buy_everything_holder'>".
                                        "<div class='margin_bottom'>".$product_name."</div>".
                                        "<div class='margin_bottom'>"."R ".number_format($price, 2)."</div>".
                                        "<div id='".$product_id."same_product_filter' class='filter hide '>".
                                            "<div class='margin_bottom'>".$product_description."</div>".
                                            "<div class='label'>".$product_color."</div>";

                                    echo "<form action='object.php' method='post'>";
                                    echo "<div class='label'>"."Choose size"."</div>";
                                    echo "<select name='add_to_cart_product_size' id='".$product_id."choose_size'>";
                                            $weight_array = explode( ',' , $weight );
                                            for( $i= 0; $i < count( $weight_array ); $i++ ){
                                                echo "<option  type='checkbox' value='".$weight_array[$i]."' onclick='sizes'>".$weight_array[$i]."<option/>";
                                            }
                                    echo "</select>";
                                    echo  "<div class='label'>"."Choose quantity"."</div>";
                                    echo  "<select name='product_quantity' >".
                                                "<option value='1'>"."1"."</option>".
                                                "<option value='2'>"."2"."</option>".
                                                "<option value='3'>"."3"."</option>".
                                                "<option value='4'>"."4"."</option>".
                                                "<option value='5'>"."5"."</option>".
                                                "<option value='6'>"."6"."</option>".
                                                "<option value='7'>"."7"."</option>".
                                                "<option value='8'>"."8"."</option>".
                                                "<option value='9'>"."9"."</option>".
                                            "</select>";
                                    //create hidden inputs to hold and send data of the product sent
                                    echo    "<input type='hidden' id='".$product_id."product_id_holder' value='".$product_id."'/>".
                                            "<input type='hidden' id='".$product_id."table_id' value='2'/>".
                                            "<input type='hidden' id='".$product_id."price' value='".$price."'/>".
                                            "<input type='hidden' name='add_to_cart_product_id' value='".$product_id."'/>".
                                            "<input type='hidden' name='add_to_cart_table_id' value='2'/>".
                                            "<input type='hidden' name='add_to_cart_product_price' value='$price'/>".
                                            "<input type='hidden' name='send_id' value='8'/>".
                                            $activity_btn.
                                           "<span id='output".$product_id."'></span>".
                                            "</form>".
                                        "</div>".
                                    "</div>".
                                "</div>".
                            "</div>".
                        "</a>"
                        ;
                }
                
                if($found > 0){
                    echo "<div class='successful_cart_holder'>". 
                        "You added the product successfully! ".
                        "<span class='heading'>".
                            "You might also want to add these to your cart.".
                        "</span>"."<br/>".
                        "OR ".
                        "<a href='checkout.php'>".
                            "<button class='btn_5'>Checkout</button>".
                        "</a>".
                        "OR ".
                        "<a href='index.php'>".
                            "<button class='btn_5'>".
                                "Continue Shopping".
                            "</button>".
                        "</a>".
                        "</div>";
                }else{
                    echo "<div class='successful_cart_holder'>".
                            "Successfully added to cart.".
                            "<a href='index.php'>OK</a>".
                        "</div>";
                }
               
                /*close connection*/
                $this->connection->close();
    }
    
}