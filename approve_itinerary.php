<?php
    session_start();
    include_once "db_conn_itinerary.php";  
    include_once "utilities/log_handler.php";
    include_once "utilities/alert_handler.php";

    
    // for estimasi
    function updateReportStatusManager($report_id, $updated_status, $status_role, $connDB){
        $queryString = "UPDATE full_report SET status = ".$updated_status.", status_estimasi_m = ".$status_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function updateReportStatusGeneralManager($report_id, $updated_status, $status_role, $connDB){
        $queryString = "UPDATE full_report SET status = ".$updated_status.", status_estimasi_gm = ".$status_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function updateReportStatusDirector($report_id, $updated_status, $status_role, $connDB){
        $queryString = "UPDATE full_report SET status = ".$updated_status.", status_estimasi_dir = ".$status_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function updateReportStatusSupervisor($report_id, $updated_status, $status_role, $connDB){
        $queryString = "UPDATE full_report SET status = ".$updated_status.", status_estimasi_spv = ".$status_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function addReportAction($type, $report_id, $comment, $connDB){
        $queryString = "INSERT INTO report_action (type, user, comment, report_id) values (".$type.", ".$_SESSION['ID'].", '".$comment."', ".$report_id.")";
        $connDB->query($queryString);
    }

    //for realisasi
    function updateReportStatusRealisasiManager($report_id, $updated_status, $status_komentar_role, $connDB){
        $queryString = "UPDATE full_report SET status_komentar_realisasi = ".$updated_status.", status_komentar_m = ".$status_komentar_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function updateReportStatusRealsasiGeneralManager($report_id, $updated_status, $status_komentar_role, $connDB){
        $queryString = "UPDATE full_report SET status_komentar_realisasi = ".$updated_status.", status_komentar_gm = ".$status_komentar_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function updateReportStatusRealisasiDirector($report_id, $updated_status, $status_komentar_role, $connDB){
        $queryString = "UPDATE full_report SET status_komentar_realisasi = ".$updated_status.", status_komentar_dir = ".$status_komentar_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function updateReportStatusRealisasiSupervisor($report_id, $updated_status, $status_komentar_role, $connDB){
        $queryString = "UPDATE full_report SET status_komentar_realisasi = ".$updated_status.", status_komentar_spv = ".$status_komentar_role." WHERE id = ".$report_id."";
        $connDB->query($queryString);
    }

    function addReportRealisasiAction($type, $report_id, $comment, $connDB){
        $queryString = "INSERT INTO report_action (type, user, comment, report_id) values (".$type.", ".$_SESSION['ID'].", '".$comment."', ".$report_id.")";
        $connDB->query($queryString);
    }

   
   
    if ($_POST['item_id']){
            
        // Check whether the current user's role is allowed to approve or reject this report
        $queryString = "SELECT * FROM full_report WHERE id='" . $_POST['item_id'] . "'";
    
        $result = $connDB->query($queryString);

        $report = mysqli_fetch_assoc($result);
        
        
        // Cek apakah report ini boleh di approve / tidak
        if ($_SESSION['ID'] == $report['need_approval_by'] || $_SESSION['ID'] == $report['need_approval_by_2'] || $_SESSION['ID'] == $report['need_approval_by_3'] || $_SESSION['ID'] == $report['need_approval_by_4'] || $_SESSION['ID'] == $report['need_approval_by_5'] || $_SESSION['ROLE'] === "director" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager"){
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
            $result = $connDB->query($queryString);

            $user = mysqli_fetch_assoc($result);

            
            if ($_SESSION['ROLE'] == "manager"){
                updateReportStatusManager($_POST['item_id'], $newStatus, $statusAvailableManager, $connDB);
            }
            elseif ($_SESSION['ROLE'] == "gmanager"){
                updateReportStatusGeneralManager($_POST['item_id'], $newStatus, $statusAvailableGeneralManager, $connDB);
            }
            elseif ($_SESSION['ROLE'] == "director"){
                updateReportStatusDirector($_POST['item_id'], $newStatus, $statusAvailableDirector,  $connDB);
            }
            elseif ($_SESSION['ROLE'] == "supervisor"){
                updateReportStatusSupervisor($_POST['item_id'], $newStatus, $statusAvailableSupervisor,  $connDB);
            }


            // if($newStatus > 0){
            //     createLog($_SESSION['ID'], "APPROVE_REPORT", $connDB);
            //     addReportAction(1,$_POST['item_id'], $_POST['input-comment'], $connDB);
            // }
            // else{
            //     createLog($_SESSION['ID'], "REJECT_REPORT", $connDB);
            //     addReportAction(0,$_POST['item_id'], $_POST['input-comment'], $connDB);
            // }
            // setAlert("Berhasil menanggapi laporan", "success");
                if ($newStatus > 0) {
                createLog($_SESSION['ID'], "APPROVE_REPORT", $connDB);

                // First, get the sppd of the current report by item_id
                $queryString = "SELECT sppd FROM full_report WHERE id=" . $_POST['item_id'];
                $result = $connDB->query($queryString);
                $report = mysqli_fetch_assoc($result);
                $sppd = $report['sppd'];

                // Now, fetch all reports that have the same sppd
                $queryString = "SELECT id FROM full_report WHERE sppd='" . $sppd . "'";
                $result = $connDB->query($queryString);
                
                // Loop through all the reports with the same sppd and add the approval comment
                while ($row = mysqli_fetch_assoc($result)) {
                    addReportAction(1, $row['id'], $_POST['input-comment'], $connDB);
                }
            } else {
                createLog($_SESSION['ID'], "REJECT_REPORT", $connDB);

                // Fetch the sppd of the current report by item_id
                $queryString = "SELECT sppd FROM full_report WHERE id=" . $_POST['item_id'];
                $result = $connDB->query($queryString);
                $report = mysqli_fetch_assoc($result);
                $sppd = $report['sppd'];

                // Fetch all reports that have the same sppd
                $queryString = "SELECT id FROM full_report WHERE sppd='" . $sppd . "'";
                $result = $connDB->query($queryString);

                // Loop through all the reports with the same sppd and add the rejection comment
                while ($row = mysqli_fetch_assoc($result)) {
                    addReportAction(0, $row['id'], $_POST['input-comment'], $connDB);
                }
            }

        }
        else {
            //TODO: Add error alert here
            setAlert("Anda tidak memiliki akses untuk melakukan aksi ini", "danger");
        }
    }
    header("location: manager_dashboard_itinerary.php");
?>