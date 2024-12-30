<?php 
    // $dbservername = "localhost";
    // $dbusername = "root";
    // $dbpassword = "";

    // $db_name = "sales_report";

    $dbservername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $db_name = "itenenary_report";

    $connDB = mysqli_connect($dbservername, $dbusername, $dbpassword, $db_name);

    if (!$connDB){
        echo "Connection failed !";
    }
?>