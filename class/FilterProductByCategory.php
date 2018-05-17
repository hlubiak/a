<?php

class FilterProductByCategory {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    public function filterProductByCategory(){
        
        echo 
            //create a select to select category name options    
            "<div name='product_category_id' class='text_inpu more_height'>".
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
                echo "<a href='index.php?filter=$product_category_id'>".
                        "<button class='sub_sub_option_btn' >".$category_name."</button>".
                    "</a>";
        }
        echo "</div>";
        
        //close database
        $this->connection->close();
    }
    
}
