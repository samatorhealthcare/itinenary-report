<?php
// Database connection
include_once "db_conn_itinerary.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $projects = isset($_POST['input-project']) ? $_POST['input-project'] : [];
    //$tanggal_aktivitas = $_POST['input-tanggal-aktivitas'] ?? [];
    $instansi = $_POST['input-instansi'] ?? [];
    $kota = $_POST['input-kota'] ?? [];
    $kode_proyek = $_POST['input-kode-proyek'] ?? [];
    $nama_proyek = $_POST['input-nama-proyek'] ?? [];
    $target = $_POST['input-target'] ?? [];
    $progress = $_POST['input-progress'] ?? [];
    $kegiatan = $_POST['input-kegiatan'] ?? [];

    // Check if all arrays have the same length
    $count = count($projects);
    if ($count === count($instansi) && $count === count($kota) && $count === count($kode_proyek) && $count === count($nama_proyek) && $count === count($target) && $count === count($progress) && $count === count($kegiatan)) {
        for ($i = 0; $i < $count; $i++) {
            // $project = $connDB->real_escape_string($projects[$i]);
            //$tanggal = $connDB->real_escape_string($tanggal_aktivitas[$i]);
            $lokasi = $connDB->real_escape_string($instansi[$i]);
            $kota = $connDB->real_escape_string($kota[$i]);
            $kode = $connDB->real_escape_string($kode_proyek[$i]);
            $nama_proyek = $connDB->real_escape_string($nama_proyek[$i]);
            $target = $connDB->real_escape_string($target[$i]);
            $progress = $connDB->real_escape_string($progress[$i]);
            $kegiatan = $connDB->real_escape_string($kegiatan[$i]);

            // Insert data into database
            $sql = "INSERT INTO full_report (project, instansi, kota, kode_proyek, nama_proyek, target, progress, kegiatan) 
                    VALUES ('$projects', $lokasi', '$kota', '$kode', '$nama_proyek', '$target', '$progress', '$kegiatan')";

            if (!$connDB->query($sql)) {
                echo "Error: " . $connDB->error;
            }
        }
        echo "Records inserted successfully.";
    } else {
        echo "instansi count: " . count($instansi) . "<br>";
        echo "kota count: " . count($kota) . "<br>";
        echo "kode_proyek count: " . count($kode_proyek) . "<br>";
        echo "nama_proyek count: " . count($nama_proyek) . "<br>";
        echo "target count: " . count($target) . "<br>";
        echo "progress count: " . count($progress) . "<br>";
        echo "kegiatan count: " . count($kegiatan) . "<br>";
        echo "Error: Form data mismatch.";
    }
}

// Close connection
$connDB->close();
?>
