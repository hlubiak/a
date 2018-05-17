

/*
 * This function sends data to search customer by name
*/
function search_product( a, b ){
	var xmlhttp;
        var send_id = 5;
        var category = document.getElementById(a).value;
        var search_name = document.getElementById(b).value;
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
            document.getElementById("search_output").innerHTML = xmlhttp.responseText;
          }
        }
          xmlhttp.open( "POST", "object.php" ,true );
          xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
          xmlhttp.send( "send_id="+send_id + "&search_name="+search_name + "&category="+category );
}//close search function

/*
*just fetch data  
*/
function fetch_data(a){
      
	var xmlhttp;
        var send_id = 6;
        var fetch_id = document.getElementById(a).value;
      
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function()
        {
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById( "manage_customer_users_holder" ).innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&fetch_id="+fetch_id );
          
}

//send upload data to backened
function upload(a, b, c){

	var xmlhttp;
        var send_id = 7;
        var upload_id = document.getElementById(a).value;
        var value_1 = document.getElementById(b).value;
        var value_2 = document.getElementById(c).value;
            
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function()
        {
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 )
          {
            document.getElementById( "output"+upload_id ).innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&upload_id="+upload_id + "&value_1="+value_1 + "&value_2="+value_2 );
          
}

//send data to buy a product
function add_to_cart(a, b, c, d ){

	var xmlhttp;
        var send_id = 8;
        var product_id = document.getElementById(a).value;
        var table_id = document.getElementById(b).value;
        var product_size = document.getElementById(c).value;
        var product_price = document.getElementById(d).value;
            
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function()
        {
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 )
          {
            document.getElementById( "output"+product_id ).innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&product_id="+product_id  + "&table_id="+table_id + "&product_size="+product_size+ "&product_price="+product_price);
          
}

/*remove product ajax*/
function remove_product(a,b){

	var xmlhttp;
        var send_id = 9;
        var array_position = document.getElementById(a).value;
        var product_price = document.getElementById(b).value;
        var total_price_value = document.getElementById("total_price_value").value;
        var count_cart_value = document.getElementById("count_cart_value").value;
        /*update the total price value*/
        var updated_total_price = total_price_value - product_price;
        /*update count cat*/
        var updated_cart_value = count_cart_value - 1;
        
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function()
        {
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 )
          {
            document.getElementById( "output"+array_position ).innerHTML = xmlhttp.responseText;
            /*hide the product from the feed*/
            document.getElementById(array_position+"session_product_holder").style.display = "none";
            /*show updated total price value*/
            document.getElementById( "total_price" ).innerHTML = updated_total_price;
            document.getElementById("total_price_value").value = updated_total_price;
            /*show updated count cart value*/
            document.getElementById( "count_cart" ).innerHTML = updated_cart_value;
            document.getElementById("count_cart_value").value = updated_cart_value;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&array_position="+array_position );
          
}

/*confirm checkout*/
function confirm_checkout(a,b,c,d,e,f,g,h, i, j, k, l, m){

	var xmlhttp;
        var send_id = 10;
        var fullname = document.getElementById(a).value;
        var phonenumber = document.getElementById(b).value;
        var email = document.getElementById(c).value;
        var city = document.getElementById(d).value;
        var town = document.getElementById(e).value;
        var strnumber = document.getElementById(f).value;
        var sold_products = document.getElementById(g).value; 
        var amount = document.getElementById(h).value;
        var size = document.getElementById(i).value;
        var quantity = document.getElementById(j).value; 
        var payment_card_number = document.getElementById(k).value;
        var expiry_date = document.getElementById(l).value; 
        var three_number = document.getElementById(m).value;
        /*prevent empty inputs*/
        if(fullname=="" && phonenumber=="" && town=="" && strnumber=="" && payment_card_number=="" ){
            alert("Please fill in all information.");
        }else{
            if ( window.XMLHttpRequest ){
              xmlhttp = new XMLHttpRequest();
            }else{
              xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange=function(){
                if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){
                  document.getElementById("confirm_payment_output").innerHTML = xmlhttp.responseText;
                  /*empty the inputs values*/
                    document.getElementById(a).value="";
                    document.getElementById(b).value="";
                    document.getElementById(c).value="";
                    document.getElementById(d).value="";
                    document.getElementById(e).value="";
                    document.getElementById(f).value="";
                    document.getElementById(g).value=""; 
                    document.getElementById(h).value="";
                    document.getElementById(k).value="";
                    document.getElementById(l).value=""; 
                    document.getElementById(m).value="";
                }
            }
            /*show processing feedback*/
            document.getElementById('confirm_payment_output').style.display="block";
            
            xmlhttp.open( "POST", "object.php" ,true );
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.send( "send_id="+send_id + "&fullname="+fullname 
                    + "&phonenumber="+phonenumber + "&email="+email
                    + "&city="+city + "&town="+town
                    + "&strnumber="+strnumber + "&sold_products="+sold_products + "&amount="+amount
                    + "&size="+size + "&quantity="+quantity
                    + "&payment_card_number="+payment_card_number + "&expiry_date="+expiry_date 
                    + "&three_number="+three_number);
        }  
}

/*retire product*/
function retire_product(a){
	var xmlhttp;
        var send_id = 12;
        var product_id = document.getElementById(a).value;
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById( product_id+"retire_btn" ).innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&product_id="+product_id );
}

/*remove user*/
function remove_user(a, b){
	var xmlhttp;
        var send_id = 13;
        var user_id = document.getElementById(a).value;
        var total_users = document.getElementById(b).value;
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById( user_id+"remove_user_holder" ).innerHTML = xmlhttp.responseText;
            document.getElementById( user_id+"remove_user_holder" ).style.display="none";
            document.getElementById("total_user_value_show").innerHTML=total_users-1;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&user_id="+user_id );
}

/*update names and contacts*/
function update_name_contact(){
    
	var xmlhttp;
        var send_id = 16;
        var fullname = document.getElementById("fullname").value;
        var phonenumber = document.getElementById("phonenumber").value;
        var email = document.getElementById("email").value;
     
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById("update_name_contact_btn").innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&fullname="+fullname + "&phonenumber="+phonenumber + "&email="+email );
}
/*update address*/
function update_address(){
	var xmlhttp;
        var send_id = 17;
        var city = document.getElementById("city").value;
        var town = document.getElementById("town").value;
        var strnumber = document.getElementById("strnumber").value;
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById("update_address_btn").innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&city="+city + "&town="+town + "&strnumber="+strnumber );
}

/*update password*/
function update_password(){
	var xmlhttp;
        var send_id = 18;
        var current_password = document.getElementById("current_password").value;
        var new_password = document.getElementById("new_password").value;
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById("update_password_btn").innerHTML = xmlhttp.responseText;
          }
        }
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&current_password="+current_password + "&new_password="+new_password );
}

/*admin search product*/
function admin_search_product(a){
	var xmlhttp;
        var send_id = 19;
        var product_name = document.getElementById(a).value;
        if ( window.XMLHttpRequest ){
          xmlhttp = new XMLHttpRequest();
        }else{
          xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
          xmlhttp.onreadystatechange=function(){
          if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
            document.getElementById("admin_search_product_output").innerHTML = xmlhttp.responseText;
          }
        }
        document.getElementById("admin_search_product_output").style.display="block";
        xmlhttp.open( "POST", "object.php" ,true );
        xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send( "send_id="+send_id + "&product_name="+product_name );
}