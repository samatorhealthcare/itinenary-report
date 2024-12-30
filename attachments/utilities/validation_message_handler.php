<?php
    function setValidationErrorMessage($fieldname, $message){
        $_SESSION['ERROR'][$fieldname] = $message;
    }

    function destroyValidationErrorMessages(){
        unset($_SESSION['ERROR']);
    }
?>