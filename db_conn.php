<?php 
    // $dbservername = "localhost";
    // $dbusername = "root";
    // $dbpassword = "";

    // $db_name = "sales_report";

    $dbservername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $db_name = "u322572271_sales_report";

    $conn = mysqli_connect($dbservername, $dbusername, $dbpassword, $db_name);

    // if (!$conn){
    //     echo "Connection failed !";
    // }
    if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>