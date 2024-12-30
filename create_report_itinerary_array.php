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

    function parseFilesObject($fileObject){
        $fileArr = [];

        $length = count($fileObject['name']);

        for($i = 0; $i < $length; $i++){
            $tempFileObject = array();

            $tempFileObject['name'] = $fileObject['name'][$i];
            $tempFileObject['full_path'] = $fileObject['full_path'][$i];
            $tempFileObject['type'] = $fileObject['type'][$i];
            $tempFileObject['tmp_name'] = $fileObject['tmp_name'][$i];
            $tempFileObject['error'] = $fileObject['error'][$i];
            $tempFileObject['size'] = $fileObject['size'][$i];

            array_push($fileArr, $tempFileObject);
        }

        return $fileArr;
    }

    function uploadFile($file, $suffix = "") {
        $target_dir = "attachments/";
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $target_file = $target_dir.basename(generateFileName($imageFileType, $suffix));  
        $reason = "";

        if(isset($_POST['submit'])){
            $check = getimagesize($file["tmp_name"]);
            if ($check !== false){
                echo "Files is an image - " . $check['mime'] . ".";
                $uploadOk = 1;
            }
        }

        if ($file['size'] > 6000000) {
            $reason = "Sorry your file is too big";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "jfif"){
            $reason = "Sorry file type [".$imageFileType."] not allowed";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $reason = "Sorry, your file was not uploaded.";
            // header("location: error.php?error=Sorry, your file was not uploaded.");
        // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                parseToPortrait($target_file);
                echo "The file ". htmlspecialchars( basename( $file["name"])). " has been uploaded.";
            } else {
                header("location: error.php?error=Sorry, there was an error uploading your file");
            }
        }

        $returnObj["status"] = $uploadOk;
        $returnObj["pathname"] = $target_file;
        $returnObj["reason"] = $reason;

        return $returnObj;
    }


