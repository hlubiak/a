<?php
class UserAccount {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    public function userAccount(){
        
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
    
}
