<?php

class UserAnalytics {
    
     private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*users analytics*/
    public function userAnalytics(){
        
        /*calculate total website users/registered*/
        $remove = 0;
        $get_users = $this->connection->prepare ("
            SELECT user_id,fullname, email, phonenumber 
            FROM users
            WHERE remove=?
            ");
        
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
        
        echo "<div class='total_holder total_holder_user'>".
                "<input type='hidden' id='total_user_value' value='".$total_users."'/>".
                "<h3>Total users".": "."<span id='total_user_value_show' class='heading'>".$total_users."</span>"."</h3>".
            "</div>";
        
        $this->connection->close();
    }
}
