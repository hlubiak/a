<?php


class UploadProductForm {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function uploadProductForm( $retire_id, $product_id, $action_id, $category_id ){
        
        /*
         * get product category from the database 
        */
        if($action_id == 1){
            $get_product_category = $this->connection->prepare ("
                SELECT product_id, category_name 
                FROM pro_category 
                WHERE product_id=? 
                " );
            $get_product_category->bind_param("i",$category_id);
            $send_id = 11;
        }else{
        $get_product_category = $this->connection->prepare ("
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
            $get_product = $this->connection->prepare ("
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
        $get_product_brand = $this->connection->prepare("
                SELECT brand_id, brand_name 
                FROM brand 
                WHERE brand_id=?
            ");
        $get_product_brand->bind_param("i",$product_brand_id);
        }else{
        $get_product_brand = $this->connection->prepare("
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
        $get_product_type = $this->connection->prepare("
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
         
         //close connection
         $this->connection->close();
    }
}
