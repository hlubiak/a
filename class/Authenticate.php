<?php

class Authenticate {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    //validate login
    function validateLogin($username, $inputPassword){
        
        //check username and password from database
        if($statement = $this->connection->prepare("
                SELECT user_id, phonenumber, password_key, password
                FROM users 
                WHERE phonenumber = ? OR email = ?
            ")
        ){
            $statement->bind_param("ss", $username, $username);
            $statement->execute();
            $statement->bind_result($user_id, $phonenumber, $passwordKey, $password);
            $count=0;
            //check if rows exists
            while($statement->fetch()){
                $count += 1;
                
                //encrypt password
                $encryptPasswordObject = new EncryptPassword();
                $encryptedPassword = $encryptPasswordObject->encryptInput( $passwordKey, $inputPassword);
                
                    if($password==$encryptedPassword){
                        //set session
                        session_start();
                        $_SESSION['user_id'] = $user_id;
                        $loggedinUser = $_SESSION['user_id'];
                        $statement->close();
                        header("Location:index.php");
                        return "Login success";
                    }else{
                        return "Login failed";
                    }
            }
            if($count==0){
               //login fail
                $statement->close();
                return "Login failed";
            }
            //close conn
            $this->connection->close();
        }else{
            die("Error: Something went wrong");
        }
        
    }
    
    //check login status
    function checkLoginStatus(){
        session_start();
        if(isset($_SESSION['user_id'])){
            return TRUE;
        }else{
            return FALSE;
            header("Location:index.php");
        }
    }
    
    //logout
    function logout(){
        session_start();
        session_destroy();
        header('Location: index.php');
    }
    
}
