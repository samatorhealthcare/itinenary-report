<?php
session_start();
include_once "db_conn.php";
include_once "utilities/util.php";

// if (isset($_GET['item_id'])){
//         $queryString = "SELECT * FROM full_report WHERE ID=".$_GET['item_id']."";
        
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

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

if(isset($_POST['export_excel_all']))
{
    $file_ext_name = "xls";
    //$idreport = $_GET['item_id'];
    $username = $_SESSION['USERNAME'];
    $fileName = "laporan-" . $username;

    $student = "SELECT * FROM report WHERE report_by =  " . $_SESSION['ID'] . "";
    $query_run = mysqli_query($conn, $student);
    

    if(mysqli_num_rows($query_run) > 0)
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet ->setCellValue('A1', 'ID Laporan');
        $sheet ->setCellValue('B1', 'Tanggal Pembuatan');
        $sheet ->setCellValue('C1', 'Instansi');
        $sheet->setCellValue('D1', 'Kunjungan ke');
        $sheet->setCellValue('E1', 'Peluang');
        $sheet->setCellValue('F1', 'Dibutuhkan Kapan');
        $sheet->setCellValue('G1', 'Kota Kunjungan');
        $sheet->setCellValue('H1', 'Nilai Prospek');
        $sheet->setCellValue('I1', 'Pesaing');
        $sheet->setCellValue('J1', 'Keterangan');
        $sheet->setCellValue('K1', 'Komentar dari Sales');
        $sheet->setCellValue('L1', 'Jenis Proyek');
         $sheet->setCellValue('M1', 'Status Laporan');
        

        $row = 2;
        while($row_data = $query_run->fetch_assoc()) {

        // Exporting data to different columns
        $sheet->setCellValue('A' . $row, $row_data['id']);
        $sheet->setCellValue('B' . $row, (new DateTime($row_data['upload_at']))->format("d F Y"));
        $sheet->setCellValue('C' . $row, $row_data['location']);
        $sheet->setCellValue('D' . $row, $row_data['visit_number']);
        $sheet->setCellValue('E' . $row, $row_data['chance'] . "%");
        $sheet->setCellValue('F' . $row, (new DateTime($row_data['deadline']))->format("d F Y"));
        $sheet->setCellValue('G' . $row, $row_data['city']);
        $sheet->setCellValue('H' . $row, "Rp" . number_format($row_data['prospect'], 0, ',', '.'));
        $sheet->setCellValue('I' . $row, $row_data['competitor']);
        $sheet->setCellValue('J' . $row, $row_data['note']);
        $sheet->setCellValue('K' . $row, $row_data['sales_note']);
        $sheet->setCellValue('L' . $row, $row_data['project']);
        $sheet->setCellValue('M' . $row, parseReportStatus($row_data['status']));
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
        header('Location: detail_report.php');
        exit(0);
    }
}

?>