<?php

class Menu {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
        
    function menu(){
        echo "
                <ul class='menu_ul'>
                    <a href='#'><li>About us</li></a>
                    <a href='#'><li>How to buy</li></a>
                    <a href='#'><li>Terms and Conditions</li></a>
                    <a href='#'><li>Who we are</li></a>
                </ul>
                <ul class='menu_ul'>
                    <a href='#'><li>My Account</li></a>
                    <a href='#'><li>Buy</li></a>
                    <a href='#'><li>Track my sales</li></a>
                    <a href='#'><li>Contact us</li></a>
                </ul>
            ";
    }
}