if(checkSession() && checkSessionRole(["sales", "supervisor", "manager", "gmanager"])){
    $part1 = trim($_POST['input-sppd-part1']);
    $part2 = trim($_POST['input-sppd-part2']);
    $part3 = trim($_POST['input-sppd-part3']);
    // Retrieve input single
    $attachment = '';
    $sppd = "SPPD-" . $part1 . "-" . $part2 . "-" . $part3;
    $duration = isset($_POST['input-duration'])? sanitizeInput($_POST['input-duration']): '';
    $type = isset($_POST['input-type-activity'])? sanitizeInput($_POST['input-type-activity']): '';
    $tanggal_spi = isset($_POST['input-tanggal-spi'])? sanitizeInput($_POST['input-tanggal-spi']): '';
    $tanggal_kontrak = isset($_POST['input-tanggal-kontrak'])? sanitizeInput($_POST['input-tanggal-kontrak']): '';
    $jenis_laporan = isset($_POST['input-jenis-laporan']) ? sanitizeInput($_POST['input-jenis-laporan']) : null;
    $input_role_spv = isset($_POST['input-reports-to-lead-1']) ? sanitizeInput($_POST['input-reports-to-lead-1']) : null;
    $input_role_manager = isset($_POST['input-reports-to-lead-2']) ? sanitizeInput($_POST['input-reports-to-lead-2']) : null;
    $input_role_gmanager = isset($_POST['input-reports-to-lead-3']) ? sanitizeInput($_POST['input-reports-to-lead-3']) : null;
    $input_role_director = isset($_POST['input-reports-to-lead-4']) ? sanitizeInput($_POST['input-reports-to-lead-4']) : null;
    $input_role_director_2 = isset($_POST['input-reports-to-lead-5']) ? sanitizeInput($_POST['input-reports-to-lead-5']) : null;

    if(isset($_FILES['input-attachment']) && $_FILES['input-attachment']['name'][0] !== ""){
                    $files = parseFilesObject($_FILES['input-attachment']);
    
                    $i = 0;
                    foreach ($files as $file){
                        $uploadObj = uploadFile($file, $i);
    
                        if ($uploadObj['status'] == 0){
                            $insertOk = $uploadObj['status'];
                            array_push($reason, $uploadObj['reason']);
                        }
                        else {
                            $attachment = $attachment . $uploadObj['pathname'];
                            $i = $i + 1;
                            if ($i != count($_FILES['input-attachment'])){
                                $attachment = $attachment . ';';
                            }
                        }
                    }
                }

    // Retrieve form atasan
     $roles = [
            'spv' => $input_role_spv,
            'manager' => $input_role_manager,
            'gmanager' => $input_role_gmanager,
            'director' => $input_role_director,
            'director_2' => $input_role_director_2
        ];

        $approval_columns = [
            'spv' => 'need_approval_by',
            'manager' => 'need_approval_by_2',
            'gmanager' => 'need_approval_by_3',
            'director' => 'need_approval_by_4',
            'director_2' => 'need_approval_by_5'
        ];

        $columns = [];
        $values = [];

        foreach ($roles as $role => $value) {
            if ($value !== NULL) {
                $columns[] = $approval_columns[$role];
                $values[] = $value;
            }
        }

        if (empty($columns)) {
            $columns[] = 'need_approval_by';
            $values[] = $input_role_spv !== NULL ? $input_role_spv : "NULL";
        }

        $columns_str = implode(', ', $columns);
        $values_str = implode(', ', $values);


        // Retrieve form data array
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

            $tanggal_aktivitas_sql = getdate(strtotime($tanggal_input));
            $tanggal_aktivitas_sql = $tanggal_aktivitas_sql["mday"]."/".$tanggal_aktivitas_sql["mon"]."/".$tanggal_aktivitas_sql['year'];

            $tanggal_spi = getdate(strtotime($tanggal_spi));
            $tanggal_spi = $tanggal_spi["mday"]."/".$tanggal_spi["mon"]."/".$tanggal_spi['year'];

            $tanggal_kontrak = getdate(strtotime($tanggal_kontrak));
            $tanggal_kontrak = $tanggal_kontrak["mday"]."/".$tanggal_kontrak["mon"]."/".$tanggal_kontrak['year'];
            
            
            $lokasi = $connDB->real_escape_string($instansi[$i] ?? '');
            $kota_value = $connDB->real_escape_string($kota[$i] ?? '');
            $kode = $connDB->real_escape_string($kode_proyek[$i] ?? '');
            $nama_proyek_value = $connDB->real_escape_string($nama_proyek[$i] ?? '');
            $target_value = $connDB->real_escape_string($target[$i] ?? '');
            $progress_value = $connDB->real_escape_string($progress[$i] ?? '');
            $kegiatan_value = $connDB->real_escape_string($kegiatan[$i] ?? ''); 
            $sppd = $connDB->real_escape_string($sppd);
            $duration = $connDB->real_escape_string($duration);
            $type = $connDB->real_escape_string($type);
            $jenis_laporan = $connDB->real_escape_string($jenis_laporan);
            $columns_str = $connDB->real_escape_string($columns_str);
            $values_str = $connDB->real_escape_string($values_str);

            // Insert data array into the database
            $sql = "INSERT INTO full_report (
                project, tanggal_aktivitas, tanggal_spi, tanggal_kontrak, instansi, kota, kode_proyek, nama_proyek, target, progress, kegiatan, report_by, sppd, durasi, tipe_kegiatan, $columns_str, attachment, jenis_laporan
            ) VALUES (
                '$project', " . ($tanggal_aktivitas_sql !== NULL ? "STR_TO_DATE('" . $tanggal_aktivitas_sql . "', '%d/%m/%Y')" : "NULL") . ",  " . ($tanggal_spi !== NULL ? "STR_TO_DATE('" . $tanggal_spi . "', '%d/%m/%Y')" : "NULL") . ",  " . ($tanggal_kontrak !== NULL ? "STR_TO_DATE('" . $tanggal_kontrak . "', '%d/%m/%Y')" : "NULL") . ",'$lokasi', '$kota_value', '$kode', '$nama_proyek_value', '$target_value', '$progress_value', '$kegiatan_value', " . strval($_SESSION['ID']) . ", '$sppd', '$duration', '$type', $values_str, '" . $attachment . "', '$jenis_laporan'
            )";

            if (!$connDB->query($sql)) {
                echo "Error array data: " . $connDB->error . "<br>";
                setAlert("Gagal membuat laporan baru", "danger");
                header("Location: error.php");
            }
        }

        setAlert("Sukses membuat laporan baru", "success");
                    createLog($_SESSION['ID'], "CREATE_REPORT", $connDB);

                    // Redirect based on user role
                    switch ($_SESSION['ROLE']) {
                        case "sales":
                            header("Location: user_dashboard_itinerary.php");
                            break;
                        case "supervisor":
                        case "manager":
                        case "gmanager":
                            header("Location: manager_dashboard_itinerary.php");
                            break;
                        default:
                            header("Location: error.php");
                            break;
                    }
                    exit();
       
        //echo "Records inserted successfully.";


// Close the connection
$connDB->close();

}


?>
