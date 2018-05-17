<?php
    class UploadProduct {
        
        private $connection;

        //constructor
        function __construct(){

            //connect database
            $DBObject = new Database();
            $this->connection = $DBObject->dbConnect();

        }
        
       function uploadProduct($action_id, $product_category_id, $product_brand_id, $product_name, $product_photo, $product_weight, $product_description, $product_color, $product_price, $product_type_id, $gender, $upload_id){
           
            //check if the brand exists in the database
            if($upload_id == 0){
                $check_product = $this->connection->prepare ("
                        SELECT product_name 
                        FROM product 
                        WHERE product_name = ? AND product_color = ?
                    ");
            }elseif($upload_id == 1){
                $check_product = $this->connection->prepare ("
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
                            $insert = $this->connection->prepare("
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
                        $insert = $this->connection->prepare("
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
                   echo "Please fill in all fields."."<a href='products.php?retire_id=all&&product=all&&action=0&&category=all&&option=2'><button>"."OK"."</button></a>";

                }

            }
        //close database
        $this->connection->close();
       }
       
    function uploadCategory($value_1, $value_2){
        
            //check if the category exists in the database
            $check_category = $this->connection->prepare ("
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
                $insert = $this->connection->prepare("
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
            
            //close connection
            $this->connection->close();
    }
    
    function uploadType($value_1, $value_2){
       
            //check if the category exists in the database
            $check_type = $this->connection->prepare ("
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
                $insert = $this->connection->prepare("
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
            
            //close connection
            $this->connection->close();
    }
    
    //upload brand
    function uploadBrand($value_1, $value_2){
        
            //check if the category exists in the database
            $check_brand = $this->connection->prepare ("
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
                $insert = $this->connection->prepare("
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
            //close connection
            $this->connection->close();
        }
        
    }
?>
