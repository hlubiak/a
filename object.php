<?php

    require 'backened.php';
    require 'dbconnect.php';

    /*
    * the reason why i created switch statement is for if im gonna send more data separately
    */
    switch( $_POST['send_id'] ){
    
        case 1:
                require '/class/Database.php';
                require '/class/Authenticate.php';
                require '/class/EncryptPassword.php';
                //Authenticate user
                $AutObject = new Authenticate();
                echo ($AutObject->validateLogin($_POST['username'], $_POST['password']));
            break;
        
        case 2:

                //register user to website
                require '/class/Database.php';
                require '/class/Register.php';
                require '/class/EncryptPassword.php';
                //Authenticate user
                $registerObject = new Register();
                echo ($registerObject->registerUser($_POST['fullname'], $_POST['phonenumber'], $_POST['email'], 
                $_POST['password'] ));
            
            break;
        
        case 3:

            $user_id = 1;
            $code->mark( $user_id, $_POST['thread_id'], $_POST['done_id'], $_POST['mark'] );
            unset( $code );

            break;
        
        case 4:
                require '/class/Database.php';
                require '/class/LoginAdmin.php';
                require '/class/EncryptPassword.php';
                //Authenticate user
                $loginAdminObject = new LoginAdmin();
                $loginAdminObject->loginAdmin( $_POST['access_name_input'], $_POST['access_code_input'], $_POST['new_option_value'] );
            
            break;
        
        case 5:
            /*search product for user
             *redirect to index
             *show searched product
            */
            $searched_category = $_POST['search_category_id'];
            $searched_product_name = $_POST['product_search_input'];
            if(!empty($searched_product_name)){
                header("Location:index.php?searched_category=$searched_category&&searched_product=$searched_product_name");
            }else{
                header("Location:index.php");
            }
            break;
        
        case 6:

            $code->manage_customer_users($_POST['fetch_id']);
            unset( $code );

            break;
        
        case 7:
            
            require '/class/Database.php';
            require '/class/UploadProduct.php';
            //upload product
            $uploadProductObject = new UploadProduct();
                        
            /*upload product*/
            if(isset($_POST['upload_product']) || $_POST['upload_id'] == 1){
                
                $files = $_FILES['product_photo'];
                
                foreach($files['name'] as $position => $file_name){
                    /*move each file to the folder*/
                    move_uploaded_file( $files['tmp_name'][$position],"upload/".$files['name'][$position] );
                
                    /*get color of each file*/
                    $product_color_array = explode( ',' , $_POST['product_color'] );
                    for( $i= 0; $i < count( $product_color_array ); $i++ ){
                        /*insert first file into product table in the database*/
                        if($position == 0){
                            $upload_id = 0;
                        }elseif($position == 1){
                            $upload_id = 1;
                        } 
                        $uploadProductObject->uploadProduct($_POST['action_id'], $_POST['product_category_id'], $_POST['product_brand_name'], $_POST['product_name'], $files['name'][$position], $_POST['product_weight'], $_POST['product_description'], $product_color_array[$position], $_POST['product_price'], $_POST['product_type_id'], $_POST['gender'], $upload_id);
                            break;
                        }
                        
                }
                
            }//upload category, type and brand
            elseif($_POST['upload_id'] == 2){
                $uploadProductObject->uploadCategory($_POST['value_1'], $_POST['value_2']);
            }elseif( $_POST['upload_id'] == 3){
                $uploadProductObject->uploadType($_POST['value_1'], $_POST['value_2']);
            }elseif( $_POST['upload_id'] == 4){
                $uploadProductObject->uploadBrand($_POST['value_1'], $_POST['value_2']);
            }else{
                echo "Error";
            }
            
            break;
            
        case 8:
            //add to cart
            require '/class/Database.php';
            require '/class/AddToCart.php';
            //Authenticate user
            $addToCartObject = new AddToCart();
            $addToCartObject->addToCart($_POST['add_to_cart_product_id'], $_POST['add_to_cart_table_id'], 
                    $_POST['add_to_cart_product_size'], $_POST['add_to_cart_product_price'], 
                    $_POST['product_quantity']);
            
            break;
        
        case 9:
            require '/class/Database.php';
            require '/class/RemoveProduct.php';
            //remove product
            $removeProductObject = new RemoveProduct();
            $removeProductObject->removeProduct($_POST['array_position']);
            
            break;
        
        case 10:
            //confirm checkout
            require '/class/Database.php';
            require '/class/ConfirmCheckout.php';
            //Authenticate user
            $confirmCheckoutObject = new ConfirmCheckout();
            $confirmCheckoutObject->confirmCheckout($_POST['fullname'], $_POST['phonenumber'], $_POST['email'], 
                    $_POST['city'], $_POST['town'], $_POST['strnumber'], $_POST['sold_products'],
                    $_POST['amount'], $_POST['size'], $_POST['quantity']);
            

            break;
        
        case 11:
            $files = $_FILES['product_photo'];
            foreach($files['name'] as $position => $file_name){
            
                    /*move each file to the folder*/
                    move_uploaded_file( $files['tmp_name'][$position],"upload/".$files['name'][$position] );
                
                    /*get color of each file*/
                    $product_color_array = explode( ',' , $_POST['product_color'] );
                    for( $i= 0; $i < count( $product_color_array ); $i++ ){
                        
                    }
            }
            
            require '/class/Database.php';
            require '/class/UpdateProduct.php';
            //logout user
            $updateProductObject = new UpdateProduct();
            $updateProductObject->updateProduct($_POST['update_product_id'], $_POST['product_category_id'], $_POST['product_brand_name'], 
                    $_POST['product_name'], $files['name'][$position], $_POST['product_weight'], 
                    $_POST['product_description'], $product_color_array[$position], 
                    $_POST['product_price'], $_POST['product_type_id'], $_POST['gender']);
            break;
         
        case 12:
            require '/class/Database.php';
            require '/class/RetireProduct.php';
            //logout user
            $retireProductObject = new RetireProduct();
            $retireProductObject->retireProduct($_POST['product_id']);
            break;
        
        case 13:
            require '/class/Database.php';
            require '/class/RemoveUser.php';
            //logout user
            $removeUserObject = new RemoveUser();
            $removeUserObject->removeUser($_POST['user_id']);
            break;
        
        case 14:
            require '/class/Database.php';
            require '/class/Authenticate.php';
            //logout user
            $AutObject = new Authenticate();
            $AutObject->logout();
            
            break;
        
        case 15:
            
            require '/class/Database.php';
            require '/class/CloseSale.php';
            require '/class/EncryptPassword.php';
            //close sale
            $closeSaleObject = new CloseSale();
            $closeSaleObject->closeSale($_POST['user_id'], $_POST['fullname'],  $_POST['phonenumber'], $_POST['email'], 
                    $_POST['password'], $_POST['city'], $_POST['town'], $_POST['strnumber'], 
                    $_POST['product_id'], $_POST['amount'], $_POST['size'], 
                    $_POST['quantity']);
            break;
        
        case 16:
            session_start();
            require '/class/Database.php';
            require '/class/EncryptPassword.php';
            require '/class/UpdateUserInfo.php';
            //logout user
            $updateUserinfoObject = new UpdateUserInfo();
            $updateUserinfoObject->updateNameContact($_SESSION['user_id'], $_POST['fullname'],  $_POST['phonenumber'], 
                    $_POST['email'] 
                    );
            break;
        
        case 17:
            /*also send loggedin user_id to identify user */
            session_start();
            require '/class/Database.php';
            require '/class/EncryptPassword.php';
            require '/class/UpdateUserInfo.php';
            //logout user
            $updateUserinfoObject = new UpdateUserInfo();
            $updateUserinfoObject->updateAddress($_SESSION['user_id'], $_POST['city'],  $_POST['town'], 
                    $_POST['strnumber'] 
                    );
            break;
        
        case 18:
            session_start();
            require '/class/Database.php';
            require '/class/EncryptPassword.php';
            require '/class/UpdateUserInfo.php';
            //logout user
            $updateUserinfoObject = new UpdateUserInfo();
            $updateUserinfoObject->updatePassword($_SESSION['user_id'], $_POST['current_password'],  $_POST['new_password']);
            
            break;
        
        case 19:
            /*search product for admin*/
            require '/class/Database.php';
            require '/class/AdminSearchProduct.php';
            //search product
            $adminSearchProductObject = new AdminSearchProduct();
            $adminSearchProductObject->adminSearchProduct($_POST['product_name']);
            break;
        
        default:
            echo "Error...";
            
            break;
    }

?>
