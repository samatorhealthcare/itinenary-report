<?php
    session_start();
    include "db_conn.php";  

    if ($_POST['item_id']){
            
        // Check whether the current user's role is allowed to approve or reject this report
        $queryString = "SELECT * FROM report R, user U WHERE R.id=".$_POST['item_id']." AND R.REPORT_BY = U.id";

        $result = $conn->query($queryString);

        $report = mysqli_fetch_assoc($result);

        if($report['reports_to'] == $_SESSION['id']){
            $newStatus = 0;
            if(isset($_POST['approve'])){
                if($_SESSION['role'] == "manager"){
                    $newStatus = 1;
                }            
                else if($_SESSION['role'] == "gmanager"){
                    $newStatus = 2;
                }
                else if($_SESSION['role'] == "director"){
                    $newStatus = 3;
                }
            }
            else if(isset($_POST['reject'])){
                $newStatus = -1;
            }

            // Update report here 

            // Upload any comments added by the user

            else {
                // Don't think you are supposed to go here, unless you spoof
            }
        }
        else {
            // Not allowed to do anything to the report
        }
    }
?>