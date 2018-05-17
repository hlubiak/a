<?php

class UserFeed {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function userFeed($feed_id, $purpose_id, $retire_id, $filter, $search){
        
            
            /*feed_id determines if you are fetching all products or not
             * if feed_id is != 0 you put emphasis on the product a user is interacting with,
             * therefore, get only that product
            */
            if($feed_id != "0" && empty($filter) && empty($search)){
                $get_product = (" SELECT * 
                    FROM product
                    WHERE product_name='".$feed_id."' AND retire_id='".$retire_id."'
                    ORDER BY timestamp DESC 
                    ");
            }elseif(!empty($filter) && $filter != "all" && empty($search)){
                $get_product = (" SELECT * 
                    FROM product
                    WHERE retire_id='".$retire_id."' AND category_id='".$filter."'
                    ORDER BY timestamp DESC 
                    ");
            }elseif(!empty($search)){
                /*preg replace the search name
                 * to allow searching where the word appears
                 */
                $prg_search = $this->connection->real_escape_string( $search );
                $prg_search = preg_replace( "/[^A-Za-z0-9 ]/", '', $prg_search );
                $prg_search = "'%".$search."%'";
        
                $get_product = (" SELECT * 
                    FROM product
                    WHERE retire_id='".$retire_id."' AND product_name LIKE $prg_search
                    ORDER BY timestamp DESC 
                    ");
                
            }else{
                $get_product = (" SELECT * 
                    FROM product
                    WHERE retire_id='".$retire_id."'
                    ORDER BY timestamp DESC 
                    ");
            }
            $result_get_product = $this->connection->query( $get_product );
            if( $result_get_product->num_rows > 0 ){
                while($row_get_product = $result_get_product->fetch_assoc()){
                
                $product_id = $row_get_product['product_id'];  
                $category_id = $row_get_product['category_id']; 
                $product_name = $row_get_product['product_name'];
                $product_description = $row_get_product['product_description']; 
                $price = $row_get_product['price'];
                $product_type = $row_get_product['product_type'];
                $weight = $row_get_product['weight'];
                $product_color = $row_get_product['product_color'];
                $brand_id = $row_get_product['brand_id'];
                $gendar = $row_get_product['gendar'];
                $file  = $row_get_product['file'];
                $retire  = $row_get_product['retire_id'];
                
                //get product image
                if( $file == "" ){
                    $img = "<img src='/ecommerce/upload/profile icon.png' class='img_fit' />";
                }else{
                    $img = "<img src='/ecommerce/upload/".$file."' class='img_fit' />";
                }
                    /*check if the product has more same products with different color or style*/
                    $stmt = ( " SELECT product_name
                            FROM same_product 
                            WHERE product_name = '".$product_name."'
                            ORDER BY timestamp DESC    
                            ");
                    $result = $this->connection->query( $stmt );
                    if( $result->num_rows > 0 && $purpose_id != 1){
                        $onclick_product = "href='index.php?product_name=$product_name&&purpose_id=2&&retire_id=0'";
                     }elseif($purpose_id != 1){
                       $onclick_product = "href='index.php?product_name=$product_name&&purpose_id=2&&retire_id=0' onclick='buy(\"".$product_id."feed\", \"".$product_id."img_holder\", \"".$product_id."buy_holder\", \"".$product_id."buy_everything_holder\", \"".$product_id."filter\")'";
                    }
                    
                    /*insert the searched product into search
                     * for statistical purposes
                     */
                    if(!empty($search)){
                        $check_searched_product = (" 
                                SELECT search_no
                                FROM searched_product 
                                WHERE product_id = '".$product_id."'
                                ORDER BY timestamp DESC    
                                ");
                        $result_check_searched_product = $this->connection->query( $check_searched_product );
                        if( $result_check_searched_product->num_rows > 0){
                            while($row_check_searched_product = $result_check_searched_product->fetch_assoc()){
                                /*update search no
                                 * add 1
                                 */
                                $updated_search_no = $row_check_searched_product['search_no']+1;
                                $update_search_no=$this->connection->prepare("
                                    UPDATE searched_product
                                    SET search_no=?
                                    WHERE product_id=?
                                        ");
                                $update_search_no->bind_param("ii",$updated_search_no,$product_id );
                                $update_search_no->execute();
                            }
                        }else{
                          $search_no=1;
                            $insert_searched_product = $this->connection->prepare("
                                INSERT 
                                INTO searched_product ( product_id, search_no) 
                                VALUES ( ?, ? ) 
                                ");
                            $insert_searched_product->bind_param( "ii", $product_id, $search_no );
                            $insert_searched_product->execute();
                        }
                    }
                
                    if($purpose_id == 2){
                        /*create classes for css styling*/
                        $class_1 = "feed_holder_1";
                        $class_2 = "img_holder_1";
                        $class_3 = "product_name_holder_1";
                        $class_4 = "buy_everything_holder_1";
                        $class_5 = "filter_1";
                        $activity_btn = "<input type='submit' id='btn-front' class='btn-front' value='Add to cart'/>"
                                            ;
                        $add_to_cart_btn = "";
                    }
                    else{
                        /*create classes for css styling*/
                        $class_1 = "feed_holder";
                        $class_2 = "img_holder";
                        $class_3 = "product_name_holder";
                        $class_4 = "buy_everything_holder";
                        $class_5 = "filter";
                        $add_to_cart_btn = "<a $onclick_product>".
                                "<button class='submit_btn'  style='width:auto;'>"
                                . "Add to cart"
                                . "</button>".
                                "</a>";
                    }
                /*show products*/    
                echo
                    "<a href='#".$product_id."feed' >".
                        "<div id='".$product_id."feed' class='".$class_1."'>".
                            "<a $onclick_product>"."<div id='".$product_id."img_holder' class='".$class_2."' >".$img."</div>"."</a>".
                            "<div id='".$product_id."buy_holder' class='".$class_3."' style='display:block;'>".
                                "<div id='".$product_id."buy_everything_holder' class='".$class_4."'>".
                                    "<div class='margin_bottom'>".$product_name."</div>".
                                    "<div class='margin_bottom'>"."R ".number_format($price, 2)."</div>".
                                        $add_to_cart_btn.
                                    "<div id='".$product_id."filter' class='".$class_5." hide '>".
                                        "<div class='margin_bottom'>".$product_description."</div>".
                                        "<div class='label'>"."Color: ".$product_color."</div>";
                                    echo "<div class='margin_bottom'></div>";
                                    echo "<div id='btn' >".
                                         "<form action='object.php' method='post'>";
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
                                            "<input type='hidden' id='".$product_id."table_id' value='1'/>".
                                            "<input type='hidden' id='".$product_id."price' value='".$price."'/>".
                                            "<input type='hidden' name='add_to_cart_product_id' value='".$product_id."'/>".
                                            "<input type='hidden' name='add_to_cart_table_id' value='1'/>".
                                            "<input type='hidden' name='add_to_cart_product_price' value='$price'/>".
                                            "<input type='hidden' name='send_id' value='8'/>".
                                                $activity_btn.
                                            "</form>".
                                        "</div>".
                                           "<span id='output".$product_id."'></span>". 
                                    "</div>".
                                "</div>".
                            "</div>".
                        "</div>".
                    "</a>"
                    ;
                }
            }
            
         /*
          * fetch same product if have different styles or colors
        /* /*get the same product with other colors from same product table */
                $get_same_product_1 = $this->connection->prepare("
                SELECT product_id, product_name, product_color, file 
                FROM same_product
                WHERE product_name = ?
                ORDER BY timestamp DESC
                    ");
                $get_same_product_1 ->bind_param("s",$feed_id);
                $get_same_product_1 ->execute();
                $get_same_product_1 ->bind_result( $same_product_id_1, $same_product_name_1, $same_product_color_1, $same_file_1  );
                while($get_same_product_1 ->fetch()){
                    if( $same_file_1  == "" ){
                        $same_img_1  = "<img src='/ecommerce/upload/profile icon.png' class='img_fit' />";
                    }else{
                        $same_img_1 = "<img src='/ecommerce/upload/".$same_file_1 ."' class='img_fit' />";
                    }
                    
                    /*switch purpose id to create activity button for the products
                     * 0 purpose id = activity buttons for users
                     * 1 purpose id = activity button for admin
                     */
                    if($purpose_id == 1){
                        $activity_btn = "<button id='btn' class='class='btn-front' btn'>
                                        <a id='btn-front' onclick=''>UPDATE</a>
                                      </button>".
                                        "<a class='btn_50'>Retire</a>";
                    }else{
                        $activity_btn = "<input type='submit' id='btn-front' class='btn-front' value='A...d to cart'/>";
                    }
                    
                    echo
                        "<a href='#".$product_name.$same_product_id_1."same_product_feed' >".
                            "<div id='".$product_name.$same_product_id_1."same_product_feed' class='feed_holder'>".
                                "<div id='".$same_product_id_1."same_product_img_holder' class='img_holder' onclick='buy(\"".$product_name."".$same_product_id_1 ."same_product_feed\", \"".$same_product_id_1."same_product_img_holder\", \"".$same_product_id_1."same_product_buy_holder\", \"".$same_product_id_1."same_product_buy_everything_holder\", \"".$same_product_id_1."same_product_filter\")'>".$same_img_1."</div>".
                                "<div id='".$same_product_id_1."same_product_buy_holder' class='product_name_holder' style='display:block;'>".
                                    "<div id='".$same_product_id_1."same_product_buy_everything_holder'>".
                                        "<div class='margin_bottom'>".$product_name."</div>".
                                        "<div class='margin_bottom'>"."R ".$price."</div>".
                                        "<div id='".$same_product_id_1."same_product_filter' class='filter hide '>".
                                            "<div class='margin_bottom'>".$product_description."</div>".
                                            "<div class='label'>".$same_product_color_1."</div>";

                                    echo "<form action='object.php' method='post'>";
                                    echo "<div class='label'>"."Choose size"."</div>";
                                    echo "<select name='add_to_cart_product_size' id='".$same_product_id_1."choose_size'>";
                                            $same_weight_array = explode( ',' , $weight );
                                            for( $i= 0; $i < count( $same_weight_array ); $i++ ){
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
                                    echo    "<input type='hidden' id='".$same_product_id_1."product_id_holder' value='".$same_product_id_1."'/>".
                                            "<input type='hidden' id='".$same_product_id_1."table_id' value='2'/>".
                                            "<input type='hidden' id='".$same_product_id_1."price' value='".$price."'/>".
                                            "<input type='hidden' name='add_to_cart_product_id' value='".$same_product_id_1."'/>".
                                            "<input type='hidden' name='add_to_cart_table_id' value='2'/>".
                                            "<input type='hidden' name='add_to_cart_product_price' value='$price'/>".
                                            "<input type='hidden' name='send_id' value='8'/>".
                                            $activity_btn.
                                            "</form>".
                                           "<span id='output".$same_product_id_1."'></span>".
                                        "</div>".
                                    "</div>".
                                "</div>".
                            "</div>".
                        "</a>"
                        ;
                }
            /*close connection and database
             * if its for users on the index page
             * remember purpose id for users is 0 and 2
             */  
            $this->connection->close();
             
    }
}
