<?php
    session_start();
    include_once "db_conn.php";  
    include_once "utilities/log_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/util.php";
    include "logged_user_check.php";
   
    

    function updateReportAction($date, $comment, $reportId, $conn){
        
        $queryString = "UPDATE report_action SET action_last_update = '".$date."', comment = '".$comment."' WHERE user = '".$_SESSION['ID']."' AND report_id='".$reportId."'";
        $conn->query($queryString);

    }
     if (isset($_GET['item_id'])){

     }

     function addReportAction($type, $report_id, $comment, $conn){
        $queryString = "INSERT INTO report_action (type, user, comment, report_id) values (".$type.", ".$_SESSION['ID'].", '".$comment."', ".$report_id.")";
        $conn->query($queryString);
    }

   
    if ($_POST['item_id']){
            
          $date = date("Y-m-d H:i:s");
          
          //fetchCurrentTimestamp();
          $edit_comment = $_POST['edit-comment'];
          $queryComment= "UPDATE report_action SET comment = ".$edit_comment." ";
            
            // Cek sudah dikomentari dengan user, kemudian edit komentar
           createLog($_SESSION['ID'], "COMMENT_EDITED", $conn);
           updateReportAction($date, $_POST['edit-comment'], $_POST['item_id'], $conn);
            
           setAlert("Berhasil memperbarui tanggapan", "success");

    }
    header("location: manager_dashboard.php");
?>