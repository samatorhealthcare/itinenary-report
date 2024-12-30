<?php
session_start();
include_once "db_conn.php";
include_once "utilities/util.php";

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

if(isset($_POST['export_excel_admin']))
{
    $file_ext_name = "xls";
    $username = $_SESSION['USERNAME'];
    $fileName = "laporan-" . $username;

    $student = "SELECT r.id as 'id', r.project as 'project', r.location as 'location', r.upload_at as 'upload_at', u.emp_id as 'emp_id', r.status as 'status', r.visit_number as 'visit_number', r.chance as 'chance', 
                    r.deadline as 'deadline', r.city as 'city', r.prospect as 'prospect', r.competitor as 'competitor', r.note as 'note', r.sales_note as 'sales_note',
                    u.username as 'username' FROM report r, user u WHERE r.report_by = u.id";
    $query_run = mysqli_query($conn, $student);

    $queryString = "SELECT r.id as 'id', r.project as 'project', r.location as 'location', r.upload_at as 'upload_at', u.emp_id as 'emp_id', r.status as 'status', u.username as 'username' FROM report r, user u WHERE r.report_by = u.id";
    

    if(mysqli_num_rows($query_run) > 0)
    {
        $spreadsheet = new Spreadsheet();
        
        $sheet = $spreadsheet->getActiveSheet();
        
        $sheet ->setCellValue('A1', 'ID Laporan');
        $sheet->mergeCells('A1:A2');
        $sheet ->setCellValue('B1', 'Tanggal Pembuatan');
        $sheet->mergeCells('B1:B2');
        $sheet ->setCellValue('C1', 'Instansi');
        $sheet->mergeCells('C1:C2');
        $sheet->setCellValue('D1', 'Kunjungan ke');
        $sheet->mergeCells('D1:D2');
        $sheet->setCellValue('E1', 'Peluang');
        $sheet->mergeCells('E1:E2');
        $sheet->setCellValue('F1', 'Dibutuhkan Kapan');
        $sheet->mergeCells('F1:F2');
        $sheet->setCellValue('G1', 'Kota Kunjungan');
        $sheet->mergeCells('G1:G2');
        $sheet->setCellValue('H1', 'Nilai Prospek');
        $sheet->mergeCells('H1:H2');
        $sheet->setCellValue('I1', 'Pesaing');
        $sheet->mergeCells('I1:I2');
        $sheet->setCellValue('J1', 'Keterangan');
        $sheet->mergeCells('J1:J2');
        $sheet->setCellValue('K1', 'Komentar dari Sales');
        $sheet->mergeCells('K1:K2');
        $sheet->setCellValue('L1', 'Jenis Proyek');
        $sheet->mergeCells('L1:L2');
        $sheet->setCellValue('M1', 'Pelapor');
        $sheet->mergeCells('M1:M2');
        

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
        $sheet->setCellValue('M' . $row, $row_data['username']);
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
        header('Location: admin_dashboard.php');
        exit(0);
    }
}

?>