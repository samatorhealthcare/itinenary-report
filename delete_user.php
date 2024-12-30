
<?php
    session_start();
    include_once "db_conn.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/log_handler.php";
    include_once "logged_user_check.php";
    include_once "utilities/alert_handler.php";

    if(isset($_GET['item_id'])){
        if($_SESSION['ROLE'] === "admin"){
            $queryString = "DELETE FROM user WHERE id = ".$_GET['item_id']."";
            $queryItinerary = "DELETE FROM user WHERE id = ".$_GET['item_id']."";
            $result = $conn->query($queryString);
            $resultItinerary = $connDB->query($queryItinerary);

            $queryString = "UPDATE user SET reports_to = 0 WHERE reports_to = ".$_GET['item_id'];
            $conn->query($queryString);

            createLog($_SESSION['ID'], "DELETE_USER", $conn);
            createLog($_SESSION['ID'], "DELETE_USER", $connDB);
            setAlert("Sukses menghapus user", "success");
            header("location: admin_dashboard.php");
        }
        else {
            header("location: logout.php");
        }
    }
    else {
        header("location: logout.php");
    }
?>