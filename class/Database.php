<?php

    class Database{
    //database settings
        private $con;
        /*
        * this constructor function will take arguments for database
        */
        function __construct(){
			
                /*
                 * create databse connection using mysql improved
                 */
                $this->con = new mysqli( "localhost", "root", "", "ecommerce" );
                /*
                 * check if you are connected else return error
                 */
                if ( $this->con->connect_error ) {
                        die( "Connection failed: " . $this->con->connect_error )	;
                }
        }
        
        function dbConnect(){
            $connection = $this->con;
            return $connection;
        }
        
    }
?>

