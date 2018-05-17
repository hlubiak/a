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
                <input type="text" name="username" id="username" class="text_input" placeholder="Enter Access Name" />
                <label>Password</label>
                <input type="password" name="password" id="passwordt" class="text_input " placeholder="Enter Access Code" />
                <input type="submit" id="submit_login" value="Login" class="submit_btn round_border" onclick="send_what_todo()"/>
                <input type="hidden" name="new_option_value" id="new_option_value" value="0" />
                <input type="hidden" name="send_id" id="send_id" value="1"/>
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
            <a href='index.php'>
            <div class="logo">
                L.O.G.O
            </div>
            </a>
            <button class="search_btn" onclick="toggle_one('search_div')">
                <img src="/ecommerce/upload/search_icon2.png"  class="img_fit"/>
            </button>
            <div id='search_div' class='search_div' style="display:none; right:15%;">
                <?php 
                    include '/class/FilterProductByCategorySearch.php';
                    $fPBCSObject = new FilterProductByCategorySearch();
                    $fPBCSObject->filterProductsByCategoryForSearch();
                ?>
                <span id="search_output"></span>
            </div>
            <a href="checkout.php" > 
                <button class="checkout round_border" >
                    <?php
                        include '/class/ProductNumberInCart.php';
                        $productNumberInCartObject = new ProductNumberInCart();
                        $productNumberInCartObject->productNumberInCart();
                    ?>
                </button> 
            </a>
            <a href="#myaccount" onclick="get_todo('all')"> 
                <div class="myaccount" > 
                    <?php
                        include '/class/UserAccount.php';
                        $userAccountObject = new UserAccount();
                        $userAccountObject->userAccount();
                    ?>
                </div> 
            </a>
        </div>
        
        <div id='menu' class='header_menu' style='display:none;'>
            <div></div>
            <?php 
                require '/class/Menu.php';
                $menuObject = new Menu();
                $menuObject->menu()
            ?>
        </div>
        
        <div class="content" style="top:55px;">
            <div>
                <a id="products_btn" class='fixed_width_btn' onclick="toggle_one('filter_holder')" > Filter Products </a>
                <div id="filter_holder" class="activity_holder" style="display:none;">
                    <?php 
                        require '/class/FilterProductByCategory.php';
                        $filterProductByCategoryObject = new FilterProductByCategory();
                        $filterProductByCategoryObject->filterProductByCategory()
                    ?>
                </div>
            </div>
            <?php
                /*switch products, if its the same product with different colors or style or all products*/
                if(isset($_GET['product_name'])){
                    $product_to_show = $_GET['product_name'];
                    $purpose_id = $_GET['purpose_id'];
                }else{
                   $product_to_show = 0;
                  $purpose_id = 0;
                }
                
                /*filter value*/
                if(isset($_GET['filter'])){
                    $filter = $_GET['filter'];
                }else{
                    $filter = "";
                }
                
                /*filter value*/
                if(isset($_GET['searched_category']) && isset($_GET['searched_product'])){
                    $search = $_GET['searched_product'];
                }else{
                    $search = "";
                }
                /*show product feed or suggestion feed*/
                if(isset($_GET['suggestion'])){
                    require '/class/SuggestProduct.php';
                    $suggestProductObject = new SuggestProduct();
                    $suggestProductObject->suggestProduct($_GET['product']);
                }else{
                    require '/class/UserFeed.php';
                    //show products
                    $userFeedObject = new UserFeed();
                    $userFeedObject->userFeed($product_to_show, $purpose_id, 0, $filter, $search);
                }
            ?>
        </div>
    </body>
</html>
