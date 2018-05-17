<?php

class Checkout {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function checkout(){
        
        /*Start session to get products in session*/
        session_start();
        
        $count = 0;
        $sold_products =null;
        $sold_product_size =null;
        $sold_product_color =null;
        $sold_product_quantity =null;
        /*check if the user is logged in */
        if(isset($_SESSION['user_id'])){
            $get_user_info = $this->connection->prepare("
                    SELECT user_id, fullname, phonenumber, email, city, town, strnumber 
                    FROM users 
                    WHERE user_id = ?
                    ");
            $get_user_info->bind_param("i", $_SESSION['user_id']);
            $get_user_info->execute();
            $get_user_info->bind_result( $user_id, $fullname, $phonenumber, $email, $city, $town, $strnumber );
            $count=0;
            while($get_user_info->fetch()){
                $count++;
                if($count>0){
                    $got_fullname=$fullname;
                    $got_phonenumber = $phonenumber;
                    $got_email=$email;
                    $got_city = $city;
                    $got_town = $town;
                    $got_strnumber = $strnumber; 
                }
            }
        }else{
            $got_fullname = ""; 
            $got_phonenumber = ""; 
            $got_email = "";
            $got_city = "";
            $got_town = "";
            $got_strnumber = "";
        }
        if($count==0){
            $got_fullname = ""; 
            $got_phonenumber = ""; 
            $got_email = "";
            $got_city = "";
            $got_town = "";
            $got_strnumber = "";
        }
        
        /*check if there are any products purchased*/
        if( isset($_SESSION['product_cart_id']) && isset($_SESSION['product_cart_id']) ){
        /*show all products*/
        for( $i=0; $i < count($_SESSION['product_cart_id']); $i++ ){
            
                /*show total price of the cart*/
                $product_price = $_SESSION['product_price'][$i];
                $product_size = $_SESSION['product_size'][$i];
                $product_quantity = $_SESSION['product_quantity'][$i];
                $total_price = array_sum($_SESSION['product_price']);
                $total_products_in_cart = count($_SESSION['product_cart_id']);
            
                /*check product table id*/
                $table_id = $_SESSION['product_table_id'][$i];
                if($table_id == 1){
                    $get_product = $this->connection->prepare("
                        SELECT product_id, product_name, product_color, file 
                        FROM product 
                        WHERE product_id = ?
                        " );
                }elseif($table_id == 2){
                    $get_product = $this->connection->prepare("
                        SELECT product_id, product_name, product_color, file 
                        FROM same_product 
                        WHERE product_id = ?
                        ");
                }
                $get_product->bind_param("i", $_SESSION['product_cart_id'][$i]);
                $get_product->execute();
                $get_product->bind_result( $product_id, $product_name, $product_color, $file );
                while ( $get_product->fetch()  ){
                    /*show only products with price > 0*/
                    if($product_price > 0){
                        /*confirm checkout/close sale*/
                        if(isset($_GET['confirm_checkout'])){
                            /*get all products being bought in an array to send them to sales table*/
                            $sold_products .= $product_id.", ";
                            $sold_product_color .= $product_color.", ";
                            $sold_product_size .= $product_size.", ";
                            $sold_product_quantity .= $product_quantity.", ";
                            $payment_holder = "<div id='".$i."session_product_holder' class='feed_holder_middle'>".
                                    "<div  class=''>".
                                        "<h4>"."Personal Information"."</h4>".
                                        "<label>Fullname</label>".
                                        "<input type='text' name='checkout_fullname' id='checkout_fullname' value='".$got_fullname."' class='text_inpu ch' />".
                                        "<label>Phone number</label>".
                                        "<input type='text' name='checkout_phonenumber' id='checkout_phonenumber' value='".$got_phonenumber."' class='text_inpu ch' />".
                                        "<label>Email</label>".
                                        "<input type='email' name='checkout_email' id='checkout_email' value='".$got_email."' class='text_inpu ch' />".
                                    
                                        "<h4>"."Delivery Address"."</h4>".
                                        "<label>City</label>".
                                        "<input type='text' name='checkout_city' id='checkout_city' value='".$got_city."' class='text_inpu' />".
                                        "<label>Town</label>".
                                        "<input type='text' name='checkout_town' id='checkout_town' value='".$got_town."' class='text_inpu' />".
                                        "<label>House number and street name</label>".
                                        "<input type='text' name='checkout_strnumber' id='checkout_strnumber' value='".$got_strnumber."' class='text_inpu' />".
                                        
                                        "<h4>"."Payment"."</h4>".
                                        "<input type='hidden' id='sold_products' value='".$sold_products."'/>".
                                        "<input type='hidden' id='sold_products_price' value='".$total_price."'/>".
                                        "<input type='hidden' id='sold_product_color' value='".$sold_product_color."'/>".
                                        "<input type='hidden' id='sold_product_size' value='".$sold_product_size."'/>".
                                        "<input type='hidden' id='sold_product_quantity' value='".$sold_product_quantity."'/>".
                                        "<label>Debit/Credit Card Number</label>".
                                        "<input type='number' name='checkout_D/C_no' id='checkout_D/C_no' class='text_inpu ch' />".
                                        "<div>Expiry date</div>".
                                        "<div>".
                                            "Month".
                                            "<select>".
                                                "<option>"."01"."</option>".
                                                "<option>"."02"."</option>".
                                                "<option>"."03"."</option>".
                                                "<option>"."04"."</option>".
                                                "<option>"."05"."</option>".
                                                "<option>"."06"."</option>".
                                                "<option>"."07"."</option>".
                                                "<option>"."08"."</option>".
                                                "<option>"."09"."</option>".
                                                "<option>"."10"."</option>".
                                                "<option>"."11"."</option>".
                                                "<option>"."12"."</option>".
                                            "<select/>".
                                            " / ".
                                            "Year".
                                            "<select>".
                                                "<option>"."18"."</option>".
                                                "<option>"."19"."</option>".
                                                "<option>"."20"."</option>".
                                                "<option>"."21"."</option>".
                                                "<option>"."22"."</option>".
                                                "<option>"."23"."</option>".
                                                "<option>"."24"."</option>".
                                            "<select/>".
                                        "</div>".
                                        "<input type='hidden' name='checkout_card_expiry_date' id='checkout_card_expiry_date' class='text_inpu ch' />".
                                        "<br/><label>CVV</label>".
                                        "<input type='number' name='checkout_card_three_no' id='checkout_card_three_no' class='text_inpu ch' />".
                                        "<button class='submit_bt ' onclick='confirm_checkout(\"checkout_fullname\", \"checkout_phonenumber\",
                                            \"checkout_email\", \"checkout_city\", \"checkout_town\", \"checkout_strnumber\", 
                                            \"sold_products\", \"sold_products_price\", \"sold_product_size\", \"sold_product_quantity\", \"checkout_D/C_no\", \"checkout_card_expiry_date\", 
                                            \"checkout_card_three_no\")'>".
                                            "Done".
                                        "</button>".
                                        
                                        "<div id='confirm_payment_output' class='output' style='display:none;'>".
                                           "<span>"."Processing..."."</span>".
                                        "</div>".
                                    
                                    "</div>".
                                "</div>";
                                
                        }else{
                            $payment_holder = "";
                            echo "<div id='".$i."session_product_holder' class='feed_holder_middle'>".
                                    "<div  class='img_holder_small'>".
                                    "<img src='/ecommerce/upload/".$file."' class='img_fit'/>".
                                    "</div>".
                                    "<div>".
                                    $product_name.
                                    "</div>".
                                    " ".
                                    "<div>".
                                    $product_color.
                                    "</div>".
                                    "<div>".
                                    " Size:".
                                    $product_size.
                                    "</div>".
                                    "<div>".
                                    " R".
                                    number_format($product_price, 2).
                                    "</div>".
                                    "<div>".
                                    " Quantity:".
                                    $product_quantity.
                                    "</div>".
                                    "
                                    <input type='hidden' id='".$i."array_position' value='".$i."' />
                                    <input type='hidden' id='".$i."product_price' value='".$product_price."' />
                                    <button onclick='remove_product(\"".$i."array_position\", \"".$i."product_price\")'>REMOVE</button>
                                    <div id='output".$i."'></div>
                                    ".
                                "</div>";
                                $count = $count++;
                        }
                    }
                }
        }
        
            /*Payment holder*/
            echo $payment_holder;
          /*show total price of the cart*/ 
            echo 
                "<div class='total_holder'>".
                    "<input type='hidden' id='total_price_value' value='".$total_price."' />".
                    "<div>"."Total Cost : R "."<span id='total_price'>".number_format($total_price, 2)."</span>"."</div>".
                    "<a href='checkout.php?confirm_checkout=1'>".
                        "<button class='submit_btn btn_100'>"."Confirm Checkout"."</button>".
                    "</a>".
                "</div>";
            /*show product cart number*/
            echo "<button class='checkout_2 round_border' >".
                    "<input type='hidden' id='count_cart_value' value='".$total_products_in_cart."' />".
                    "cart: "."<span id='count_cart'>".$total_products_in_cart."</span>".
                "</button>";
        }else{
            echo "Cart is empty. <a href='index.php'>Purchase here</a>";
        }
        /*close connection*/
        $this->connection->close();
    }
}
