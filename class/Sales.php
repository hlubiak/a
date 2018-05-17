<?php
class Sales {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    function sales($data){
        
        
        //sales that are paid/closed
        $paid = 1;
        //get timestamp for the past 3 years
        $get_sale_timestamp = $this->connection->prepare("
            SELECT amount,timestamp 
            FROM sales
            WHERE paid= ? 
            ORDER BY timestamp DESC
        ");
        $get_sale_timestamp->bind_param("i",$paid);
        $get_sale_timestamp->execute();
        $get_sale_timestamp->bind_result( $amount, $timestamp );
        /*get current date, year*/
        $current_yr = date("Y");
        $last_yr= $current_yr-1;
        $ybl_yr= $current_yr-2;
         
        //initialise total sales
        $total_sales_current_yr=0;
        $total_sales_last_yr = 0;
        $total_sales_lastoflast_yr = 0;
        $ini_month = 0;
        $total_sales_current_yr_months = 0;
        
        //monthly sales
        $total_sales_jan = 0;
        $jan="Jan";
        $total_sales_feb = 0;
        $feb="Feb";
        $total_sales_mar = 0;
        $mar="Mar";
        $total_sales_apr = 0;
        $apr="Apr";
        $total_sales_may = 0;
        $may="May";
        $total_sales_jun = 0;
        $jun="Jun";
        $total_sales_jul = 0;
        $jul="Jul";
        $total_sales_aug = 0;
        $aug="Aug";
        $total_sales_sep = 0;
        $sep="Sep";
        $total_sales_oct = 0;
        $oct="Oct";
        $total_sales_nov = 0;
        $nov="Nov";
        $total_sales_dec = 0;
        $dec="Dec";
        
        while($get_sale_timestamp->fetch()){
            
            /*change the format of date to give months in string,
             * for example - january
             */
            $old_date_timestamp = strtotime($timestamp);
            $new_date = date('Y-F-d G:i:s', $old_date_timestamp);
            //separate date
            $arr_timestamp = explode( '-' , $new_date );
        
            for( $i= 0; $i < count($arr_timestamp); $i++ ){
                    //current year
                    if($arr_timestamp[$i]==$current_yr){
                        $this_yr = $arr_timestamp[$i];
                        $total_sales_current_yr += $amount;
                        
                    }
                    //previous year
                    if(($arr_timestamp[$i])==($current_yr-1)){
                        $last_yr = $arr_timestamp[$i];
                        $total_sales_last_yr += $amount;
                    }
                    
                    //the year before last
                    if(($arr_timestamp[$i])==($current_yr-2)){
                        $ybl_yr = $arr_timestamp[$i];
                        $total_sales_lastoflast_yr += $amount;
                    }
                    
                    //current year months
                    switch($arr_timestamp[$i]){
                        case "January":
                            $total_sales_jan += $amount;
                            break;
                        case "February":
                            $total_sales_feb += $amount;
                            break;
                        case "March":
                            $total_sales_mar += $amount;
                            break;
                        case "April":
                            $total_sales_apr += $amount;
                            break;
                        case "May":
                            $total_sales_may += $amount;
                            break;
                        case "June":
                            $total_sales_jun += $amount;
                            break;
                        case "July":
                            $total_sales_jul += $amount;
                            break;
                        case "August":
                            $total_sales_aug += $amount;
                            break;
                        case "September":
                            $total_sales_sep += $amount;
                            break;
                        case "October":
                            $total_sales_oct += $amount;
                            break;
                        case "November":
                            $total_sales_nov += $amount;
                            break;
                        case "December":
                            $total_sales_dec += $amount;
                            break;
                        default:
                            break;
                    }
            }
            
        }
        
        //total sales for the past three years
        $total_all = $total_sales_current_yr + $total_sales_last_yr + $total_sales_lastoflast_yr;
        
        /*Draw linegraph for the first three years*/
        if($data=="total"){
            echo $this_yr." R".number_format($total_sales_current_yr, 2)." ";
            echo "<br/>";
            echo $last_yr." R".number_format($total_sales_last_yr, 2)." ";
            echo "<br/>";
            echo $ybl_yr." R".number_format($total_sales_lastoflast_yr, 2)." ";
            echo "<br/>";
        }//years data
        elseif($data=="labels"){
            echo '"'.$ybl_yr.'"'.",".'"'.$last_yr.'"'.",".'"'.$this_yr.'"';
        }elseif($data=="data"){
            echo $total_sales_lastoflast_yr.",".$total_sales_last_yr.",".$total_sales_current_yr;
        }//months data
        elseif($data == "monthsLabels"){
            //draw monthl sales graph
            echo '"Jan"'.",".'"'.$feb.'"'.",".'"'.$mar.'"'.
                    ",".'"'.$apr.'"'.",".'"'.$may.'"'.",".'"'.$jun.'"'.
                    ",".'"'.$jul.'"'.",".'"'.$aug.'"'.",".'"'.$sep.'"'.
                    ",".'"'.$oct.'"'.",".'"'.$nov.'"'.",".'"'.$dec.'"';
        }elseif($data == "monthsData"){
            echo $total_sales_jan.",".$total_sales_feb.",".$total_sales_mar.
                ",".$total_sales_apr.",".$total_sales_may.",".$total_sales_jun.
                ",".$total_sales_jul.",".$total_sales_aug.",".$total_sales_sep.
                ",".$total_sales_oct.",".$total_sales_nov.",".$total_sales_dec;
        }
        
    }
}
