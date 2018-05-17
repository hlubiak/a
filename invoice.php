<?php

    require 'backened.php';
    require 'dbconnect.php';
    $code->start_session();

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
        
        <div id="header" class="header" >
            <a href="#menu" onclick="toggle_one('menu')"> 
                <div class="menu container" onclick="menu_roll(this)">
                  <div class="bar1"></div>
                  <div class="bar2"></div>
                  <div class="bar3"></div>
                </div> 
            </a>
            <a href='index.php'>
            <div class="logo">
                L.O.G.O
            </div>
            </a>
            </button>
            <a href="checkout.php" > 
                <button class="checkout round_border" >
                    <?php
                        echo($code->number_of_cart_products());
                    ?>
                </button> 
            </a>
            <a href="#myaccount" onclick="get_todo('all')"> 
                <div class="myaccount" > 
                    <?php
                        $code->myaccount();
                    ?>
                </div> 
            </a>
        </div>
        
        <a href='index.php'>
            <button class='go_shopping_btn'>
                Go Shopping
            </button>
        </a>
    
        <div id='menu' class='header_menu' style='display:none;'>
            <?php 
                $code->menu();
            ?>
        </div>
        <div class="content holder" style="top:55px;">
            <?php
                /*update profile*/
                $code->user_invoice($_SESSION['user_id']);
            ?>
        </div>
        
    </body>
</html>
