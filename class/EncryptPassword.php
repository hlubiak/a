<?php

    class EncryptPassword{
        //encrypt password
        function encryptInput($passwordKey, $encrypting_input ){
            $encryptedPassword = hash_pbkdf2( "sha512", $passwordKey, $encrypting_input, 1000, 200 );
            return $encryptedPassword;
        }
        
    }
