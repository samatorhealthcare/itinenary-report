<?php
    include_once "db_conn.php";

    if(isset($_POST['input-id'])){
        $emp_id = isset($_POST['input-emp-id']) ? $_POST['input-emp-id'] : '';
        $reports_to = $_POST['input-reports-to'];
        $role = $_POST['input-role'];
        $queryString = "UPDATE user SET emp_id='".$emp_id."', reports_to = ".$reports_to.", role = '".$role."' WHERE id = ".$_POST['input-id']."";
        $result = $conn->query($queryString);
        
        header("location: admin_dashboard.php");
    }
    header("location: admin_dashboard.php?err=true");
?>