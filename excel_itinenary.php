<?php
session_start();
include_once "db_conn_itinerary.php";
include_once "db_conn.php";
include_once "utilities/util.php";

// if (isset($_GET['item_id'])){
//         $queryString = "SELECT * FROM report WHERE ID=".$_GET['item_id']."";
        
//         $result = $conn->query($queryString);

//         $item = mysqli_fetch_assoc($result);

//         $queryString = "SELECT r.action_at as action_at, r.comment as comment, u.username as username, u.role as role, u.emp_id as id FROM report_action r, user u where r.report_id=".$_GET['item_id']." and r.user = u.id ORDER BY action_at DESC";

//         $result = $conn->query($queryString);

//         $history = [];

//         while($row = mysqli_fetch_assoc($result)){
//             array_push($history, $row);
//         }
//     }    
//     else {
//         echo "No item ID given";
//     }
//    $queryString = "SELECT u.username AS username, u.emp_id AS id 
//                 FROM full_report r 
//                 JOIN user u ON r.report_by = u.id 
//                 WHERE r.report_by = ".$_SESSION['ID']."";
//     $result = $connDB->query($queryString);

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

if(isset($_POST['export_excel_itinerary']))
{
    $file_ext_name = "xls";
    //$idreport = $_GET['item_id'];
    $username = $_SESSION['USERNAME'];

    $fileName = "laporan-" . $username;

    //$student = "SELECT * FROM report WHERE ID=".$_GET['item_id']."";
    $queryString = "SELECT * 
                    FROM full_report
                    WHERE report_by =  " . $_SESSION['ID'] . "
                    ORDER BY sppd";
    $query_run = mysqli_query($connDB, $queryString);
    

    if(mysqli_num_rows($query_run) > 0)
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet ->setCellValue('A1', 'ID Laporan');
        $sheet->mergeCells('A1:A2');
        $sheet ->setCellValue('B1', 'Tanggal Aktivitas');
        $sheet->mergeCells('B1:B2');
        $sheet ->setCellValue('C1', 'No SPPD');
        $sheet->mergeCells('C1:C2');
        $sheet ->setCellValue('D1', 'Proyek');
        $sheet->mergeCells('D1:D2');
        $sheet->setCellValue('E1', 'Instansi');
        $sheet->mergeCells('E1:E2');
        $sheet->setCellValue('F1', 'Kode Proyek');
        $sheet->mergeCells('F1:F2');
        $sheet->setCellValue('G1', 'Kota Kunjungan');
        $sheet->mergeCells('G1:G2');
        $sheet->setCellValue('H1', 'Estimasi');
        $sheet->mergeCells('H1:I1');
        $sheet->setCellValue('H2', 'Progres Saat Ini');
        $sheet->setCellValue('I2', 'Target');
        

        $row = 3;
        while($row_data = $query_run->fetch_assoc()) {

        // Exporting data to different columns
        $sheet->setCellValue('A' . $row, $row_data['id']);
        $sheet->setCellValue('B' . $row, (new DateTime($row_data['tanggal_aktivitas']))->format("d F Y"));
        $sheet->setCellValue('C' . $row, $row_data['sppd']);
        $sheet->setCellValue('D' . $row, $row_data['project']);
        $sheet->setCellValue('E' . $row, $row_data['instansi']);
        $sheet->setCellValue('F' . $row, $row_data['kode_proyek']);
        $sheet->setCellValue('G' . $row, $row_data['kota']);
        $sheet->setCellValue('H' . $row, $row_data['progress'] . "%");
        $sheet->setCellValue('I' . $row, $row_data['target'] . "%");
        $row++;
    }
        
        
        //create files xls
        $writer = new Xls($spreadsheet);
        $final_filename = $fileName.'.xls';
       
        // $writer->save($final_filename);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.urlencode($final_filename).'"');
        $writer->save('php://output');

      

    }
    else
    {
        $_SESSION['message'] = "No Record Found";
        header('Location: detail_report_itinerary.php');
        exit(0);
    }
}

?>