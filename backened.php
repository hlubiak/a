<?php

class code {
    
        /*
        * create a constructor to connect to the database
        * this is the first thing i need to be performed hence the constructor
        */
    
        /*create private variable for connection, only to be accessed in this class.*/
	private $con;
        /*
        * this constructor function will take arguments for database
        */
        function __construct( $servername, $username, $password, $dbname ){
			
                /*
                 * create databse connection using mysql improved
                 */
                $this->con = new mysqli( $servername, $username, $password, $dbname );
                /*
                 * check if you are connected else return error
                 */
                if ( $this->con->connect_error ) {
                        die( "Connection failed: " . $this->conn->connect_error )	;
                }
        }
        
    public function login_user( $username, $input_password){

        if (!empty($_POST)) { 

            $statement = mysqli_prepare($this->con,
                    "SELECT user_id, phonenumber, password_key, password
                    FROM users WHERE phonenumber=? OR email=?
                    ");
            mysqli_stmt_bind_param($statement, "ss", $username, $username);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);
            mysqli_stmt_bind_result($statement, $user_id, $phonenumber, $password_key, $password);
            $count = 0;
            while(mysqli_stmt_fetch($statement)){
                $count += 1;
                //encrypt password
                define("PBKDF2_HASH_ALGORITHM", "sha256");
                define("PBKDF2_ITERATIONS", 1000);
                define("PBKDF2_SALT_BYTE_SIZE", 44);
                define("PBKDF2_HASH_BYTE_SIZE", 24);
                define("HASH_SECTIONS", 4);
                define("HASH_ALGORITHM_INDEX", 0);
                define("HASH_ITERATION_INDEX", 1);
                define("HASH_SALT_INDEX", 2);
                define("HASH_PBKDF2_INDEX", 3);
                $iterations = 1000;
                //encrypt password
                $encrypted_password = hash_pbkdf2( "sha512",$password_key, 
                        $input_password, $iterations, 200 );
                
                //check password match
                if($encrypted_password == $password){
                    session_start();
                    $_SESSION['user_id'] = $user_id;
                    header("Location:index.php");
                }else{
                    echo "Sorry! wrong details. login denied.";
                }
                
            }
            mysqli_stmt_close($statement);

        } else{
            echo "Fill in all information. login denied.";;
        }
    }
    
