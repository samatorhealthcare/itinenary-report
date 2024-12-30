<?php
    session_start();
    include_once "db_conn.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/validation_message_handler.php";
    include_once "utilities/log_handler.php";
    include_once "utilities/user_director_handler.php";

    
    $input_reports_to_spv = isset($_POST['input-reports-to-lead-1']) ? $_POST['input-reports-to-lead-1'] : '';
    $input_reports_to_manager = isset($_POST['input-reports-to-lead-2']) ? $_POST['input-reports-to-lead-2'] : '';
    $input_reports_to_gmanager = isset($_POST['input-reports-to-lead-3']) ? $_POST['input-reports-to-lead-3'] : '';
    $input_reports_to_director = isset($_POST['input-reports-to-lead-4']) ? $_POST['input-reports-to-lead-4'] : '';  
    

    $input_reports_to_spv = sanitizeInput($input_reports_to_spv);
    $input_reports_to_manager = sanitizeInput($input_reports_to_manager);
    $input_reports_to_gmanager = sanitizeInput($input_reports_to_gmanager);
    $input_reports_to_director = sanitizeInput($input_reports_to_director);
    
    
    //Input Validation
    $passedValidation = true;

    if($passedValidation){

        $queryString = "SELECT * FROM user WHERE id ='".$_SESSION['ID']."'";
        $report = $conn->query($queryString);
        $user_login = mysqli_fetch_assoc($report);

        $report_spv = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_1']."'";
        $report_s = $conn->query($report_spv);
        $reports_to_spv = mysqli_fetch_assoc($report_s);

        $report_manager = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_2']."'";
        $result_m = $conn->query($report_manager);
        $reports_to_manager = mysqli_fetch_assoc($result_m);

        $report_gmanager = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_3']."'";
        $result_gm = $conn->query($report_gmanager);
        $reports_to_gmanager = mysqli_fetch_assoc($result_gm);

        $report_director = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_4']."'";
        $result_dr = $conn->query($report_director);
        $reports_to_director = mysqli_fetch_assoc($result_dr);


        $user = $conn->query($queryString)->fetch_assoc();


        //$queryString = '';
        
        if($user_login['role'] == "supervisor"){
               $queryString = 'UPDATE user SET reports_to_lead_2='.$input_reports_to_manager.', reports_to_lead_3='.$input_reports_to_gmanager.' WHERE id='.$_SESSION["ID"];
        }
        elseif($user_login['role'] == "manager"){
                $queryString = 'UPDATE user SET reports_to_lead_3='.$input_reports_to_gmanager.' WHERE id='.$_SESSION["ID"];
        }
        elseif($user_login['role'] == "gmanager"){
                
                $queryString = 'UPDATE user SET reports_to_lead_4='.$input_reports_to_director.' WHERE id='.$_SESSION["ID"];
        }
        elseif($user_login['role'] == "sales" && $input_reports_to_spv == NULL){
                $queryString = 'UPDATE user SET reports_to_lead_2='.$input_reports_to_manager.', reports_to_lead_3='.$input_reports_to_gmanager.' WHERE id='.$_SESSION["ID"];
        }
        else{
             $queryString = 'UPDATE user SET reports_to_lead_1='.$input_reports_to_spv.', reports_to_lead_2='.$input_reports_to_manager.', reports_to_lead_3='.$input_reports_to_gmanager.' WHERE id='.$_SESSION["ID"];
        }

        //$queryString = "INSERT INTO user (reports_to_lead_1, reports_to_lead_2) VALUES (".$input_reports_to_spv."', ".$input_reports_to_manager."')" ;
        

        try{
            if($conn->query($queryString) === TRUE){
                createLog($_SESSION['ID'], "CREATE_ATASAN", $conn);

                $queryString = "SELECT * FROM user WHERE id = '".$_SESSION["ID"]."'";
                $user = $conn->query($queryString)->fetch_assoc();
                //updateUserDirectorRelation($user_login['reports_to_lead_3'], $conn);
                setAlert("Berhasil menentukan atasan", "success");
                if($_SESSION['ROLE'] === "sales"){
                    header("location: user_dashboard.php");
                }
                else if($_SESSION['ROLE'] === "supervisor" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager"){
                    header("location: manager_dashboard.php");
                }
            }
        }
        catch (Exception $e){
            setAlert("Gagal menentukan atasan, harap kontak admin", "warning");
            if($_SESSION['ROLE'] === "sales"){
                header("location: user_dashboard.php");
            }
            else if($_SESSION['ROLE'] === "supervisor" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager"){
                header("location: manager_dashboard.php");
            }
        }
    }
    else {
       if($_SESSION['ROLE'] === "sales"){
            header("location: user_dashboard.php");
        }
        else if($_SESSION['ROLE'] === "supervisor" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager"){
            header("location: manager_dashboard.php");
        }
    }
?>