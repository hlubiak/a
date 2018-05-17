<?php

    require 'backened.php';
    require 'dbconnect.php';

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
        
        <div id="what_todo_input_div" class="what_todo_input_div round_border white_holder">
                <div class='logo logo_vertical_mid'>
                    L.O.G.O
                </div>
                <form action="object.php" method="post" class='what_todo_input_div'>
                    <h2>
                        Register
                    </h2
                    <label>Fullname</label>
                    <input type="text" name="fullname" id="fullname" class="text_input" placeholder="" />
                    <label>Email</label>
                    <input type="text" name="email" id="email" class="text_input" placeholder="" />
                    <label>Phone number</label>
                    <input type="text" name="phonenumber" id="phonenumber" class="text_input" placeholder="" />
                    <label>Password</label>
                    <input type="password" name="password" id="password" class="text_input " placeholder="Enter Access Code" />
                    <input type="submit" id="submit_register_btn" value="Submit" class="submit_btn round_border" onclick="send_what_todo()"/>
                    <input type="hidden" name="send_id" id="send_id" value="2"/>
                </form>
            </div>
            
        </div>
        
    </body>
</html>
