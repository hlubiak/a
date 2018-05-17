<?php

class FilterProductByCategorySearch {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function filterProductsByCategoryForSearch(){
        
        echo 
            //create a select to select category name options
            "<form action='object.php' method='POST'>".
            "<label>"."Choose category to search"."<label>".
            "<select name='search_category_id' id='search_category_id' class='text_inpu more_height'>".
                "<a href='index.php?filter=all'>".
                    "<button class='sub_sub_option_btn' >"."All"."</button>".
                "</a>";
        $get_product_category = $this->connection->prepare ("
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
        
        //close connection
        $this->connection->close();
    }
    
}
