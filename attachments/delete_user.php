<?php
    include_once "db_conn.php";
    include_once "utilities/log_handler.php";

    if(isset($_GET['item_id'])){
        $queryString = "DELETE FROM user WHERE id = ".$_GET['item_id']."";
        $result = $conn->query($queryString);

        createLog($_SESSION['ID'], "DELETE_USER", $conn);
        header("location: admin_dashboard.php");
    }
    header("location: admin_dashboard.php?err=true");
?>