    public function register_user($fullname, $phonenumber, $email, $password ){
         
        if (!empty($_POST['phonenumber']) && !empty($_POST['password'])) {
            
            $statement = mysqli_prepare($this->con,
                    "SELECT user_id
                    FROM users 
                    WHERE phonenumber=? OR email=?
                    ");
            mysqli_stmt_bind_param($statement, "ss", $phonenumber, $email);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);
            mysqli_stmt_bind_result($statement, $user_id);
            if( mysqli_stmt_fetch($statement) ){
                   echo "User already exists, please login"; 
            }else{
                //encrypt password
                define("PBKDF2_HASH_ALGORITHM", "sha256");
                define("PBKDF2_ITERATIONS", 1000);
                define("PBKDF2_SALT_BYTE_SIZE", 44);
                define("PBKDF2_HASH_BYTE_SIZE", 24);
                define("HASH_SECTIONS", 4);
                define("HASH_ALGORITHM_INDEX", 0);
                define("HASH_ITERATION_INDEX", 1);
                define("HASH_SALT_INDEX", 2);
                define("HASH_PBKDF2_INDEX", 3);
                $iterations = 1000;

                $password_key  = base64_encode( mcrypt_create_iv( PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM ) );
                $encrypted_password = hash_pbkdf2( "sha512",$password_key, $password, $iterations, 200 );

                $insertUser = $this->con->prepare("
                        INSERT INTO users ( fullname, email, phonenumber, password_key, password ) 
                        VALUES ( ?, ?, ?, ?, ? ) 
                        ");
                $insertUser->bind_param( "sssss", $fullname, $email, $phonenumber, 
                        $password_key, $encrypted_password );
                if($insertUser->execute() == true){
                       echo "Registration successful"; 
                }else{
                       echo "Error, Registration not successful";
                }
            }
        }else{
            echo "Please fill in all information";
        } 

    }//close register function
        
    /*
    *get website products feed for users
    */
    public function user_feed($feed_id, $purpose_id, $retire_id,  $filter, $search){
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
                $prg_search = $this->con->real_escape_string( $search );
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
            $result_get_product = $this->con->query( $get_product );
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
                    $result = $this->con->query( $stmt );
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
                        $result_check_searched_product = $this->con->query( $check_searched_product );
                        if( $result_check_searched_product->num_rows > 0){
                            while($row_check_searched_product = $result_check_searched_product->fetch_assoc()){
                                /*update search no
                                 * add 1
                                 */
                                $updated_search_no = $row_check_searched_product['search_no']+1;
                                $update_search_no=$this->con->prepare("
                                    UPDATE searched_product
                                    SET search_no=?
                                    WHERE product_id=?
                                        ");
                                $update_search_no->bind_param("ii",$updated_search_no,$product_id );
                                $update_search_no->execute();
                            }
                        }else{
                          $search_no=1;
                            $insert_searched_product = $this->con->prepare("
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
                                    "<div class='margin_bottom'>"."R ".$price."</div>".
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
        /*
                
                /*get the same product with other colors from same product table */
                $get_same_product_1 = $this->con->prepare("
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
             if($purpose_id==0 || $purpose_id==2){   
                $this->con->close();
             }
    }/*close feed function*/ 


    /*
     * This function creates a form to upload products into the database
     * It fetches the product categories and types you created from the database
     */
    public function get_products( $retire_id, $product_id, $action_id, $category_id ){
        /*
         * get product category from the database 
        */
        if($action_id == 1){
            $get_product_category = $this->con->prepare ("
                SELECT product_id, category_name 
                FROM pro_category 
                WHERE product_id=? 
                " );
            $get_product_category->bind_param("i",$category_id);
            $send_id = 11;
        }else{
        $get_product_category = $this->con->prepare ("
                SELECT product_id, category_name 
                FROM pro_category 
                " );
        $send_id = 7;
        }
        $get_product_category->execute();
        $get_product_category->bind_result( $product_category_id, $category_name );
        
        //create a form to be able to submit the product
        if($action_id>0){
            $heading = "<h3>Update the product</h3>";
        }else{
            $heading = "<h3>Upload the product</h3>";
        }
        echo 
            "<form id='upload_product_holder' action='object.php' method='post' enctype='multipart/form-data' class='upload_holder' style='display:block;'>".
                $heading.
            "<label class='label'> Choose Product Category </label>".

            //create a select to select category name options    
            "<select name='product_category_id' class='text_inpu more_height'>";

        //loop through each category
        while ( $get_product_category->fetch()  ){
                //create product category names options to choose which category a product belongs to.
                echo "<option value='".$product_category_id."'>".$category_name."<option/>";
        }
            
        //close select options created
        echo
         "</select>";
        
        
        /*get product info if action is admin update*/
        if($action_id==1){
            $get_product = $this->con->prepare ("
                SELECT product_name, brand_id , product_description, product_color, weight, price
                FROM product 
                WHERE product_id = ? 
                " );
            $get_product->bind_param("i",$product_id);
            $get_product->execute();
            $get_product->bind_result( $product_name, $product_brand_id, $product_description, $product_color, $weight, $price );
            while($get_product->fetch()){}
        }else{
            $product_name = "";
            $product_brand_id = ""; 
            $product_description = ""; 
            $product_color = ""; 
            $weight = ""; 
            $price = "";
        }
        
        /*
         * get the brand name created in the database
         * To determine which brand a product belongs to
         */
        if($action_id==1){
        $get_product_brand = $this->con->prepare("
                SELECT brand_id, brand_name 
                FROM brand 
                WHERE brand_id=?
            ");
        $get_product_brand->bind_param("i",$product_brand_id);
        }else{
        $get_product_brand = $this->con->prepare("
                SELECT brand_id, brand_name 
                FROM brand 
            ");
        }
        $get_product_brand->execute();
        $get_product_brand->bind_result( $brand_id, $brand_name );
            
            //create a select input to create brand name options
            echo 
                "<label class='label'> Choose Brand Name </label>".    
                "<select name='product_brand_name' class='text_inpu more_height'>";
            
            while ( $get_product_brand->fetch() ){
                
                $product_brand_name = $brand_id;
                    //create options to choose brand name
                    echo "<option value='".$product_brand_name."'>".$brand_name."<option/>";
            }
            
            //close the select input 
            echo
                "</select>";
        echo
            
            "</select>".
            "<label class='label'> Product Name </label>".
            "<input type='text' name='product_name' value='".$product_name."' class='text_inpu' />".
            "<label class='label'> Product description </label>".
            "<textarea type='text' name='product_description' value='".$product_description."' class='text_inpu textarea' maxlength='240' >".$product_description."</textarea>".
            "<label class='label'> Product color ( Separated by comma ) </label>".
            "<input type='text' name='product_color' value='".$product_color."' class='text_inpu' />".
            "<label class='label'> Product photo </label>".
            "<input type='file' name='product_photo[]' multiple class='text_inpu' />".
            "<label class='label'> Product size/weight available ( Separated by comma )</label>".
            "<input type='text' name='product_weight' value='".$weight."' class='text_inpu' />".
            "<label class='label'> Product Prices by size/weight above ( Separated by comma ) </label>".
            "<input type='text' name='product_price' value='".$price."' class='text_inpu' />".
                
            "<input type='hidden' name='product_match' value='0' />".
            "<input type='hidden' name='update_product_id' value='".$product_id."' />";
        
        /*
         * get the product type from the database
         */
        $get_product_type = $this->con->prepare("
                SELECT type_id, type_name 
                FROM pro_type " );
        $get_product_type->execute();
        $get_product_type->bind_result( $type_id, $type_name );
            
            /*
            *Create a type select input to create options to choose
            */
            echo 
                "<label class='label'> Choose Product Type </label>".    
                "<select name='product_type_id' class='text_inpu more_height'>";
            
            while ( $get_product_type->fetch() ){
                
                $product_type_id = $type_id;
                
                echo "<option value='".$product_type_id."'>".$type_name."<option/>";
                
            }
          
            echo "</select>";
            
         echo
            "<label class='label'>Gender</label>".
            "<select name='gender' class='text_inpu more_height'>".
                 "<option value='1'>"."Unisex"."</option>".
                 "<option value='2'>"."Women"."</option>".
                 "<option value='3'>"."men"."</option>".
            "</select>".
            "<input type='hidden' name='send_id' value='".$send_id."' />".
            "<input type='hidden' name='action_id' value='".$action_id."' />".
            "<input type='submit' name='upload_product' value='submit' class='submit_btn ' />".
            "</form>";
         
    }
    
    /*
     * The following function give access to the admin option
     * The admin who have access will be able to update data 
     */
    public function login( $access_name, $access_code_input, $new_option_value ){
        
        //data to encrypt the access code 
        
        define("PBKDF2_HASH_ALGORITHM", "sha256");
        define("PBKDF2_ITERATIONS", 1000);
        define("PBKDF2_SALT_BYTE_SIZE", 44);
        define("PBKDF2_HASH_BYTE_SIZE", 24);
        define("HASH_SECTIONS", 4);
        define("HASH_ALGORITHM_INDEX", 0);
        define("HASH_ITERATION_INDEX", 1);
        define("HASH_SALT_INDEX", 2);
        define("HASH_PBKDF2_INDEX", 3);
        $iterations = 1000;

        $check_access_code = $this->con->prepare (" 
                    SELECT admin_id, access_code, access_code_key 
                    FROM admin
                    WHERE access_name = ?
                    ");
        $check_access_code->bind_param( "i",$access_name );
        $check_access_code->execute();
        $check_access_code->bind_result( $admin_id, $access_code, $access_code_key );
        $count = 0;
        while( $check_access_code->fetch() ) {
            $count = $count++;
            //encrypt access code
            $access_code_key1  = base64_encode( mcrypt_create_iv( PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM ) );
            $encrypted_access_code_input = hash_pbkdf2( "sha512",$access_code_key, $access_code_input, $iterations, 200 );
           
            //check if encrypted_access_code_input is equal the access code fetched from the database
            //if they match, give access
            if($encrypted_access_code_input == $access_code){
                /*set admin logged in to session*/
                session_start();
                $_SESSION['admin_id'] = $admin_id;
                //give access to the relevant page admin has access to
                header("Location:products.php?retire_id=0&&product=all&&action=0&&category=all&&option=1");
            }else{
              echo "Access Denied.";  
            }
        }
        //check if it itereted the database table
        if($count == 0){
          echo "Access Denied.,,,";  
        }

        /*close connection and database*/
        $this->con->close();
    }
    
    /*
     * The following function deals with customer users of the website
    */
    public function get_users(){
        //get total number of website users that registered themselves on the website
        $get_sum_users = $this->con->prepare (" 
                    SELECT COUNT(user_id) AS total_customers_registered 
                    FROM users
                    ");
        $get_sum_users->execute();
        $get_sum_users->bind_result( $total_customers_registered );
        while( $get_sum_users->fetch() ) {
            if($total_customers_registered > 0){
                $total = $total_customers_registered;
            }else{
                $total = 0;
            }
        }
        //show total
        echo "<div class='total_div_holder font_24'>".
                strtoupper("Users : ").$total.
            "</div>".
            "<div class='search_customer_holder round_border'>".
                "<input name='customer_search_input' id='customer_search_input' class='search_text_input' placeholder='Search customer' />".
                "<input type='submit' name='submit_search_customer' id='submit_search_customer' value='Search' class='search_submit_btn' onclick='search_customer_name(\"customer_search_input\")'/>".
                "<div id='customer_search_output' class='search_output border' style='display:none;'></div>".
            "</div>";
        
        /*close connection and database*/
        $this->con->close();
    }
    
    /*
     * search customer
     */
    public function customer_search_name( $name ){
        //search users table in the database
        $search = $this->con->real_escape_string( $name );
        $search = preg_replace( "/[^A-Za-z0-9 ]/", '', $search );
        $search = "'%".$name."%'";

        echo $name;

        /*
        * select users according to the selling id
        */
        $stmt = ( " SELECT fullname
                FROM users 
                WHERE fullname LIKE $search " );
        $result = $this->con->query( $stmt );
        if( $result->num_rows > 0 ){
            while ( $row = $result->fetch_assoc() ){
                echo $row['fullname'];
            }
        }else{
            echo "No results found.";
        }
        /*close connection and database*/
        $this->con->close();
    }
    
    /*
    * manage customer users
    */
    public function manage_customer_users($fetch_id){
        
        if( $fetch_id == 1 ){
        //create heding for the data
        echo "<div class='heading'>Manage Customer Users</div>";
        
            $get_users = $this->con->prepare("
                SELECT fullname, email, phonenumber, country, city, town
                FROM users " );
            $get_users->execute();
            $get_users->bind_result( $fullname, $email, $phonenumber, $country, $city, $town );
            $count =0;
            while ( $get_users->fetch() ){

                $count = $count + 1;
                if($fullname != ""){            
                    echo 
                    $fullname.
                    $email.
                    $phonenumber;
                }else{
                    echo 
                    "No users";
                }
            }
            if($count == 0){
                echo "No users.";
            }
        }elseif( $fetch_id == 2 ){
        //create heding for the data
        echo "<div class='heading'>Manage Admin Users</div>";
        
            $get_users = $this->con->prepare("
                SELECT access_name
                FROM admin " );
            $get_users->execute();
            $get_users->bind_result( $access_name );
            $count =0;
            while ( $get_users->fetch() ){

                $count = $count + 1;
                if($access_name != ""){            
                    echo 
                    $access_name;
                }else{
                    echo 
                    "No admin users";
                }
            }
            if($count == 0){
                echo "No admin users.";
            }
        }else{
            echo "Error...";
        }
        
        /*close connection and database*/
        $this->con->close();
    }
    
    /*
     * upload product into the database
     */
    public function upload($upload_id, $value_1, $value_2){
        
        //upload category
        if($upload_id == 2){
            //check if the category exists in the database
            $check_category = $this->con->prepare ("
                    SELECT category_name 
                    FROM pro_category 
                    WHERE category_name = ? 
                    ");
            $check_category->bind_param("s", $value_1);
            $check_category->execute();
            $check_category->bind_result( $category_name );
            $count = 0;
            if($check_category->fetch()){
               $count++; 
            }
            if($count > 0 || empty($value_1)){
                echo "Category already exists";
            }else{
                $insert = $this->con->prepare("
                            INSERT 
                            INTO pro_category ( category_name, selling_category) 
                            VALUES ( ?, ? ) 
                            ");
                $insert->bind_param( "si", $value_1, $value_2 );
                if( $insert->execute() == true){
                    echo "Category uploaded successfully.";
                }else{
                    echo "Error uploading category";
                }
            }
        }//upload type
        elseif($upload_id == 3){
            //check if the category exists in the database
            $check_type = $this->con->prepare ("
                    SELECT type_name 
                    FROM pro_type 
                    WHERE type_name = ? 
                    ");
            $check_type->bind_param("s", $value_1);
            $check_type->execute();
            $check_type->bind_result( $category_name );
            $count = 0;
            if($check_type->fetch()){
               $count++; 
            }
            if($count > 0 || empty($value_1)){
                echo "Type already exists";
            }else{
                $insert = $this->con->prepare("
                            INSERT 
                            INTO pro_type ( type_name ) 
                            VALUES ( ? ) 
                            ");
                $insert->bind_param( "s", $value_1 );
                if( $insert->execute() == true){
                    echo "Type uploaded successfully.";
                }else{
                    echo "Error uploading type";
                }
            }
        }//upload brand
        elseif($upload_id == 4){
            //check if the category exists in the database
            $check_brand = $this->con->prepare ("
                    SELECT brand_name 
                    FROM brand 
                    WHERE brand_name = ? 
                    ");
            $check_brand->bind_param("s", $value_1);
            $check_brand->execute();
            $check_brand->bind_result( $brand_name );
            $count = 0;
            if($check_brand->fetch()){
               $count++; 
            }
            if($count > 0 || empty($value_1)){
                echo "Brand already exists";
            }else{
                $insert = $this->con->prepare("
                            INSERT 
                            INTO brand ( brand_name) 
                            VALUES ( ? ) 
                            ");
                $insert->bind_param( "s", $value_1 );
                if( $insert->execute() == true){
                    echo "brand uploaded successfully.";
                }else{
                    echo "Error uploading brand";
                }
            }
        }
        
    /*close connection and database*/
    $this->con->close();
    }
    
    /*
     *upload product
    */
    public function upload_product($action_id, $product_category_id, $product_brand_id, $product_name, $product_photo, $product_weight, $product_description, $product_color, $product_price, $product_type_id, $gender, $upload_id){
        
        //check if the brand exists in the database
        if($upload_id == 0){
            $check_product = $this->con->prepare ("
                    SELECT product_name 
                    FROM product 
                    WHERE product_name = ? AND product_color = ?
                ");
        }elseif($upload_id == 1){
            $check_product = $this->con->prepare ("
                    SELECT product_name 
                    FROM same_product 
                    WHERE product_name = ? AND product_color = ? 
                ");
        }
        $check_product->bind_param("ss", $product_name, $product_color);
        $check_product->execute();
        $check_product->bind_result( $fetched_product_name );
        $count = 0;
        if($check_product->fetch()){
           $count++; 
        }
        if($count > 0 ){
            echo "Product already exists"."<a href='products.php?retire_id=all&&product=all&&action=0&&category=all&&option=2'><button>"."OK"."</button></a>";
        }else{
            /*
            *check if all fields are not empty
            */
            if(!empty($product_category_id) && !empty($product_brand_id) && !empty($product_name) && !empty($product_photo) && !empty($product_weight) && !empty($product_color) && !empty($product_price) && !empty($product_type_id) && !empty($gender) ){
                
                /*separate product according to different colors or styles*/
                $product_color_array = explode( ',' , $product_color );
                if($upload_id == 1){
                        /*insert  all the products into same_product table in the database
                         * to show the products when a user clicks on the product to purchase
                         */
                        $insert = $this->con->prepare("
                            INSERT 
                            INTO same_product ( product_name, product_color, file ) 
                            VALUES ( ?, ?, ? ) 
                            ");
                        $insert->bind_param( "sss", $product_name, $product_color, $product_photo );
                        $insert->execute();
                }elseif($upload_id == 0){
                
                    /*insert only first product to the product table in database
                     * to show only one product on the website
                     * since its the same product just with different colors
                     * show other colors when a user clicks on the product
                     */
                    $insert = $this->con->prepare("
                                INSERT 
                                INTO product ( category_id, product_name, price, product_type, weight, product_description, product_color, brand_id, gendar, file ) 
                                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ) 
                                ");
                    $insert->bind_param( "ssssssssss", $product_category_id, $product_name, $product_price, $product_type_id, $product_weight, $product_description, $product_color, $product_brand_id, $gender, $product_photo );
                    if( $insert->execute() == true){
                        echo "product uploaded successfully."."<a href='products.php?retire_id=all&&product=all&&action=0&&category=all&&option=2'><button>"."OK"."</button></a>";;
                    }else{
                        echo "Error uploading product";
                    }
                }else{
                    echo "error uploading.";
                }
            }else{
               echo "Please fill in all fields."."<a href='upload.php'><button>"."OK"."</button></a>";
                
            }
            
        }
        
        /*close connection and database*/
        $this->con->close();
    }
    
    /*
     * create menu
    */
    public function menu(){
        echo "
                <ul class='menu_ul'>
                    <a href='#'><li>About us</li></a>
                    <a href='#'><li>How to buy</li></a>
                    <a href='#'><li>Terms and Conditions</li></a>
                    <a href='#'><li>Who we are</li></a>
                </ul>
                <ul class='menu_ul'>
                    <a href='#'><li>My Account</li></a>
                    <a href='#'><li>Buy</li></a>
                    <a href='#'><li>Track my sales</li></a>
                    <a href='#'><li>Contact us</li></a>
                </ul>
            ";
    }
    
    /*add to cart function
     * send product data to ethe databa
     */
    public function add_to_cart($product_id, $table_id, $product_size, $product_price, $product_quantity){
        
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
            
            echo    "<a href='checkout.php'>".
                        "<button class='btn_50'>Checkout</button>".
                    "</a>".
                    "<a href='index.php'>".
                        "<button class='btn_50 grey' onclick='buy(\"".$product_id."feed\", \"".$product_id."img_holder\", \"".$product_id."buy_holder\", \"".$product_id."buy_everything_holder\", \"".$product_id."filter\")'>".
                            "Continue Shopping".
                        "</button>".
                    "</a>"
                    ;
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
            
            echo    "<a href='checkout.php'>".
                        "<button class='btn_50'>Checkout</button>".
                    "</a>".
                    "<button class='btn_50 grey' onclick='buy(\"".$product_id."feed\", \"".$product_id."img_holder\", \"".$product_id."buy_holder\", \"".$product_id."buy_everything_holder\", \"".$product_id."filter\")'>"
                    . "Continue Shopping"
                    ."</button>";
        
        }
        
    }
    /*index sessio*/
    public function checkout(){
        /*Start session to get products in session*/
        session_start();
        
        $count = 0;
        $sold_products =null;
        $sold_product_size =null;
        $sold_product_color =null;
        $sold_product_quantity =null;
        /*check if the user is logged in */
        if(isset($_SESSION['user_id'])){
            $get_user_info = $this->con->prepare("
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
            
                /*check product table id*/
                $table_id = $_SESSION['product_table_id'][$i];
                if($table_id == 1){
                    $get_product = $this->con->prepare("
                        SELECT product_id, product_name, product_color, file 
                        FROM product 
                        WHERE product_id = ?
                        " );
                }elseif($table_id == 2){
                    $get_product = $this->con->prepare("
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
                                    $product_price.
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
                                $count = $count + 1;
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
                    "<div>"."Total Cost : R "."<span id='total_price'>".$total_price."</span>"."</div>".
                    "<a href='checkout.php?confirm_checkout=1'>".
                        "<button class='submit_btn btn_100'>"."Confirm Checkout"."</button>".
                    "</a>".
                "</div>";
            /*show product cart number*/
            echo "<button class='checkout_2 round_border' >".
                    "<input type='hidden' id='count_cart_value' value='".$count."' />".
                    "cart: "."<span id='count_cart'>".$count."</span>".
                "</button>";
        }else{
            echo "Cart is empty. <a href='index.php'>Purchase here</a>";
        }
        /*close connection*/
        $this->con->close();
    }
    
    /*
    *remove a product from cart
    *destroy the session
    */
    public function remove_product( $product_array_position ){
        session_start();
        if( isset($_SESSION['product_cart_id']) && isset($_SESSION['product_table_id']) ){
            $_SESSION['product_price'][$product_array_position] = 0; 
        }else{
            echo "Error occured. Product not removed.";
        }
    }
    
    /*show number of cart products*/
    public function number_of_cart_products(){
        $count=0;
        if(isset($_SESSION['product_cart_id'])){
            for( $i=0; $i < count($_SESSION['product_cart_id']); $i++ ){
                if($_SESSION['product_price'][$i] > 0){
                    $count = $count + 1;
                }
            }
            return "Cart: ".$count;
        }else{
            return 0;
        }
    }
    
    /*restrict admin panel only to logged in admin*/
    public function restrict_to_loggedin_admin(){
        session_start();
        if(isset($_SESSION['admin_id'])){
        }else{
           header("Location:adminpanel.php"); 
        }
    }
    
    /*get products for the marketing introduction div
     * the product may show in slides
     * Just show products categories that the website is selling
     * you can show products such as new product line, hot selling product, product you think users would want to buy
     */
    public function marketing_product(){
        /*
        * get product category from the database 
        */
        $stmt = ( " SELECT product_id, category_name
                FROM pro_category 
                ORDER BY timestamp DESC    
                ");
        $result = $this->con->query( $stmt );
        while ( $row = $result->fetch_assoc()  ){
            /*get one product for each category*/
            $get_product = $this->con->prepare ("
                SELECT product_id, file 
                FROM product 
                WHERE category_id = ?
                LIMIT 1
                " );
            $get_product->bind_param("i", $row['product_id']);
            $get_product->execute();
            $get_product->bind_result( $product_id, $file );
            $output = 0;
            while ( $get_product->fetch()  ){
                /*get one product for each category*/
                echo "<div class='mySlides fade'>".
                            "<div class='marketing_product_text'>"."<h1>".strtoupper($row['category_name'])."</h1>"."</div>".
                            "<img src='/ecommerce/upload/".$file."' class='img_fit'>".
                          "</div>";
            }
           
        }
    }
    
    /*confirm payment*/
    public function confirm_checkout($fullname, $phonenumber, $email, $city, $town, $strnumber, $sold_products, $amount, $size, $quantity){
        /*Create a form to send the cart informtion
        *After a user creates or confirms their password
        */
        echo "<form action='object.php' method='post'>".
                "<input type='hidden' name='phonenumber' value='".$phonenumber."'/>".
                "<input type='hidden' name='fullname' value='".$fullname."'/>".
                "<input type='hidden' name='email' value='".$email."'/>".
                "<input type='hidden' name='city' value='".$city."'/>".
                "<input type='hidden' name='town' value='".$town."'/>".
                "<input type='hidden' name='strnumber' value='".$strnumber."'/>".
                "<input type='hidden' name='product_id' value='".$sold_products."'/>".
                "<input type='hidden' name='amount' value='".$amount."'/>".
                "<input type='hidden' name='size' value='".$size."'/>".
                "<input type='hidden' name='quantity' value='".$quantity."'/>".
                "<input type='hidden' name='send_id' value='15'/>";
        /*insert user personal infomation into user table*/
        $check_user_exists = $this->con->prepare ("
                SELECT user_id, phonenumber, email
                FROM users 
                WHERE phonenumber=? OR email=?
                " );
        $check_user_exists->bind_param("ss",$phonenumber, $email);
        $check_user_exists->execute();
        $check_user_exists->bind_result( $user_id, $phonenumber, $email );
        if($check_user_exists->fetch()){
                /*confirm users*/
           echo  "Check Password".
                "<input type='hidden' name='user_id' value='".$user_id."'/>".
                "<input type='text' name='password' />".
                "<input type='submit' name='close_sale' value='Continue' />".
                "</form>";  
        }else{
          echo  "Create Password".
                "<input type='hidden' name='user_id' value=''/>".
                "<input type='text' name='password' />".
                "<input type='submit' name='close_sale' value='Continue' />".
                "</form>";
        }
        /*close database*/
        $this->con->close();
    }
    
    /*create options for the admin panel*/
    public function choose_option($option, $toggle){
        switch($option){
           case 1:
               /*display*/
               if($toggle==1){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 2:
               /*display*/
               if($toggle==2){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 3:/*display*/
               if($toggle==3){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 4:/*display*/
               if($toggle==4){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 5:/*display*/
               if($toggle==5){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           default:
               break;
        }
    }
    
    /*suggest products to user
     * based on what a user has purchased
    */
    public function suggest_product($purchased_product_id){
                
        /*get info on the product purchased*/
        $get_purchased_product_info = $this->con->prepare("
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
        $get_suggested_product = $this->con->prepare("
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
                                            <div id='btn-front' class='btn-front' onclick='add_to_cart(\"".$product_id."product_id_holder\",\"".$product_id."table_id\", \"".$product_id."choose_size\", \"".$product_id."price\")'>Add to cart....</div>
                                          </div>";
                    
                    echo
                        "<a href='#".$product_name.$product_id."same_product_feed' >".
                            "<div id='".$product_name.$product_id."same_product_feed' class='feed_holder'>".
                                "<div id='".$product_id."same_product_img_holder' class='img_holder' onclick='buy(\"".$product_name."".$product_id ."same_product_feed\", \"".$product_id."same_product_img_holder\", \"".$product_id."same_product_buy_holder\", \"".$product_id."same_product_buy_everything_holder\", \"".$product_id."same_product_filter\")'>".$img."</div>".
                                "<div id='".$product_id."same_product_buy_holder' class='product_name_holder' style='height:20%;'>".
                                    "<div id='".$product_id."same_product_buy_everything_holder'>".
                                        "<div class='margin_bottom'>".$product_name."</div>".
                                        "<div class='margin_bottom'>"."R ".$price."</div>".
                                        "<div id='".$product_id."same_product_filter' class='filter hide '>".
                                            "<div class='margin_bottom'>".$product_description."</div>".
                                            "<div class='label'>".$product_color."</div>";

                                        echo "<div class='margin_bottom'></div>";

                                        echo    "<div class='label'>"."Choose size"."</div>";
                                                    $same_weight_array = explode( ',' , $weight );
                                                    for( $i= 0; $i < count( $same_weight_array ); $i++ ){
                                                        echo "<input type='checkbox' id='".$product_id."choose_size' value='".$same_weight_array[$i]."'/>".$same_weight_array[$i]."<br/>";
                                                    }
                                        //create hidden inputs to hold and send data of the product sent
                                        echo "<input type='hidden' id='".$product_id."product_id_holder' value='".$product_id."'/>";
                                        echo "<input type='hidden' id='".$product_id."table_id' value='2'/>";
                                        echo "<input type='hidden' id='".$product_id."price' value='".$price."'/>";
                                        echo $activity_btn.
                                           "<span id='output".$product_id."'></span>".
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
                $this->con->close();
    }
    
    /*update product data*/
    public function update_product($update_product_id, $product_category_id, $product_brand_id, $product_name, $product_photo, $product_weight, $product_description, $product_color, $product_price, $product_type_id, $gender){
        /*check if product update is not empty*/
        if(!empty($product_photo)){
            $update_product = $this->con->prepare("
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
        $this->con->close();
    }/*close update product function*/
    
    /*retire product*/
    public function retire_product($product_id){
        $update_product = $this->con->prepare("
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
        $this->con->close();
    }/*close retire function*/
    
    
    /*get sales*/
    public function sales($data){
        /*get timestamp of the sale
            *to check which year and month a sale was 
        */
        //sales that are paid/closed
        $paid = 1;
        $get_sale_timestamp = $this->con->prepare("
            SELECT amount,timestamp 
            FROM sales
            WHERE paid= ?
            ORDER BY timestamp DESC
        ");
        $get_sale_timestamp->bind_param("i",$paid);
        $get_sale_timestamp->execute();
        $get_sale_timestamp->bind_result( $amount, $timestamp );
        /*get current date, year*/
        $current_yr = date("Y");
        //initialise total sales
        $total_sales_current_yr=0;
        $total_sales_last_yr = 0;
        $total_sales_lastoflast_yr = 0;
        while($get_sale_timestamp->fetch()){
            $arr_timestamp = explode( '-' , $timestamp );
            for( $i= 0; $i < count($arr_timestamp); $i++ ){
                    //current year
                    if($arr_timestamp[$i]==$current_yr){
                        $this_yr = $arr_timestamp[$i];
                        $total_sales_current_yr += $amount;
                    }
                    //previous year
                    if(($arr_timestamp[$i])==($current_yr-1)){
                        $last_yr = $arr_timestamp[$i];
                        $total_sales_last_yr += $amount;
                    }
                    
                    //the year before last
                    if(($arr_timestamp[$i])==($current_yr-2)){
                        $ybl_yr = $arr_timestamp[$i];
                        $total_sales_lastoflast_yr += $amount;
                    }
            }
        }
        if($total_sales_current_yr){
            $total_sales_current_yr=$total_sales_current_yr;
        }else{
            $total_sales_current_yr=0;
            $this_yr = $current_yr;
        } 
        if($total_sales_last_yr){
            $total_sales_last_yr=$total_sales_last_yr;
        }else{
             $total_sales_last_yr=0;
             $last_yr= $current_yr-1;
        }
        if($total_sales_lastoflast_yr){
            $total_sales_lastoflast_yr=$total_sales_lastoflast_yr;
        }else{
            $total_sales_lastoflast_yr=0;
            $ybl_yr= $current_yr-2;
        }
        
        $total_all = $total_sales_current_yr + $total_sales_last_yr + $total_sales_lastoflast_yr;
        
        /*Draw linegraph for the first three years*/
        if($data=="total"){
           echo $this_yr." R".$total_sales_current_yr." ";echo "<br/>";
            echo $last_yr." R".$total_sales_last_yr." ";
            echo "<br/>";
            echo $ybl_yr." R".$total_sales_lastoflast_yr." ";
            echo "<br/>";
        }elseif($data=="labels"){
            echo "".$ybl_yr."".","."".$last_yr."".","."".$this_yr."";
        }elseif($data=="data"){
            echo $total_sales_lastoflast_yr.",".$total_sales_last_yr.",".$total_sales_current_yr;
        }
    }
    
    /*filter products by category*/
    public function filter_products_by_category(){
        
        echo 
            //create a select to select category name options    
            "<div name='product_category_id' class='text_inpu more_height'>".
                "<a href='index.php?filter=all'>".
                    "<button class='sub_sub_option_btn' >"."All"."</button>".
                "</a>";
        $get_product_category = $this->con->prepare ("
                SELECT product_id, category_name 
                FROM pro_category 
                " );
        $get_product_category->execute();
        $get_product_category->bind_result( $product_category_id, $category_name );
        //loop through each category
        while ( $get_product_category->fetch()  ){
                //create product category names options to choose which category a product belongs to.
                echo "<a href='index.php?filter=$product_category_id'>".
                        "<button class='sub_sub_option_btn' >".$category_name."</button>".
                    "</a>";
        }
        echo "</div>";
    }
    
    /*filter products by category*/
    public function filter_products_by_category_for_search(){
        
        echo 
            //create a select to select category name options
            "<form action='object.php' method='POST'>".
            "<label>"."Choose category to search"."<label>".
            "<select name='search_category_id' id='search_category_id' class='text_inpu more_height'>".
                "<a href='index.php?filter=all'>".
                    "<button class='sub_sub_option_btn' >"."All"."</button>".
                "</a>";
        $get_product_category = $this->con->prepare ("
                SELECT product_id, category_name 
                FROM pro_category 
                " );
        $get_product_category->execute();
        $get_product_category->bind_result( $product_category_id, $category_name );
        //loop through each category
        while ( $get_product_category->fetch()  ){
                //create product category names options to choose which category a product belongs to.
                echo "<option value='".$product_category_id."' class='sub_sub_option_btn' >".$category_name."</option>";
        }
        echo "</select>";
        
        echo "<div class='search_index_holder round_border'> 
                    <input type='text' name='product_search_input' id='product_search_input' class='index_text_input' placeholder='Search customer' />
                    <input type='submit' name='submit_search_customer' id='submit_search' value='Search' class='search_submit_btn' onclick='search_product(\"search_category_id\", \"product_search_input\")'/>
                    <input type='hidden' name='send_id' id='send_id' value='5'/>
                    <div id='customer_search_output' class='search_output border' style='display:none;'></div>
            </div>".
            "</form>";
    }
    
    /*Search product*/
    public function search_product($search_name, $category){
        if(!empty($_POST['search_name'])){
            
            $search = $this->con->real_escape_string( $search_name );
            $search = preg_replace( "/[^A-Za-z0-9 ]/", '', $search );
            $search = "'%".$search_name."%'";
            
            $get_product = $this->con->prepare ("
                SELECT product_name 
                FROM product 
                WHERE category_id=? AND product_name LIKE $search
                LIMIT 1
                " );
            $get_product->bind_param("i", $category);
            $get_product->execute();
            $get_product->bind_result( $product_name );
            $count = 0;
            while ($get_product->fetch()){
                $count++;
                echo $product_name;
                header("Location:index.php?filter=$category");
            }
            if($count==0){
             echo "Nothing was found";   
            }
            
        }else{
          echo "Nothing was found..."; 
        }
        
    }//close search product
    
    //most selling products
    public function most_selling_products(){
        $paid=1;
        $get_sold_product = $this->con->prepare ("
                SELECT product_id 
                FROM sales 
                WHERE paid=?
                ");
            $get_sold_product->bind_param("i", $paid);
            $get_sold_product->execute();
            $get_sold_product->bind_result( $product_id );
            $sold_product_id=null;
            echo "<br/>";
            $count = 0;
            $initial_value=0;
            while ($get_sold_product->fetch()){
                $sold_product_id .= $product_id;
                $arr_sold_products = explode(",", $sold_product_id);
                $total_products_sold = count($arr_sold_products).'<br/>';
                $counts = array_unique($arr_sold_products);
            }
            print_r($counts);
            echo "".
                "<h4>"."Products sold: ".$total_products_sold."</h4>".
                "";
    }
    
    /*users analytics*/
    public function users_analytics(){
        /*calculate total website users/registered*/
        $remove = 0;
        $get_users = $this->con->prepare ("
            SELECT user_id,fullname, email, phonenumber 
            FROM users
            WHERE remove=?
            ");;
        $get_users->bind_param("s",$remove);
        $get_users->execute();
        $get_users->bind_result( $user_id, $fullname, $email, $phonenumber );
        $total_users=null;
        while ($get_users->fetch()){
            $total_users += count($fullname);
            echo "<div id='".$user_id."remove_user_holder' class='feed_holder_middle user_feed_holder'>".
                    "<div>".$fullname."</div>".
                    "<div>".$email."</div>".
                    "<div>".$phonenumber."</div>".
                    "<button class='' onclick='remove_user()'>Message</button>".
                    "<input type='hidden' id='".$user_id."value' value='".$user_id."'/>".
                    "<button class='' onclick='remove_user(\"".$user_id."value\", \"total_user_value\")'>REMOVE</button>".
                "</div>";
        }
        
        echo "<div class='total_holder total_holder_users'>".
                "<input type='hidden' id='total_user_value' value='".$total_users."'/>".
                "<h3>Total users".": "."<span id='total_user_value_show' class='heading'>".$total_users."</span>"."</h3>".
            "</div>";
    }
    
    /*remove user*/
    public function remove_user($user_id){
        $remove = 1;
        $remove_user = $this->con->prepare("
                        UPDATE users 
                        SET remove=? 
                        WHERE user_id=? 
                        ");
            $remove_user->bind_param( "ii", $remove, $user_id );
            if( $remove_user->execute() == true){
            }
    }
    
    /*get invoices*/
    public function invoices($paid_invoice, $unpaid_invoice){
        $get_invoice = ("
            SELECT sale_id, user_id,amount, invoice_number 
            FROM sales
            WHERE paid='".$paid_invoice."'
            ORDER BY timestamp DESC
            ");
        $result_get_invoice = $this->con->query( $get_invoice );
        if( $result_get_invoice->num_rows > 0 ){
            $total_invoice=null;
            $total_invoice_amount=0;
            while ( $row_get_invoice = $result_get_invoice->fetch_assoc() ){
            $sale_id=$row_get_invoice['sale_id'];
            $user_id=$row_get_invoice['user_id'];
            $amount=$row_get_invoice['amount'];
            $invoice_number=$row_get_invoice['invoice_number'];
            /*get users for the invoices*/
            $stmt = (" SELECT fullname, email, phonenumber
                FROM users
                WHERE user_id='".$user_id."' OR phonenumber='".$user_id."' OR email='".$user_id."'
                ORDER BY timestamp DESC
                ");
            $result = $this->con->query( $stmt );
            if( $result->num_rows > 0 ){
                while ( $row = $result->fetch_assoc() ){
                    $fullname=$row['fullname'];
                    $email=$row['email'];
                    $phonenumber=$row['phonenumber'];
                }
            }else{
                $fullname="";
                $email="";
                $phonenumber="";
            }
            
            $total_invoice += count($invoice_number);
            $total_invoice_amount += $amount;
            echo "<div id='".$sale_id."remove_user_holder' class='feed_holder_middle user_feed_holder'>".
                    "<div>"."Invoice NO: ".$invoice_number."</div>".
                    "<div>"."R ".$amount."</div>".
                    "<div>".$fullname."</div>".
                    "<div>".$email."</div>".
                    "<div>".$phonenumber."</div>".
                    "<button class='' onclick='remove_user()'>Message</button>".
                    "<input type='hidden' id='".$sale_id."value' value='".$sale_id."'/>".
                    "<button class='' onclick='remove_user(\"".$sale_id."value\", \"total_user_value\")'>REMOVE</button>".
                "</div>";
        }
        echo "<div class='total_holder total_holder_users'>".
                "<input type='hidden' id='total_user_value' value='".$total_invoice_amount."'/>".
                "<h5>Total invoices".": "."<span id='total_user_value_show' class='heading'>".$total_invoice."</span>"."</h5>".
                "<h5>Amount".": R "."<span id='total_user_value_show' class='heading'>".$total_invoice_amount."</span>"."</h5>".
            "</div>";
        }
    }
    
    /*logout*/
    function logout(){
        session_start();
        session_destroy();
        header('Location: index.php');
    }//close logout//
    
    /*insert sale*/
    public function insert_sale($user_id, $fullname, $phonenumber, $email, $password, $city, $town, $strnumber, $sold_products, $amount, $size, $quantity){
        /*check if a user is already registered
        *if not, compare registered password and entered password to confirm identity
         *else register a user as a new user
        */
        
        //for encrypting password
        define("PBKDF2_HASH_ALGORITHM", "sha256");
        define("PBKDF2_ITERATIONS", 1000);
        define("PBKDF2_SALT_BYTE_SIZE", 44);
        define("PBKDF2_HASH_BYTE_SIZE", 24);
        define("HASH_SECTIONS", 4);
        define("HASH_ALGORITHM_INDEX", 0);
        define("HASH_ITERATION_INDEX", 1);
        define("HASH_SALT_INDEX", 2);
        define("HASH_PBKDF2_INDEX", 3);
        $iterations = 1000;
               
        if($user_id != ""){
            /*get register passowrd*/
            $get_password=$this->con->prepare("
                SELECT password, password_key 
                FROM users
                WHERE user_id=?
                    ");
            $get_password->bind_param("i",$user_id);
            $get_password->execute();
            $get_password->bind_result($fetched_password, $password_key);
            while($get_password->fetch()){
                $encrypted_password = hash_pbkdf2( "sha512",$password_key, $password, $iterations, 200 );
                if($encrypted_password == $fetched_password){
                    /*enter sale*/
                    $confirmed_identity = 1;
                }else{
                    echo "Sorry! Identity not confirmed. sale failed.";
                    $confirmed_identity = 0;
                }                
            }
        }else{
            //encrypt password
            $password_key  = base64_encode( mcrypt_create_iv( PBKDF2_SALT_BYTE_SIZE, MCRYPT_DEV_URANDOM ) );
            $encrypted_password = hash_pbkdf2( "sha512",$password_key, $password, $iterations, 200 );
            $insert = $this->con->prepare("
                        INSERT 
                        INTO users ( fullname, phonenumber, email, password, password_key, city, town, strnumber ) 
                        VALUES ( ?, ?, ?, ?, ?, ?, ?, ? ) 
                        ");
            $insert->bind_param( "ssssssss", $fullname, $phonenumber, $email, $encrypted_password, $password_key, $city, $town, $strnumber );
            if( $insert->execute() == true){
                $confirmed_identity = 2;
            }else{
                $confirmed_identity = 0;
            }
        }
        
        /*insert sale*/
        if($confirmed_identity == 1 || $confirmed_identity == 2){
                $paid = 1;
                $invoice_number = mt_rand( 100000, 999999 );
                $invoice_number .=  mt_rand( 0, 2 ).$phonenumber;
                
                $insert_sale = $this->con->prepare("
                        INSERT 
                        INTO sales ( product_id, user_id, amount, paid, invoice_number, size, quantity ) 
                        VALUES ( ?, ?, ?, ?, ?, ?, ? ) 
                        ");
                $insert_sale->bind_param( "sssisss", $sold_products, $phonenumber, $amount, $paid, $invoice_number, $size, $quantity );
                if( $insert_sale->execute() == true){
                    echo "Purchase is successful. Expect your delivery. <a href='index.php'>OK</a>";
                    /*unset products from session*/
                    session_start();
                    unset($_SESSION['product_cart_id']);
                    unset($_SESSION['product_table_id']);
                    unset($_SESSION['product_price']);
                }
                
        }else{
            echo "Sorry. something went wrong. Purchase unsuccessful.";
        }
        //close connection
        $this->con->close();
    }/*close insert sale function*/
    
    /*restrict access to the logged user*/
    public function loggedin(){
        session_start();
        if(isset($_SESSION['user_id'])){
        }else{
            header("Location:index.php");
        }
    }
    
    /*start session*/
    public function start_session(){
       session_start(); 
    }
    
    /*create myaccount activities
     * update personal information
     * checking unreceived invoices
    */
    public function myaccount(){
        if(isset($_SESSION['user_id'])){
            echo "<button onclick='toggle_one(\"myprofile_activity_holder\")'>".
                    "My profile".
                 "</button>";
            echo "<div id='myprofile_activity_holder' style='display:none;'>".
                    "<div class='space'>".
                        "<a href='profile.php'>Update profile</a>".
                    "</div>".
                    "<div class='space'>".
                        "<a href='invoice.php'>Invoice</a>".
                    "</div>".
                    "<form action='object.php' method='post' class='space'>".
                        "<input type='submit' name='logout' value='Logout'/>".
                        "<input type='hidden' name='send_id' value='14'/>".
                    "</form>".
                 "</div>";
            
        }else{
            echo "<div class='my_account_click' onclick='toggle(\"what_todo_input_div\", \"new_option_value\", \"new_option_value\")'>My account</div>";
        }
    }
    
    /*get profile*/
    public function get_profile($loggedin_user_id){
        $get_profile=$this->con->prepare("
            SELECT fullname, phonenumber, email, city, town, strnumber 
            FROM users
            WHERE user_id=?
                ");
        $get_profile->bind_param("i",$loggedin_user_id);
        $get_profile->execute();
        $get_profile->bind_result($fullname, $phonenumber, $email, $city, $town, $strnumber);
        while($get_profile->fetch()){
            
            echo 
                "<h3>"."Update Name and Contacts"."</h3>".
                "<div>"."Fullname"."</div>".
                "<input type='text' id='fullname' value='".$fullname."'/>".
                "<div>"."Phonenumber"."</div>".
                "<input type='text' id='phonenumber' value='".$phonenumber."'/>".
                "<div>"."Email"."</div>".
                "<input type='text' id='email' value='".$email."'/>".
                "<button id='update_name_contact_btn' onclick='update_name_contact()'>"."Update info"."</button>";
            echo 
                "<h3>"."Update Address"."</h3>".
                "<div>"."City"."</div>".
                "<input type='text' id='city' value='".$city."'/>".
                "<div>"."town"."</div>".
                "<input type='text' id='town' value='".$town."'/>".
                "<div>"."house number and street"."</div>".
                "<input type='text' id='strnumber' value='".$strnumber."'/>".
                "<button id='update_address_btn' onclick='update_address()'>"."Update address"."</button>";
            echo 
                "<h3>"."Update Password"."</h3>".
                "<div>"."Existing Password"."</div>".
                "<input type='text' id='current_password' />".
                "<div>"."New Password"."</div>".
                "<input type='text' id='new_password' />".
                "<button id='update_password_btn' onclick='update_password()'>"."Update password"."</button>";             
             
        }
    }
    
    /*update names*/
    public function update_name_contact($loggedin_user_id, $fullname,  $phonenumber,$email){
        $update = $this->con->prepare("
            UPDATE users 
            SET fullname=?, phonenumber=?, email=? 
            WHERE user_id=? 
            ");
        $update_success=0;
        $update->bind_param( "sssi", $fullname, $phonenumber, $email, $loggedin_user_id );
        if( $update->execute() == true){
            $update_success=1;
            echo "Updated";
        }
        /*update sales to keep users invoices relevant to their new info*/
        if($update_success=1){
            $update_sales = $this->con->prepare("
            UPDATE sales 
            SET user_id=? 
            WHERE user_id=? OR user_id=? OR user_id=?
            ");
            $update_sales->bind_param( "issi",$loggedin_user_id, $phonenumber, $email, $loggedin_user_id );
            $update_sales->execute();
        }
    }
    
    /**update address*/
    public function update_address($loggedin_user_id, $city, $town,$strnumber){
        $update = $this->con->prepare("
            UPDATE users 
            SET city=?, town=?, strnumber=? 
            WHERE user_id=? 
            ");
        $update->bind_param( "sssi", $city, $town, $strnumber, $loggedin_user_id );
        if( $update->execute() == true){
            echo "Updated";
        }
    }

    /*update password*/    
    public function update_password($loggedin_user_id, $current_password,  $new_password){
        //get registered encrypted password
        $statement = mysqli_prepare($this->con,
                    "SELECT password, password_key
                    FROM users WHERE user_id=?
                    ");
            mysqli_stmt_bind_param($statement, "i", $loggedin_user_id);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);
            mysqli_stmt_bind_result($statement, $password, $password_key);
            $count = 0;
            while(mysqli_stmt_fetch($statement)){
                $count += 1;
                //encrypt password
                define("PBKDF2_HASH_ALGORITHM", "sha256");
                define("PBKDF2_ITERATIONS", 1000);
                define("PBKDF2_SALT_BYTE_SIZE", 44);
                define("PBKDF2_HASH_BYTE_SIZE", 24);
                define("HASH_SECTIONS", 4);
                define("HASH_ALGORITHM_INDEX", 0);
                define("HASH_ITERATION_INDEX", 1);
                define("HASH_SALT_INDEX", 2);
                define("HASH_PBKDF2_INDEX", 3);
                $iterations = 1000;
                //encrypt password
                $current_encrypted_password = hash_pbkdf2( "sha512",$password_key, 
                        $current_password, $iterations, 200 );
                //create a new password
                $new_encrypted_password= hash_pbkdf2( "sha512",$password_key, 
                        $new_password, $iterations, 200 );
                //check password match
                if($current_encrypted_password == $password){
                    //update password
                    $update = $this->con->prepare("
                        UPDATE users 
                        SET password=? 
                        WHERE user_id=? 
                        ");
                    $update->bind_param( "si", $new_encrypted_password, $loggedin_user_id );
                    if( $update->execute() == true){
                        echo "Updated";
                    }else{
                        echo "error";
                    }
                }else{
                    echo "Denied.";
                }
                
            }
            mysqli_stmt_close($statement);
            //close connection to database
            $this->con->close();

    }
    
    /*get user invoices*/
    public function user_invoice($loggedin_user_id){
        $get_user_id = $this->con->prepare("
        SELECT phonenumber, email 
        FROM users
        WHERE user_id = ?
            ");
        $get_user_id->bind_param("i",$loggedin_user_id);
        $get_user_id->execute();
        $get_user_id->bind_result( $phonenumber, $email  );
        while($get_user_id->fetch()){}
        /*get invoices*/
        $get_invoice = $this->con->prepare("
        SELECT invoice_number, amount, timestamp 
        FROM sales
        WHERE user_id = ? OR user_id = ? OR user_id = ?
            ");
        $get_invoice->bind_param("iss",$loggedin_user_id, $phonenumber, $email);
        $get_invoice->execute();
        $get_invoice->bind_result( $invoice_number, $amount, $timestamp  );
        while($get_invoice->fetch()){
           echo "<div  class='feed_holder_middle' style='left:0%;'>".
                "<div>".
                    "Invoice NO: ".
                $invoice_number.
                "</div>".
                " ".
                "<div>".
                    "Cost: R".
                $amount.
                "</div>".
                "<div>".
                    "Date generated: ".
                $timestamp.
                "</div>".
            "</div>";
        }
                    
    }
    
    /*creat a product feed for admin*/
    public function admin_feed($feed_id, $purpose_id, $retire_id,  $filter, $search){
        
            if($search){
                $get_product = (" SELECT * 
                    FROM product
                    WHERE product_name='".$search."'
                    ORDER BY timestamp DESC 
                    ");
            }elseif($retire_id=="all"){
                $get_product = (" SELECT * 
                    FROM product
                    ORDER BY timestamp DESC 
                    ");
            }else{
                $get_product = (" SELECT * 
                    FROM product
                    WHERE retire_id='".$retire_id."'
                    ORDER BY timestamp DESC 
                    ");
            }
            $result_get_product = $this->con->query( $get_product );
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
                    $result = $this->con->query( $stmt );
                    if( $result->num_rows > 0 && $purpose_id != 1){
                        $onclick_product = "href='index.php?product_name=$product_name&&purpose_id=2&&retire_id=0'";
                     }elseif($purpose_id != 1){
                       $onclick_product = "href='index.php?product_name=$product_name&&purpose_id=2&&retire_id=0' onclick='buy(\"".$product_id."feed\", \"".$product_id."img_holder\", \"".$product_id."buy_holder\", \"".$product_id."buy_everything_holder\", \"".$product_id."filter\")'";
                    }
                    
                    /*switch purpose id to create activity button for the products
                     * 0 purpose id = activity buttons for users
                     * 1 purpose id = activity button for admin
                     */
                    if($purpose_id == 1){
                        /*create classes for css styling*/
                        $class_1 = "feed_holder";
                        $class_2 = "img_holder";
                        $class_3 = "product_name_holder";
                        $class_4 = "buy_everything_holder";
                        $class_5 = "filter";
                        $onclick_product = "href='#".$product_id."feed' onclick='buy(\"".$product_id."feed\", \"".$product_id."img_holder\", \"".$product_id."buy_holder\", \"".$product_id."buy_everything_holder\", \"".$product_id."filter\")'";
                        /*just switch retire button text
                         * if product retired, button text= Retire.
                         * if product is not retired, button text= Retired.
                         */
                        if($retire==1){
                          $retire_btn_text = "RETIRED";  
                        }else{
                          $retire_btn_text = "RETIRE";
                        }
                        $activity_btn = "<div>".
                                        "<a href='products.php?retire_id=0&&product=$product_id&&action=1&&category=$category_id&&l&&option=2'>
                                            <button  class='btn_50'>
                                                UPDATE
                                            </button>
                                        </a>
                                        <a onclick='retire_product(\"".$product_id."product_id_holder\")'>
                                            <button id='".$product_id."retire_btn'  class='btn_50 grey'>
                                                $retire_btn_text
                                            </button>
                                        </a>".
                                        "</div>";
                        $add_to_cart_btn = "";
                    }
                /*show products*/    
                echo
                    "<a href='#".$product_id."feed' >".
                        "<div id='".$product_id."feed' class='".$class_1."'>".
                            "<a $onclick_product>"."<div id='".$product_id."img_holder' class='".$class_2."' >".$img."</div>"."</a>".
                            "<div id='".$product_id."buy_holder' class='".$class_3."' style='display:block;'>".
                                "<div id='".$product_id."buy_everything_holder' class='".$class_4."'>".
                                    "<div class='margin_bottom'>".$product_name."</div>".
                                    "<div class='margin_bottom'>"."R ".$price."</div>".
                                        $add_to_cart_btn.
                                    "<div id='".$product_id."filter' class='".$class_5." hide '>".
                                        "<div class='margin_bottom'>".$product_description."</div>".
                                        "<div class='label'>"."Color: ".$product_color."</div>";
                                    echo "<div class='margin_bottom'></div>";
                                    echo "<div id='btn' >";
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
        /*
                
                /*get the same product with other colors from same product table */
                $get_same_product_1 = $this->con->prepare("
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
             if($purpose_id==0 || $purpose_id==2){   
                $this->con->close();
             }
    }/*close feed function*/ 

    
    /*admin search product*/
    public function admin_search_product($product_name){
        $search = $this->con->real_escape_string( $product_name );
        $search = preg_replace( "/[^A-Za-z0-9 ]/", '', $search );
        $search = "'%".$product_name."%'";
        $search_product = $this->con->prepare("
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
    
/*get most searched products*/
public function most_searched_product(){
    /*get current date, year*/
    $current_yr = date("Y");
    $prg_current_yr = $this->con->real_escape_string( $current_yr );
    $prg_current_yr = preg_replace( "/[^A-Za-z0-9 ]/", '', $prg_current_yr );
    $prg_current_yr = "'%".$current_yr."%'";
    /*get searched products for only the current year*/
    $get_searched_product = $this->con->prepare("
            SELECT product_id, search_no,timestamp 
            FROM searched_product
            WHERE timestamp LIKE $prg_current_yr
            ORDER BY search_no DESC
        ");
        $get_searched_product->execute();
        $get_searched_product->bind_result( $product_id, $search_no, $timestamp );
        //initialise total sales
        $total_searches_current_yr=0;
            $jan_searches=0;
            $feb_searches=0;
            $mar_searches=0;
            $apr_searches=0;
            $may_searches=0;
            $jun_searches=0;
            $jul_searches=0;
            $aug_searches=0;
            $sep_searches=0;
            $oct_searches=0;
            $nov_searches=0;
            $dec_searches=0;
        while($get_searched_product->fetch()){
            $total_searches_current_yr += $search_no;
            /*get total searches for months*/
            $arr_timestamp = explode( '-' , $timestamp );
            for( $i= 0; $i < count($arr_timestamp); $i++ ){
                    //jan
                    if($arr_timestamp[$i]==01){
                        $jan_searches += $search_no;
                    }
                    //feb
                    if($arr_timestamp[$i]==02){
                        $feb_searches += $search_no;
                    }
                    //mar
                    if($arr_timestamp[$i]==03){
                        $mar_searches += $search_no;
                    }
                    //apr
                    if($arr_timestamp[$i]==04){
                        $apr_searches += $search_no;
                    }
                    //may
                    if($arr_timestamp[$i]==05){
                        $may_searches += $search_no;
                    }
                    //jun
                    if($arr_timestamp[$i]==06){
                        $jun_searches += $search_no;
                    }
                    //jul
                    if($arr_timestamp[$i]==07){
                        $jul_searches += $search_no;
                    }
                    //aug
                    if($arr_timestamp[$i]==08){
                        $aug_searches += $search_no;
                    }
                    //sep
                    if($arr_timestamp[$i]==09){
                        $sep_searches += $search_no;
                    }
                    //oct
                    if($arr_timestamp[$i]==10){
                        $oct_searches += $search_no;
                    }
                    //nov
                    if($arr_timestamp[$i]==11){
                        $nov_searches += $search_no;
                    }
                    //dec
                    if($arr_timestamp[$i]==12){
                        $dec_searches += $search_no;
                    }
            }
        }
        if($jan_searches){
            $jan_searches=$jan_searches;
        }else{
           $jan_searches=0;
        } 
        if($feb_searches){
            $feb_searches=$feb_searches;
        }else{
           $feb_searches=0;
        }
        if($mar_searches){
            $mar_searches=$apr_searches;
        }else{
           $mar_searches=0;
        }
        if($apr_searches){
            $apr_searches=$apr_searches;
        }else{
           $apr_searches=0;
        }
        if($may_searches){
            $may_searches=$may_searches;
        }else{
           $may_searches=0;
        }
        if($jun_searches){
            $jun_searches=$jun_searches;
        }else{
           $jun_searches=0;
        }
        if($jul_searches){
            $jul_searches=$jul_searches;
        }else{
           $jul_searches=0;
        }
        if($aug_searches){
            $aug_searches=$aug_searches;
        }else{
           $aug_searches=0;
        }
        if($sep_searches){
            $sep_searches=$sep_searches;
        }else{
           $sep_searches=0;
        }
        if($oct_searches){
            $oct_searches=$oct_searches;
        }else{
           $oct_searches=0;
        }
        if($nov_searches){
            $nov_searches=$nov_searches;
        }else{
           $nov_searches=0;
        }
        if($dec_searches){
            $dec_searches=$dec_searches;
        }else{
           $dec_searches=0;
        }
        
        echo "<h4>Total Searches</h4>";
        echo $current_yr." : ".$total_searches_current_yr." times";
        echo "<h4>Months</h4>";
        echo "<div>".
                "Jan : ".$jan_searches." times".
            "</div>";
        echo "<div>".
                "Feb : ".$feb_searches." times".
            "</div>";
        echo "<div>".
                "Mar : ".$mar_searches." times".
            "</div>";
        echo "<div>".
                "Apr : ".$apr_searches." times".
            "</div>";
        echo "<div>".
                "May : ".$may_searches." times".
            "</div>";
        echo "<div>".
                "Jun : ".$jun_searches." times".
            "</div>";
        echo "<div>".
                "Jul : ".$jul_searches." times".
            "</div>";
        echo "<div>".
                "Aug : ".$aug_searches." times".
            "</div>";
        echo "<div>".
                "Sep : ".$sep_searches." times".
            "</div>";
        echo "<div>".
                "Oct : ".$oct_searches." times".
            "</div>";
        echo "<div>".
                "Nov : ".$nov_searches." times".
            "</div>";
        echo "<div>".
                "Dec : ".$dec_searches." times".
            "</div>";
      
    }
}
?>

