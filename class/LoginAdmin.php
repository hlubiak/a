<?php

class LoginAdmin {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function loginAdmin( $access_name, $access_code_input, $new_option_value ){
        
        //data to encrypt the access code 
        $check_access_code = $this->connection->prepare (" 
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
            //encrypt password
            $encryptPasswordObject = new EncryptPassword();
            $encryptedPassword = $encryptPasswordObject->encryptInput( $access_code_key, $access_code_input);
                
            //check if encrypted_access_code_input is equal the access code fetched from the database
            //if they match, give access
            if($encryptedPassword == $access_code){
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
        $this->connection->close();
    }
    
    
}
