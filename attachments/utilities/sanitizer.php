<?php
    function sanitizeInput($text){
        //Sanitize here
        return strip_tags($text);
    }

    function validateInput($input, $validation_type){
        if($validation_type == "STRING_NO_SYMBOL"){
            $result = preg_match('/[^\w\s]/', $input);

            if($result){
                return true;
            }
            else {
                return false;
            }
        }
        else if($validation_type == "STRING_NUMBER_ONLY"){
            if(filter_var($input, FILTER_VALIDATE_INT)){
                return true;
            }
            else {
                return false;
            }
        }
        else if($validation_type == "STRING_EMAIL"){
            if(filter_var($input, FILTER_VALIDATE_EMAIL)){
                return true;
            }
            else {
                return false;
            }
        }
    }

    function isEmptyValidation($value){
        if(isset($value)){
            return true;
        }
        return false;
    }
?>