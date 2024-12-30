<?php
    session_start();
    require_once 'db_conn.php';
    require_once 'db_conn_itinerary.php';
    require_once 'utilities/sanitizer.php';
    require_once 'utilities/log_handler.php';
    require_once 'logged_user_check.php';
    require_once 'utilities/alert_handler.php';

    // function deleteReport($conn, $connDB){
    //     $reportId = $_GET['item_id'];

    //     $queryString = 'DELETE FROM report WHERE id='.$reportId.'';
    //     $queryItinerary = 'DELETE FROM full_report WHERE id='.$reportId.'';
        
    //     if($conn->query($queryString)){
    //         $connDB->query($queryItinerary);
    //         createLog($_SESSION['ID'], "DELETE_REPORT", $conn);
    //         createLog($_SESSION['ID'], "DELETE_REPORT", $connDB);
    //         return true;
    //     }
    //     else{
    //         return false;
    //     }
    // }
    function deleteReport($conn, $connDB) {
        $reportId = $_GET['item_id'];

        // Step 1: Retrieve the upload_at and sppd for the report ID
        $querySelect = "SELECT upload_at, sppd FROM full_report WHERE id = ?";
        $stmtSelect = $connDB->prepare($querySelect);
        $stmtSelect->bind_param("i", $reportId);  // "i" indicates an integer type for id
        $stmtSelect->execute();
        $result = $stmtSelect->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $upload_at = $row['upload_at'];
            $sppd = $row['sppd'];

            // Step 2: Delete from `report` table
            $queryDeleteReport = "DELETE FROM report WHERE id = ?";
            $stmtDeleteReport = $conn->prepare($queryDeleteReport);
            $stmtDeleteReport->bind_param("i", $reportId);

            // Step 3: Delete from `itinerary` based on `upload_at` or `sppd`
            $queryDeleteItinerary = "DELETE FROM full_report WHERE upload_at = ? OR sppd = ?";
            $stmtDeleteItinerary = $connDB->prepare($queryDeleteItinerary);
            $stmtDeleteItinerary->bind_param("ss", $upload_at, $sppd);  // "ss" for two string parameters

            // Execute the deletions
            if ($stmtDeleteReport->execute()) {
                if ($stmtDeleteItinerary->execute()) {
                    // Log the deletions
                    createLog($_SESSION['ID'], "DELETE_REPORT", $conn);
                    createLog($_SESSION['ID'], "DELETE_REPORT", $connDB);
                    
                    // Close the prepared statements
                    $stmtDeleteReport->close();
                    $stmtDeleteItinerary->close();
                    $stmtSelect->close();

                    return true;
                } else {
                    return false;  // Itinerary deletion failed
                }
            } else {
                return false;  // Report deletion failed
            }
        } else {
            return false;  // No record found with the given report ID
        }
}

    // Check permissions here
    //TODO: Add permission check for delete, and checks for the request

    if(isset($_GET['item_id'])){
        $queryString = "SELECT * FROM report WHERE id = ".$_GET['item_id'];
        $report = $conn->query($queryString)->fetch_assoc();

        if($_SESSION['ROLE'] == "admin" || ($report['report_by'] == $_SESSION['ID'] && $report['status'] == 0)){
            deleteReport($conn, $connDB);
            setAlert("Sukses menghapus laporan", "success");
        }
        else {
            setAlert("Anda tidak memiliki akses untuk melakukan aksi ini", "danger");
        }
        
        if (($_SESSION['ROLE']) == "sales"){
            header("location: user_dashboard.php");
        }
        else if($_SESSION['ROLE'] === "supervisor" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager" || $_SESSION['ROLE'] === "director"){
            header("location: manager_dashboard.php");
        }
        else if ($_SESSION['ROLE'] == "admin"){
            header("location: admin_dashboard.php");
        }
        else {
            header("location: logout.php");
        }
    }
?>