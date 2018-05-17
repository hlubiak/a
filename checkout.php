<?php

    require '/class/Database.php';

?>
<html>
    
    <head>
        <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
        <title> Ecommerce </title>
        <link href="/ecommerce/style/style.css" rel="stylesheet" type="text/css" />
        <script src="/ecommerce/javascript/ajax.js"></script>
        <script src="/ecommerce/javascript/action.js"></script>
        <script src="/ecommerce/javascript/autoaction.js"></script>
    </head>
    
<body>
        
        <div id="dim" class="dim" onclick="toggle('what_todo_input_div', 'new_option_value', 'new_option_value')"></div>
        
        <div id="what_todo_input_div" class="what_todo_input_div round_border" style='display:none;'>
            <form action="object.php" method="post">
                <div class="code_heading">
                    Login
                </div>
                <label>Email / Phone number</label>
                <input type="text" name="access_name_input" id="access_name_input" class="text_input" placeholder="Enter Access Name" />
                <label>Password</label>
                <input type="password" name="access_code_input" id="access_code_input" class="text_input " placeholder="Enter Access Code" />
                <input type="submit" id="submit_what_tod_btn" value="Login" class="submit_btn round_border" onclick="send_what_todo()"/>
                <input type="hidden" name="new_option_value" id="new_option_value" value="0" />
                <input type="hidden" name="send_id" id="send_id" value="0"/>
            </form>
            <label class="label">
                <a href="register.php">No account? Register</a>
            </label>
        </div>
        
        <div id="header" class="header" >
            <a href="#menu" onclick="toggle_one('menu')"> 
                <div class="menu container" onclick="menu_roll(this)">
                  <div class="bar1"></div>
                  <div class="bar2"></div>
                  <div class="bar3"></div>
                </div> 
            </a>
            <div class="logo">
                L.O.G.O
            </div>
            <div class='search_index_holder round_border' style="display:none;">
                <input type="text" name='index_search_input' id='index_search_input' class='index_text_input' placeholder='Search customer' />
                <input type='submit' name='submit_search_customer' id='submit_search_customer' value='Search' class='search_submit_btn' onclick='search_customer_name("customer_search_input")'/>
                <div id='customer_search_output' class='search_output border' style='display:none;'></div>
            </div>
        </div>
        
        <a href='index.php'>
            <button class='go_shopping_btn'>
                Go Shopping
            </button>
        </a>
        
        <div id='menu' class='header_menu' style='display:none;'>
            <?php 
                require '/class/Menu.php';
                $menuObject = new Menu();
                $menuObject->menu()
            ?>
        </div>
        
        
        <div class="content">
            <?php
                //checkout a sale
                require '/class/Checkout.php';
                $checkoutObject = new Checkout();
                $checkoutObject->checkout();
            ?>
        </div>
        
    </body>
</html>
