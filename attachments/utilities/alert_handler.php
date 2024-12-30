<?php
    function unsetAlert(){
        if (isset($_SESSION['ALERT'])){
            unset($_SESSION['ALERT']);
        }
    }

    function setAlert($message, $type){
        $alertObject["MESSAGE"] = $message;
        $alertObject["TYPE"] = $type;
        $_SESSION['ALERT'] = $alertObject;
    }
?>