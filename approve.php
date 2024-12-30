<?php
    session_start();
    include_once "db_conn.php";  
    include_once "utilities/log_handler.php";
    include_once "utilities/alert_handler.php";

    

    function updateReportStatusManager($report_id, $updated_status, $status_role, $conn){
        $queryString = "UPDATE report SET status = ".$updated_status.", status_m = ".$status_role." WHERE id = ".$report_id."";
        $conn->query($queryString);
    }

    function updateReportStatusGeneralManager($report_id, $updated_status, $status_role, $conn){
        $queryString = "UPDATE report SET status = ".$updated_status.", status_gm = ".$status_role." WHERE id = ".$report_id."";
        $conn->query($queryString);
    }

    function updateReportStatusDirector($report_id, $updated_status, $status_role, $conn){
        $queryString = "UPDATE report SET status = ".$updated_status.", status_dir = ".$status_role." WHERE id = ".$report_id."";
        $conn->query($queryString);
    }

    function updateReportStatusSupervisor($report_id, $updated_status, $status_role, $conn){
        $queryString = "UPDATE report SET status = ".$updated_status.", status_spv = ".$status_role." WHERE id = ".$report_id."";
        $conn->query($queryString);
    }

    function addReportAction($type, $report_id, $comment, $conn){
        $queryString = "INSERT INTO report_action (type, user, comment, report_id) values (".$type.", ".$_SESSION['ID'].", '".$comment."', ".$report_id.")";
        $conn->query($queryString);
    }

   
   
    if ($_POST['item_id']){
            
        // Check whether the current user's role is allowed to approve or reject this report
        $queryString = "SELECT * from report WHERE id=".$_POST['item_id']."";

        $result = $conn->query($queryString);

        $report = mysqli_fetch_assoc($result);
        
        
        // Cek apakah report ini boleh di approve / tidak

        if ($_SESSION['ID'] == $report['need_approval_by'] || $_SESSION['ID'] == $report['need_approval_by_2'] || $_SESSION['ID'] == $report['need_approval_by_3'] || $_SESSION['ROLE'] === "director" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager"){
            $newStatus = 0;
            $statusAvailableManager = 0;
            $statusAvailableGeneralManager = 0;
            $statusAvailableDirector = 0;
            $statusAvailableSupervisor = 0;

            if (isset($_POST['approve'])){
                if ($_SESSION['ROLE'] == "manager"){
                    $newStatus = 1;
                    $statusAvailableManager = 1;
                }
                elseif ($_SESSION['ROLE'] == "gmanager"){
                    $newStatus = 2;
                    $statusAvailableGeneralManager = 2;
                }
                elseif ($_SESSION['ROLE'] == "director"){
                    $newStatus = 3;
                    $statusAvailableDirector = 3;
                }
                elseif ($_SESSION['ROLE'] == "supervisor"){
                    $newStatus = 4;
                    $statusAvailableSupervisor = 4;
                }
            }
            else if(isset($_POST['reject'])){
                $newStatus = -1;
            }

            $queryString = "SELECT * FROM user WHERE id=".$_SESSION['ID']."";
            $result = $conn->query($queryString);

            $user = mysqli_fetch_assoc($result);

             
            
            if ($_SESSION['ROLE'] == "manager"){
                updateReportStatusManager($_POST['item_id'], $newStatus, $statusAvailableManager, $conn);
            }
            elseif ($_SESSION['ROLE'] == "gmanager"){
                updateReportStatusGeneralManager($_POST['item_id'], $newStatus, $statusAvailableGeneralManager, $conn);
            }
            elseif ($_SESSION['ROLE'] == "director"){
                updateReportStatusDirector($_POST['item_id'], $newStatus, $statusAvailableDirector,  $conn);
            }
            elseif ($_SESSION['ROLE'] == "supervisor"){
                updateReportStatusSupervisor($_POST['item_id'], $newStatus, $statusAvailableSupervisor,  $conn);
            }


            if($newStatus > 0){
                createLog($_SESSION['ID'], "APPROVE_REPORT", $conn);
                addReportAction(1,$_POST['item_id'], $_POST['input-comment'], $conn);
            }
            else{
                createLog($_SESSION['ID'], "REJECT_REPORT", $conn);
                addReportAction(0,$_POST['item_id'], $_POST['input-comment'], $conn);
            }
            setAlert("Berhasil menanggapi laporan", "success");
        }
        else {
            //TODO: Add error alert here
            setAlert("Anda tidak memiliki akses untuk melakukan aksi ini", "danger");
        }
    }
    header("location: manager_dashboard.php");
?>