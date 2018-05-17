<?php

class ConfirmCheckout {
    
     private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*confirm payment*/
    function confirmCheckout($fullname, $phonenumber, $email, $city, $town, $strnumber, $sold_products, $amount, $size, $quantity){
        
        /*Create a form to send the cart informtion
        *After a user creates or confirms their password
        */
        echo "<form action='object.php' method='post'>".
                "<input type='hidden' name='phonenumber' value='".$phonenumber."'/>".
                "<input type='hidden' name='fullname' value='".$fullname."'/>".
                "<input type='hidden' name='email' value='".$email."'/>".
                "<input type='hidden' name='city' value='".$city."'/>".
                "<input type='hidden' name='town' value='".$town."'/>".
                "<input type='hidden' name='strnumber' value='".$strnumber."'/>".
                "<input type='hidden' name='product_id' value='".$sold_products."'/>".
                "<input type='hidden' name='amount' value='".$amount."'/>".
                "<input type='hidden' name='size' value='".$size."'/>".
                "<input type='hidden' name='quantity' value='".$quantity."'/>".
                "<input type='hidden' name='send_id' value='15'/>";
        /*insert user personal infomation into user table*/
        $check_user_exists = $this->connection->prepare ("
                SELECT user_id, phonenumber, email
                FROM users 
                WHERE phonenumber=? OR email=?
                " );
        $check_user_exists->bind_param("ss",$phonenumber, $email);
        $check_user_exists->execute();
        $check_user_exists->bind_result( $user_id, $phonenumber, $email );
        if($check_user_exists->fetch()){
                /*confirm users*/
           echo  "Check Password".
                "<input type='hidden' name='user_id' value='".$user_id."'/>".
                "<input type='text' name='password' />".
                "<input type='submit' name='close_sale' value='Continue' />".
                "</form>";  
        }else{
          echo  "Create Password".
                "<input type='hidden' name='user_id' value=''/>".
                "<input type='text' name='password' />".
                "<input type='submit' name='close_sale' value='Continue' />".
                "</form>";
        }
        /*close database*/
        $this->connection->close();
    }
}
