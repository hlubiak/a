<?php

class CloseSale {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*insert sale*/
    public function closeSale($user_id, $fullname, $phonenumber, $email, $password, $city, $town, $strnumber, $sold_products, $amount, $size, $quantity){
        
        //create encrypt password object
        $encryptPasswordObject = new EncryptPassword();
        
        if($user_id != ""){
            /*get register passowrd*/
            $get_password=$this->connection->prepare("
                SELECT password, password_key 
                FROM users
                WHERE user_id=?
                    ");
            $get_password->bind_param("i",$user_id);
            $get_password->execute();
            $get_password->bind_result($fetched_password, $password_key);
            while($get_password->fetch()){
                
                //encrypt password 
                $encryptedPassword = $encryptPasswordObject->encryptInput($password_key, $password);
                
                if($encryptedPassword == $fetched_password){
                    /*enter sale*/
                    $confirmed_identity = 1;
                }else{
                    echo "Sorry! Identity not confirmed. sale failed.";
                    $confirmed_identity = 0;
                }                
            }
        }else{
            //encrypt password
            $password_key  = base64_encode( mcrypt_create_iv( 44, MCRYPT_DEV_URANDOM ) );
            $encryptedPassword = $encryptPasswordObject->encryptInput($password, $password_key);
            $insert = $this->connection->prepare("
                        INSERT 
                        INTO users ( fullname, phonenumber, email, password, password_key, city, town, strnumber ) 
                        VALUES ( ?, ?, ?, ?, ?, ?, ?, ? ) 
                        ");
            $insert->bind_param( "ssssssss", $fullname, $phonenumber, $email, $encryptedPassword, $password_key, $city, $town, $strnumber );
            if( $insert->execute() == true){
                $confirmed_identity = 2;
            }else{
                $confirmed_identity = 0;
            }
        }
        
        /*update user info*/
        if($confirmed_identity == 1){
            //update user information
            $update_user_info = $this->connection->prepare("
                UPDATE users  
                SET fullname=?, phonenumber=?, email=?, city=?, town=?, strnumber=? 
                WHERE user_id=? 
                ");
            $update_user_info->bind_param( "ssssssi", $fullname, $phonenumber, $email, $city, $town, $strnumber, $user_id );
            $update_user_info->execute();
        }
        
        /*insert sale*/
        if($confirmed_identity == 1 || $confirmed_identity == 2){
                $paid = 1;
                $invoice_number = mt_rand( 100000, 999999 );
                $invoice_number .=  mt_rand( 0, 2 ).$phonenumber;
                
                $insert_sale = $this->connection->prepare("
                        INSERT 
                        INTO sales ( product_id, user_id, amount, paid, invoice_number, size, quantity ) 
                        VALUES ( ?, ?, ?, ?, ?, ?, ? ) 
                        ");
                $insert_sale->bind_param( "sssisss", $sold_products, $phonenumber, $amount, $paid, $invoice_number, $size, $quantity );
                if( $insert_sale->execute() == true){
                    echo "Purchase is successful. Expect your delivery. <a href='index.php'>OK</a>";
                    /*unset products from session*/
                    session_start();
                    unset($_SESSION['product_cart_id']);
                    unset($_SESSION['product_table_id']);
                    unset($_SESSION['product_price']);
                }
                
        }else{
            echo "Sorry. something went wrong. Purchase unsuccessful.";
        }
        //close connection
        $this->connection->close();
    }/*close insert sale function*/
    
}
