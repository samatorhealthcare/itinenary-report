<?php
 session_start();
// Database connection
include_once 'db_conn_itinerary.php'; // Adjust to your actual database connection file

    include_once "utilities/util.php";
    include_once "utilities/session_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/log_handler.php";
    include_once "utilities/image_util.php";
    include_once "utilities/user_director_handler.php";

// Retrieve form data
$projects = $_POST['input-project'] ?? [];
$tanggal_aktivitas = $_POST['input-tanggal-aktivitas'] ?? [];
$instansi = $_POST['input-instansi'] ?? [];
$kota = $_POST['input-kota'] ?? [];
$kode_proyek = $_POST['input-kode-proyek'] ?? [];
$nama_proyek = $_POST['input-nama-proyek'] ?? [];
$target = $_POST['input-target'] ?? [];
$progress = $_POST['input-progress'] ?? [];
$kegiatan = $_POST['input-kegiatan'] ?? [];

// Assuming the form data for each project is correlated, you should iterate over them.
for ($i = 0; $i < count($projects); $i++) {
    $project = $connDB->real_escape_string($projects[$i] ?? '');
    $tanggal_input = $tanggal_aktivitas[$i] ?? '';

    // Convert the date to MySQL datetime format
    $tanggal = DateTime::createFromFormat('Y-m-d H:i:s', $tanggal_input); // Change format according to your input
    if ($tanggal !== false) {
        $tanggal_mysql = $tanggal->format('Y-m-d H:i:s');
    } else {
        $tanggal_mysql = null; // Handle invalid date input
    }

      // Debugging: Check if the date conversion worked
    if ($tanggal_mysql === null) {
        echo "Invalid date format for entry $i: '$tanggal_input'<br>";
    } else {
        echo "Date for entry $i: '$tanggal_mysql'<br>";
    }
    
    $lokasi = $connDB->real_escape_string($instansi[$i] ?? '');
    $kota_value = $connDB->real_escape_string($kota[$i] ?? '');
    $kode = $connDB->real_escape_string($kode_proyek[$i] ?? '');
    $nama_proyek_value = $connDB->real_escape_string($nama_proyek[$i] ?? '');
    $target_value = $connDB->real_escape_string($target[$i] ?? '');
    $progress_value = $connDB->real_escape_string($progress[$i] ?? '');
    $kegiatan_value = $connDB->real_escape_string($kegiatan[$i] ?? '');

    // Insert data into the database
    $sql = "INSERT INTO full_report (
        project, tanggal_aktivitas, instansi, kota, kode_proyek, nama_proyek, target, progress, kegiatan, report_by
    ) VALUES (
        '$project', '$tanggal_mysql', '$lokasi', '$kota_value', '$kode', '$nama_proyek_value', '$target_value', '$progress_value', '$kegiatan_value', " . strval($_SESSION['ID']) . "
    )";

    if (!$connDB->query($sql)) {
        echo "Error: " . $connDB->error . "<br>";
    }
}

echo "Records inserted successfully.";

// Close the connection
$connDB->close();
?>
