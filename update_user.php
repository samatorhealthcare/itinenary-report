<?php
    include_once "db_conn.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/user_director_handler.php";
    include_once "utilities/alert_handler.php";

    if(isset($_POST['input-id'])){
        try {
            $username = $_POST['input-username'];
            $reports_to = isset($_POST['input-reports-to']) ? $_POST['input-reports-to'] : 0;
            $role = $_POST['input-role'];

            $queryString = "UPDATE user SET username='".$username."', reports_to = ".$reports_to.", role = '".$role."' WHERE id = ".$_POST['input-id']."";
            $queryItinerary = "UPDATE user SET username='".$username."', reports_to = ".$reports_to.", role = '".$role."' WHERE id = ".$_POST['input-id']."";
            
            $result = $conn->query($queryString);
            $resultItinerary = $connDB->query($queryItinerary);

            updateUserDirectorRelation($_POST['input-id'], $conn);

            header("location: admin_dashboard.php");

            setAlert("Berhasil mengupdate user", "success");
        } 
        catch(Exception $error) {
            header("location: admin_dashboard.php");

            setAlert("Gagal mengupdate user", "danger");
        }
    }
    else {
        header("location: logout.php");
    }
?>