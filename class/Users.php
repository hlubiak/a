<?php

class Users {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function users(){
        echo "hallo world";
        //get total number of website users that registered themselves on the website
        $get_sum_users = $this->connection->prepare (" 
                    SELECT COUNT(user_id) AS total_customers_registered 
                    FROM users
                    ");
        $get_sum_users->execute();
        $get_sum_users->bind_result( $total_customers_registered );
        while( $get_sum_users->fetch() ) {
            if($total_customers_registered > 0){
                echo $total = $total_customers_registered;
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
        $this->connection->close();
    }
}
