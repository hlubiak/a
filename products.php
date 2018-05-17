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
        <script src="/ecommerce/javascript/javascript.js"></script>
    </head>
    
    <body>
        <div id='dim' class='dim'></div>
        <div id="header" class="header" style="top:0;">
            <a  href="adminpanel.php" class="page_heading">
                 <h2> Admin Panel </h2>
             </a> 
             <div class="logo">
                 L.O.G.O
             </div>
             <div class='search_index_holder round_border' style="display:none;">
                 <input type="text" name='index_search_input' id='index_search_input' class='index_text_input' placeholder='Search customer' />
                 <input type='submit' name='submit_search_customer' id='submit_search_customer' value='Search' class='search_submit_btn' onclick='search_customer_name("customer_search_input")'/>
                 <div id='customer_search_output' class='search_output border' style='display:none;'></div>
             </div>
            <div class='logout_holder'>
                <form action='object.php' method='post'>
                    <input type='hidden' name='send_id' value='14'/>
                    <input type='submit' name='logout_btn' class='logout_btn' value='Logout' />
                </form>
            </div>
         </div>
        
        <div class="my_todolist_heading" >
            <input type="hidden" id="all" value="0" />
        </div>
        
        <div class="todo_content_holder" >
            
            <div class='todo_actions_div' >
                
                <div class='separate_div'>
                    <div>
                        <a id="products_btn" class='fixed_width_btn' onclick="toggle_one('activity_holder')" > Choose activity </a>
                        <div id="activity_holder" class="activity_holder" style="display:none;">
                            <a href="products.php?retire_id=all&&product=all&&action=0&&category=all&&option=1">
                                <button class="sub_sub_option_btn" >Products</button>
                            <a/>
                            <a href="products.php?retire_id=all&&product=all&&action=0&&category=all&&option=2">
                                <button class="sub_sub_option_btn" >Upload</button>
                            </a>
                            <a href="products.php?retire_id=all&&product=all&&action=0&&category=all&&option=3">
                                <button class="sub_sub_option_btn" >Analytics</button>
                            </a>
                            <a href="products.php?retire_id=all&&product=all&&action=0&&category=all&&option=4">
                                <button class="sub_sub_option_btn" >Invoices</button>
                            </a>
                        </div>
                    </div>
                    
                    <?php
                    //toggle options
                    include '/class/ChooseOption.php';
                    $chooseOptionObject = new ChooseOption();
                    ?>
                    
                    <a href="#products"> 
                        <div class='separate_div'  style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],1) ?>">
                            <a href="products.php?retire_id=all&&product=all&&action=0&&category=all&&option=1">
                                <button id="products_btn" class='right_red_todo_btn fixed_width_btn' style="background:#069;" > Products </button>
                            </a>
                            <div class="sub_options_holder">
                                <button class="sub_option_btn">All</button>
                                <a href="products.php?retire_id=all&&product=all&&action=0&&category=all&&option=1">
                                    <button class="sub_sub_option_btn" >Manage</button>
                                    <input type="hidden" name="_id" id="_id" value="1"/>
                                </a>
                                <button class="sub_option_btn">Current</button>
                                <a href="products.php?retire_id=0&&product=all&&action=0&&category=all&&option=1">
                                    <button class="sub_sub_option_btn">Manage</button>
                                    <input type="hidden" name="_id" id="_id" value="2"/>
                                </a>
                                <button class="sub_option_btn">Retired</button>
                                <a href="products.php?retire_id=1&&product=all&&action=0&&category=all&&option=1">
                                    <button class="sub_sub_option_btn">Manage</button>
                                    <input type="hidden" name="_id" id="_id" value="3"/>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="products_id" value="1" />
                    </a>
                    
                    <a href="#upload" > 
                        <div class='separate_div' style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],2) ?>">  
                            <button id="upload_btn" class='right_red_todo_btn fixed_width_btn'  style="background:#069;" > Upload </button>
                            <div class="sub_options_holder">
                                <button class="sub_option_btn">Product</button>
                                <a href="#uploadproduct">
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('upload_product_holder', 'upload_category_holder', 'upload_type_holder', 'upload_brand_holder')">Upload</button>
                                    <input type="hidden" name="_id" id="_id" value="4"/>
                                </a>
                                <button class="sub_option_btn">Product category</button>
                                <a href="#uploadproductcategory">
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('upload_category_holder', 'upload_product_holder', 'upload_type_holder', 'upload_brand_holder')">Upload</button>
                                    <input type="hidden" name="_id" id="_id" value="1"/>
                                </a>
                                <button class="sub_option_btn">Product type</button>
                                <a href="#uploadproducttype">
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('upload_type_holder', 'upload_category_holder', 'upload_product_holder', 'upload_brand_holder')">Upload</button>
                                    <input type="hidden" name="_id" id="_id" value="2"/>
                                </a>
                                <button class="sub_option_btn">Product brand</button>
                                <a href="#uploadproductbrand">
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('upload_brand_holder', 'upload_type_holder', 'upload_category_holder', 'upload_product_holder')">Upload</button>
                                    <input type="hidden" name="_id" id="_id" value="3"/>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="upload_id" value="2" />
                    </a>
                    
                    <a href="#analytics"> 
                        <div class='separate_div' style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],3) ?>"> 
                            <button id="analytics_btn" class='right_red_todo_btn fixed_width_btn' style="background:#069;" > Analytics </button>
                            <div class="sub_options_holder">
                                <button class="sub_option_btn" onclick="toggle_hide( 'sales_div', 'selling_products_analytics_holder', 'users_analytics_holder', 'searched_products_analytics_holder')">
                                    Sales
                                </button>
                                <a href="#managecustomers">
                                    <button class="sub_sub_option_btn" onclick="fetch_data('fetch_customer_users_id')">
                                        Yearly
                                    </button>
                                    <input type="hidden" name="_id" id="_id" value="1"/>
                                </a>
                                <a href="#monthly_sales_analytics">
                                    <button class="sub_sub_option_btn" >
                                       Monthly
                                    </button>
                                    <input type="hidden" name="_id" id="_id" value="1"/>
                                </a>
                                <button class="sub_option_btn" onclick="toggle_hide('selling_products_analytics_holder', 'sales_div', 'users_analytics_holder', 'searched_products_analytics_holder')">
                                    Products
                                </button>
                                <a href="#manageadministrators" >
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('selling_products_analytics_holder', 'sales_div', 'users_analytics_holder', 'searched_products_analytics_holder')">
                                        Selling
                                    </button>
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('searched_products_analytics_holder', 'sales_div', 'users_analytics_holder', 'selling_products_analytics_holder')">
                                        Searched
                                    </button>
                                    <input type="hidden" name="_id" id="_id" value="2"/>
                                </a>
                                <button class="sub_option_btn">Users</button>
                                <a href="#manageadministrators">
                                    <button class="sub_sub_option_btn" onclick="toggle_hide('users_analytics_holder','selling_products_analytics_holder', 'sales_div','searched_products_analytics_holder')">Manage</button>
                                    <input type="hidden" name="_id" id="_id" value="3"/>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="analytics_id" value="3" />
                    </a>
                
                    <a href="#invoices" > 
                        <div class='separate_div' style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],4) ?>"> 
                            <button id="invoices_btn" class='right_red_todo_btn fixed_width_btn' style="background:#069;"  > Invoices </button> 
                            <div class="sub_options_holder">
                                <button class="sub_option_btn">Paid</button>
                                <a href="#managecustomers">
                                    <button class="sub_sub_option_btn" onclick="fetch_data('fetch_customer_users_id')">Manage</button>
                                    <input type="hidden" name="_id" id="_id" value="1"/>
                                </a>
                                <button class="sub_option_btn">Outstanding</button>
                                <a href="#manageadministrators">
                                    <button class="sub_sub_option_btn" onclick="fetch_data('fetch_admin_users_id')">Manage</button>
                                    <input type="hidden" name="_id" id="_id" value="2"/>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" id="invoices_id" value="4" />
                    </a>
                
                    </div>
            
            <div id="what_todo_input_div" class="what_todo_input_div round_border" style="display:none;">
                <form action="object.php" method="post">
                    <div class="code_heading">
                        Enter Access Code
                    </div>
                    <label>Access Name</label>
                    <input type="text" name="access_name_input" id="access_name_input" class="text_input" placeholder="Enter Access Name" />
                    <label>Access Code</label>
                    <input type="password" name="access_code_input" id="access_code_input" class="text_input " placeholder="Enter Access Code" />
                    <input type="submit" id="submit_what_tod_btn" value="Submit" class="submit_btn round_border" onclick="send_what_todo()"/>
                    <input type="hidden" name="new_option_value" id="new_option_value" value="" />
                    <input type="hidden" name="send_id" id="send_id" value="4"/>
                </form>
            </div>
               
            <div id="output">  </div>
            <button id="loading"> Loading... </button>
            </div>
                
            <div id="feed" class="holder">
                
                <div id='upload_category_holder' class='upload_holder'>
                    <div class="label heading"> Upload Product Category </div>
                    <label class='label'> Product Category Name(eg : Sneaker or Handbag or laptop)</label>
                    <input type='text' id='category_name' class='text_inpu' />
                    <label class='label'> Selling Category </label>
                    <select id="selling_category" name="selling_category" class='text_inpu'>
                        <option value="1">Clothing</option>
                        <option value="2">Electronics</option>
                        <option value="3">Grocery</option>
                        <option value="4">Drinks</option>
                    </select>
                    <input type='hidden' id='upload_category_id' class='text_inpu' value='2' />
                    <input type='button' id='submit_category' value="submit" class='submit_btn' onclick='upload("upload_category_id", "category_name", "selling_category")' />
                    <span id="output2"></span>
                </div>

                <div id='upload_type_holder' class='upload_holder' >
                    <div class="label heading"> Upload Product Type </div>
                    <label class='label'> Type Name</label>
                    <input type='text' id='type_name' class='text_inpu' />
                    <input type='hidden' id='upload_type_id' class='text_inpu' value='3' />
                    <input type='button' id='submit_type' value="submit" class='submit_btn'  onclick='upload("upload_type_id", "type_name", "upload_type_id")' />
                    <span id="output3"></span>
                </div>

                <div id='upload_brand_holder' class='upload_holder' >
                    <div class="label heading"> Upload Brand Name </div>
                    <label class='label'> Brand Name</label>
                    <input type='text' id='brand_name' class='text_inpu' />
                    <input type='hidden' id='upload_brand_id' class='text_inpu' value='4' />
                    <input type='button' id='submit_type' value="submit" class='submit_btn'  onclick='upload("upload_brand_id", "brand_name", "upload_brand_id")' />
                    <span id="output4"></span>
                </div>
                
                <div id='selling_products_analytics_holder' class='upload_holder' >
                    Most selling products
                    <?php
                        include '/class/MostSellingProduct.php';
                        $mostSellingProductObject = new MostSellingProduct();
                        $mostSellingProductObject->mostSellingProduct();
                    ?>
                    <span id="output5"></span>
                </div>
                <div id='searched_products_analytics_holder' class='upload_holder' >
                    Most searched products
                    <?php
                        include '/class/MostSearchedProduct.php';
                        $mostSearchedProductObject = new MostSearchedProduct();
                        $mostSearchedProductObject->mostSearchedProduct();
                    ?>
                    <span id="output5"></span>
                </div>
                 <div id='users_analytics_holder' class='upload_holder' >
                    <?php
                        include '/class/UserAnalytics.php';
                        $userAnalyticsObject = new UserAnalytics();
                        $userAnalyticsObject->userAnalytics();
                    ?>
                    <span id="output5"></span>
                </div>
                <div style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],1)?>">
                    <div class="admin_search_product_holder">
                        <div>
                            Search by name
                        </div>
                        <input type="search" id="admin_search_product_input" placeholder='Search by name' onkeyup='admin_search_product("admin_search_product_input")'/>
                        <input type="submit" id="admin_input_submit_search_btn" value='Search'  onclick='admin_search_product("admin_search_product_input")' />
                        <div id='admin_search_product_output' class='search_div' style='display:none;' >
                            <span id='admin_searching_processor'>Searching...</span>
                        </div>
                    </div>
                    <?php
                        if(isset($_GET['search'])){
                            $searched_product_name = $_GET['search'];
                        }else{
                           $searched_product_name = ""; 
                        }
                        require '/class/AdminFeed.php';
                        $adminFeedObject = new AdminFeed();
                        $adminFeedObject->adminFeed(0, 1, $_GET['retire_id'], "", $searched_product_name);
                    ?>
                </div>
                <div style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],2)?>">
                    <?php
                        include '/class/UploadProductForm.php';
                        $uploadProductFormObject = new UploadProductForm();
                        $uploadProductFormObject->uploadProductForm( $_GET['retire_id'], $_GET['product'], $_GET['action'], $_GET['category'] );
                    ?>
                </div>
                <div id="sales_div" style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],3)?>">
                    <h3>Sales</h3>
                    <?php
                        include '/class/Sales.php';
                        $salesObject = new Sales();
                        $salesObject->sales("total");
                    ?>
                   
                    <canvas id="myChart" width="100" height="40"></canvas>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
                    <script>
                        var ctx = document.getElementById('myChart').getContext('2d');
                            var chart = new Chart(ctx, {
                                // The type of chart we want to create
                                type: 'line',

                                // The data for our dataset
                                data: {
                                    labels: [<?php $salesObject->sales("labels") ?>],
                                    datasets: [{
                                        label: "Yearly Sales performance ",
                                        backgroundColor: 'transparent',
                                        borderColor: 'rgb(255, 99, 132)',
                                        data: [<?php $salesObject->sales("data") ?>],
                                    }]
                                },

                                // Configuration options go here
                                options: {}
                            });     
                    </script>
                    
                    <div id="monthly_sales_analytics">
                        <canvas id="myChartMonths" width="100" height="40"></canvas>
                        <script>
                            var ctxMonths = document.getElementById('myChartMonths').getContext('2d');
                                var chart = new Chart(ctxMonths, {
                                    // The type of chart we want to create
                                    type: 'line',

                                    // The data for our dataset
                                    data: {
                                        labels: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
                                        datasets: [{
                                            label: "Monthly Sales performance",
                                            backgroundColor: 'transparent',
                                            borderColor: 'blue',
                                            data: [<?php $salesObject->sales("monthsData") ?>],
                                        }]
                                    },

                                    // Configuration options go here
                                    options: {}
                                });     
                        </script>
                    </div>
                </div>
                <div style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],4)?>">
                    <?php 
                        include '/class/AdminManageInvoice.php';
                        $adminManageInvoiceObject = new AdminManageInvoice();
                        $adminManageInvoiceObject->adminManageInvoice(1,0);
                    ?>
                </div>
                <div style="display:<?php $chooseOptionObject->chooseOption($_GET['option'],5)?>">
                    <?php
                        include '/class/Users.php';
                        $usersObject = new Users();
                        $usersObject->users();
                    ?>
                </div>
                
            </div>
            
        </div>
        
    </body>
</html>
