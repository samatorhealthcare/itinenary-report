<?php 
    session_start();

    if(isset($_SESSION['ID'])){
        // Log user out
        session_unset();
        // session_destroy();
    }

    header('location: index.php');
?>