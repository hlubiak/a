<?php

class RemoveUser {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*remove user*/
    function removeUser($user_id){
        
        $remove = 1;
        $remove_user = $this->connection->prepare("
                    UPDATE users 
                    SET remove=? 
                    WHERE user_id=? 
                    ");
        $remove_user->bind_param( "ii", $remove, $user_id );
        if( $remove_user->execute() == true){
            echo "Removed";
        }
        //close conncetion
        $this->connection->close();
    }
    
}
