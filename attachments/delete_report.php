<?php
    session_start();
    require_once 'db_conn.php';
    require_once 'utilities/sanitizer.php';
    require_once 'utilities/log_handler.php';

    function deleteReport($conn){
        $reportId = $_GET['item_id'];

        $queryString = 'DELETE FROM report WHERE id='.$reportId.'';

        if($conn->query($queryString)){
            createLog($_SESSION['ID'], "DELETE_REPORT", $conn);
            return true;
        }
        else{
            return false;
        }
    }
    // Check permissions here
    //TODO: Add permission check for delete, and checks for the request

    if(isset($_GET['item_id'])){
        deleteReport($conn);

        if (($_SESSION['ROLE']) == "sales"){
            header("location: user_dashboard.php");
        }
        else if (($_SESSION['ROLE'] == "manager") || $_SESSION['ROLE'] == "gmanager" || $_SESSION['ROLE'] == "director"){
            header("location: manager_dashboard.php");
        }
        else {
            // You're not supposed to be here
        }
    }
?>