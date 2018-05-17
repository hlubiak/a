<?php

class AdminManageInvoice {
    
    private $connection;

    //constructor
    function __construct(){
	
        //connect database
        $DBObject = new Database();
        $this->connection = $DBObject->dbConnect();
               
    }
    
    /*get invoices*/
    public function adminManageInvoice($paid_invoice, $unpaid_invoice){
        
        $get_invoice = ("
            SELECT sale_id, user_id,amount, invoice_number 
            FROM sales
            WHERE paid='".$paid_invoice."'
            ORDER BY timestamp DESC
            ");
        $result_get_invoice = $this->connection->query( $get_invoice );
        if( $result_get_invoice->num_rows > 0 ){
            $total_invoice=null;
            $total_invoice_amount=0;
            while ( $row_get_invoice = $result_get_invoice->fetch_assoc() ){
            $sale_id=$row_get_invoice['sale_id'];
            $user_id=$row_get_invoice['user_id'];
            $amount=$row_get_invoice['amount'];
            $invoice_number=$row_get_invoice['invoice_number'];
            /*get users for the invoices*/
            $stmt = (" SELECT fullname, email, phonenumber
                FROM users
                WHERE user_id='".$user_id."' OR phonenumber='".$user_id."' OR email='".$user_id."'
                ORDER BY timestamp DESC
                ");
            $result = $this->connection->query( $stmt );
            if( $result->num_rows > 0 ){
                while ( $row = $result->fetch_assoc() ){
                    $fullname=$row['fullname'];
                    $email=$row['email'];
                    $phonenumber=$row['phonenumber'];
                }
            }else{
                $fullname="";
                $email="";
                $phonenumber="";
            }
            
            $total_invoice += count($invoice_number);
            $total_invoice_amount += $amount;
            echo "<div id='".$sale_id."remove_user_holder' class='feed_holder_middle user_feed_holder'>".
                    "<div>"."Invoice NO: ".$invoice_number."</div>".
                    "<div>"."R ".$amount."</div>".
                    "<div>".$fullname."</div>".
                    "<div>".$email."</div>".
                    "<div>".$phonenumber."</div>".
                    "<button class='' onclick='remove_user()'>Message</button>".
                    "<input type='hidden' id='".$sale_id."value' value='".$sale_id."'/>".
                    "<button class='' onclick='remove_user(\"".$sale_id."value\", \"total_user_value\")'>REMOVE</button>".
                "</div>";
        }
        echo "<div class='total_holder total_holder_users'>".
                "<input type='hidden' id='total_user_value' value='".$total_invoice_amount."'/>".
                "<h5>Total invoices".": "."<span id='total_user_value_show' class='heading'>".$total_invoice."</span>"."</h5>".
                "<h5>Amount".": R "."<span id='total_user_value_show' class='heading'>".$total_invoice_amount."</span>"."</h5>".
            "</div>";
        }
        
        $this->connection->close();
    }
}
