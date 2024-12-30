<?php
    session_start();
    include_once "utilities/sanitizer.php";
    include_once "db_conn.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/alert_handler.php";
    include "logged_user_check.php";

    if(isset($_POST)){
        $id = sanitizeInput($_SESSION['ID']);
        $newPassword = sanitizeInput($_POST['input-password']);
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $requireRegister = 0;

        if($_SESSION['ROLE'] === "admin"){
            $requireRegister = 1;
        }

        if(isset($_POST['id'])){
            $id = $_POST['id'];
        }

        $queryString = "UPDATE user SET password = '".$hashedPassword."', require_register = ".$requireRegister." WHERE id = ".$id;
        $queryItinerary= "UPDATE user SET password = '".$hashedPassword."', require_register = ".$requireRegister." WHERE id = ".$id;
        
        try {
            if($conn->query($queryString)){
                $connDB->query($queryItinerary);
                if($_SESSION['ROLE'] === "admin"){
                    setAlert("Sukses mengubah password user", "success");
                }
                if($_SESSION['ROLE'] === "sales"){
                    header("location: user_dashboard.php");
                }
                else if($_SESSION['ROLE'] === "supervisor" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager" || $_SESSION['ROLE'] === "director"){
                    header("location: manager_dashboard.php");
                }
                else if($_SESSION['ROLE'] === "admin"){
                    header("location: admin_dashboard.php");
                }
            }
            else {
                throw new Exception("Error");
            }
        }
        catch (Exception $e){
            setAlert("Suatu error terjadi, harap mengontak admin", "warning");
            header("location: logout.php");
        }
    }
?>