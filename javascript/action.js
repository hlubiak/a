
/*
 * create toggle function
 */
function toggle_one( a ){
    
    var toggle = document.getElementById(a);
    if( toggle.style.display == "none" ){
        toggle.style.display = "block";
    }else{
        toggle.style.display = "none";
    }
    
}

function menu_roll(x) {
    x.classList.toggle("change");
}

/*toggle div*/
function toggle( a, b, c ){
    var toggle = document.getElementById(a);
    var option_value = document.getElementById(b).value;
    var new_option_value = document.getElementById(c);
    var dim = document.getElementById("dim");
    if( toggle.style.display == "none" ){
        toggle.style.display = "block";
        new_option_value.value =  option_value;
        dim.style.display = "block";
    }else{
        toggle.style.display = "none";
        dim.style.display = "none"
        new_option_value.value = "";
    }
    
}


/*
 * create a toggle function to show options as you click
 * show one option and hide others
 */
function toggle_hide( a, b, c, d ){
    var show = document.getElementById(a);
    var hide_1 = document.getElementById(b);
    var hide_2 = document.getElementById(c);
    var hide_3 = document.getElementById(d);
    if( show.style.display == "none" ){
        show.style.display = "block";
        hide_1.style.display =  "none";
        hide_2.style.display =  "none";
        hide_3.style.display =  "none";
    }else{
        show.style.display = "block";
        hide_1.style.display =  "none";
        hide_2.style.display =  "none";
        hide_3.style.display =  "none";
    }
}

/*
 * create a big view on feed click
 * to allow a better view of the product 
 * allow user to buy
 */
function buy(a, b, c, d, e){
     
    var feed_holder = document.getElementById(a);
    var img_holder = document.getElementById(b);
    var buy_holder = document.getElementById(c);
    var buy_everything_holder = document.getElementById(d);
    var filter = document.getElementById(e);
   
    if(buy_holder.style.display == "block"){
        feed_holder.style.width = "100%";
        feed_holder.style.height = 0.5*window.innerWidth;
        feed_holder.style.borderBottom = "1px solid #CCC";
        feed_holder.style.borderTop = "1px solid #CCC";

        img_holder.style.width = "69%";
        img_holder.style.textAlign = "center";
        img_holder.style.float = "left";
        img_holder.style.height = "100%";

        buy_everything_holder.style.position = "relative";
        buy_everything_holder.style.top = "100px";
        buy_everything_holder.style.fontSize = "22px";
        buy_everything_holder.style.textAlign = "left";

        buy_holder.style.width = "28%";
        buy_holder.style.height = "96.6%";
        buy_holder.style.float = "right";
        buy_holder.style.background = "#fff";

        filter.style.display = "block";
        filter.style.fontSize = "14px";
        filter.style.fontWeight = "normal";
    }else{
        buy_holder.style.width = "98%";
        buy_holder.style.height = "20%";
        buy_holder.style.textAlign = "center";
        buy_holder.style.background = "none";
        
        feed_holder.style.width = "33%";
        feed_holder.style.height = "300px";
        feed_holder.style.borderBottom = "none";
        feed_holder.style.borderTop = "none";

        img_holder.style.width = "100%";
        img_holder.style.textAlign = "center";
        img_holder.style.float = "none";

        buy_everything_holder.style.position = "relative";
        buy_everything_holder.style.top = "0";
        buy_everything_holder.style.fontSize = "15px";
        buy_everything_holder.style.textAlign = "center";
        
        filter.style.display = "none";
    }
    
}

/*
 * slideshow funtion
 */





	
	
