<?php 
    session_start();

    if(isset($_SESSION['id'])){
        // Log user out
        session_unset();
    }

    header('location: index.php');
?>