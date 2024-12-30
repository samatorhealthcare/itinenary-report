<?php
    include_once "db_conn.php";
    include_once "utilities/user_director_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/util.php";

    session_start();

   
    if(isset($_POST['input-id'])){
        try {
            $reports_to_spv = isset($_POST['input-reports-to-lead-1']) ? $_POST['input-reports-to-lead-1'] : 0;
            $reports_to_manager = isset($_POST['input-reports-to-lead-2']) ? $_POST['input-reports-to-lead-2'] : 0;
            $reports_to_gmanager = isset($_POST['input-reports-to-lead-3']) ? $_POST['input-reports-to-lead-3'] : 0;
            $reports_to_director = isset($_POST['input-reports-to-lead-4']) ? $_POST['input-reports-to-lead-4'] : 0;

            $queryString = "SELECT * FROM user WHERE id = ".$_SESSION['ID'];
            $user = $conn->query($queryString)->fetch_assoc();

            if($reports_to_spv == NULL){
                $queryString = "UPDATE user SET reports_to_lead_2 = ".$reports_to_manager.", reports_to_lead_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
                $reportString = "UPDATE report SET need_approval_by_2 = ".$reports_to_manager.", need_approval_by_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
            }
            else if($reports_to_manager == NULL){
                $queryString = "UPDATE user SET reports_to_lead_1 = ".$reports_to_spv.", reports_to_lead_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
                $reportString = "UPDATE report SET need_approval_by = ".$reports_to_spv.", need_approval_by_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
            }
            else if($reports_to_gmanager == NULL){
                $queryString = "UPDATE user SET reports_to_lead_1 = ".$reports_to_manager.", reports_to_lead_2 = ".$reports_to_manager." WHERE id = ".$_POST['input-id']."";
                $reportString = "UPDATE report SET need_approval_by = ".$reports_to_spv.", need_approval_by_2 = ".$reports_to_manager." WHERE id = ".$_POST['input-id']."";
            }
            else if($reports_to_director == NULL){
                $queryString = "UPDATE user SET reports_to_lead_1 = ".$reports_to_spv.", reports_to_lead_2 = ".$reports_to_manager.", reports_to_lead_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
                $reportString = "UPDATE report SET need_approval_by = ".$reports_to_spv.", need_approval_by_2 = ".$reports_to_manager.", need_approval_by_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
            }
            else{
                $queryString = "UPDATE user SET reports_to_lead_1 = ".$reports_to_spv.", reports_to_lead_2 = ".$reports_to_manager.", reports_to_lead_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
                $reportString = "UPDATE report SET need_approval_by = ".$reports_to_spv.", need_approval_by_2 = ".$reports_to_manager.", need_approval_by_3 = ".$reports_to_gmanager." WHERE id = ".$_POST['input-id']."";
            }
            
            $result = $conn->query($queryString);

            //updateUserDirectorRelation($_POST['input-id'], $conn);

            if($_SESSION['role'] == "supervisor" || $_SESSION['role'] == "manager" || $_SESSION['role'] == "gmanager" ){
                header("location: manager_dashboard.php");
            }
            else{
                header("location: user_dashboard.php");
            }

            setAlert("Berhasil mengupdate atasan", "success");
        } 
        catch(Exception $error) {
             if($_SESSION['role'] == "supervisor" || $_SESSION['role'] == "manager" || $_SESSION['role'] == "gmanager" ){
                header("location: manager_dashboard.php");
            }
            else{
                header("location: user_dashboard.php");
            }
            
           setAlert("Gagal mengupdate atasan", "danger");
        }
    }
    else {
        
        header("location: logout.php");
    }
?>