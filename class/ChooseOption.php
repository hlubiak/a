<?php

class ChooseOption {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*create options for the admin panel*/
    public function chooseOption($option, $toggle){
        switch($option){
           case 1:
               /*display*/
               if($toggle==1){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 2:
               /*display*/
               if($toggle==2){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 3:/*display*/
               if($toggle==3){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 4:/*display*/
               if($toggle==4){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           case 5:/*display*/
               if($toggle==5){
                  echo "block"; 
               }else{
                   echo "none";
               }
               break;
           default:
               break;
        }
    }
}

?>
