<?php
    if(isset($_SESSION['id'])){
        // User is already logged in, redirect to their corresponding dashboards
        if ($_SESSION['role'] == "sales"){
            header("location: sales_dashboard.php");
        }
        elseif ($_SESSION['role'] == "admin"){
            header("location: admin_dashboard.php");
        }
        elseif ($_SESSION['role'] == "manager"){
            header("location: manager_dashboard.php");
        }
        elseif ($_SESSION['role'] == "gmanager"){
            header("location: manager_dashboard.php");
        }
        elseif ($_SESSION['role'] == "director"){
            header("location: manager_dashboard.php");
        }
    }
?>