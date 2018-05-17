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
                    <h1>
                        Admin Panel
                    </h1>
                    <h2>
                        Login
                    </h2
                    <label>Username</label>
                    <input type="text" name="access_name_input" id="access_name_input" class="text_input" placeholder="Enter Access Name" />
                    <label>Passcode</label>
                    <input type="password" name="access_code_input" id="access_code_input" class="text_input " placeholder="Enter Access Code" />
                    <input type="submit" id="submit_what_tod_btn" value="Login" class="submit_btn btn_100" />
                    <input type="hidden" name="new_option_value" id="new_option_value" value="1" />
                    <input type="hidden" name="send_id" id="send_id" value="4"/>
                </form>
            </div>
            
        </div>
        
    </body>
</html>
