<?php

class UpdateUserInfo {
    
    private $connection;

    //constructor
    function __construct(){

        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();

    }
    
    /*update names*/
    function updateNameContact($loggedin_user_id, $fullname,  $phonenumber,$email){
        
        //connect database
        $DBObject = new Database();
        $conn = $DBObject->dbConnect();
        
        $update = $conn->prepare("
            UPDATE users 
            SET fullname=?, phonenumber=?, email=? 
            WHERE user_id=? 
            ");
        $update_success=0;
        $update->bind_param( "sssi", $fullname, $phonenumber, $email, $loggedin_user_id );
        if( $update->execute() == true){
            $update_success=1;
            echo "Updated";
        }
        /*update sales to keep users invoices relevant to their new info*/
        if($update_success=1){
            $update_sales = $conn->prepare("
            UPDATE sales 
            SET user_id=? 
            WHERE user_id=? OR user_id=? OR user_id=?
            ");
            $update_sales->bind_param( "issi",$loggedin_user_id, $phonenumber, $email, $loggedin_user_id );
            $update_sales->execute();
        }else{
            echo "Something went wrong with your invoices.";
        }
        //close database
        $conn->close();
    }
    
    /**update address*/
    function updateAddress($loggedin_user_id, $city, $town,$strnumber){
        
        //connect database
        $DBObject = new Database();
        $conn = $DBObject->dbConnect();
        
        $update = $conn->prepare("
            UPDATE users 
            SET city=?, town=?, strnumber=? 
            WHERE user_id=? 
            ");
        $update->bind_param( "sssi", $city, $town, $strnumber, $loggedin_user_id );
        if( $update->execute() == true){
            echo "Updated";
        }
        //close database
        $conn->close();
    }

    /*update password*/    
    function updatePassword($loggedin_user_id, $current_password,  $new_password){
        
        //connect database
        $DBObject = new Database();
        $conn = $DBObject->dbConnect();
        
        //get registered encrypted password
        $statement = mysqli_prepare($conn,
                    "SELECT password, password_key
                    FROM users WHERE user_id=?
                    ");
            mysqli_stmt_bind_param($statement, "i", $loggedin_user_id);
            mysqli_stmt_execute($statement);
            mysqli_stmt_store_result($statement);
            mysqli_stmt_bind_result($statement, $password, $password_key);
            $count = 0;
            while(mysqli_stmt_fetch($statement)){
                $count += 1;
                //encrypt password
                $encryptPasswordObject = new EncryptPassword();
                $current_encrypted_password = $encryptPasswordObject->encryptInput($password_key, $current_password);
                //create a new password
                $new_encrypted_password= $encryptPasswordObject->encryptInput($password_key, $new_password);
                //check password match
                if($current_encrypted_password == $password){
                    //update password
                    $update = $conn->prepare("
                        UPDATE users 
                        SET password=? 
                        WHERE user_id=? 
                        ");
                    $update->bind_param( "si", $new_encrypted_password, $loggedin_user_id );
                    if( $update->execute() == true){
                        echo "Updated";
                    }else{
                        echo "error";
                    }
                }else{
                    echo "Denied.";
                }
                
            }
            mysqli_stmt_close($statement);
            //close connection to database
            $conn->close();

    }
}
