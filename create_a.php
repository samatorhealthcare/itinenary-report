<?php 
    session_start();
    include_once "db_conn_itinerary.php";
    include_once "utilities/util.php";
    include_once "utilities/session_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/log_handler.php";
    include_once "utilities/image_util.php";
    include_once "utilities/user_director_handler.php";

    // Check whether the user is currently logged in or not
    // If so then add the report
    // If not the return back to the login page
     $queryString = "SELECT * FROM user WHERE id ='".$_SESSION['ID']."'";
        $report = $connDB->query($queryString);
        $user_login = mysqli_fetch_assoc($report);

        $report_spv = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_1']."'";
        $report_s = $connDB->query($report_spv);
        $reports_to_spv = mysqli_fetch_assoc($report_s);

        $report_manager = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_2']."'";
        $result_m = $connDB->query($report_manager);
        $reports_to_manager = mysqli_fetch_assoc($result_m);

        $report_gmanager = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_3']."'";
        $result_gm = $connDB->query($report_gmanager);
        $reports_to_gmanager = mysqli_fetch_assoc($result_gm);

        $report_director = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_4']."'";
        $result_dr = $connDB->query($report_director);
        $reports_to_director = mysqli_fetch_assoc($result_dr);

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
        if (isset($_POST['input-location'])){
            // $note = '';
            $attachment = '';
            $activity = isset($_POST['input-type-activity'])? sanitizeInput($_POST['input-type-activity']): '';
            $sppd = isset($_POST['input-sppd'])? sanitizeInput($_POST['input-sppd']): '';
            $duration = isset($_POST['input-duration'])? sanitizeInput($_POST['input-duration']): '';
            $spi = isset($_POST['input-spi'])? sanitizeInput($_POST['input-spi']): '';
           
            $input_role_spv = isset($_POST['input-reports-to-lead-1'])? sanitizeInput($_POST['input-reports-to-lead-1']): '';
            $input_role_manager = isset($_POST['input-reports-to-lead-2'])? sanitizeInput($_POST['input-reports-to-lead-2']): '';
            $input_role_gmanager = isset($_POST['input-reports-to-lead-3'])? sanitizeInput($_POST['input-reports-to-lead-3']): '';
            $input_role_director = isset($_POST['input-reports-to-lead-4'])? sanitizeInput($_POST['input-reports-to-lead-4']): '';
            $input_role_director_2 = isset($_POST['input-reports-to-lead-5'])? sanitizeInput($_POST['input-reports-to-lead-5']): '';

            $insertOk = 1;
            $reason = [];

            $deadline = getdate(strtotime($deadline));
            $deadline = $deadline["mday"]."/".$deadline["mon"]."/".$deadline['year'];

            $tanggal_spi = getdate(strtotime($tanggal_spi));
            $tanggal_spi = $tanggal_spi["mday"]."/".$tanggal_spi["mon"]."/".$tanggal_spi['year'];

            $tanggal_kontrak = getdate(strtotime($tanggal_kontrak));
            $tanggal_kontrak = $tanggal_kontrak["mday"]."/".$tanggal_kontrak["mon"]."/".$tanggal_kontrak['year'];

            // $note = htmlspecialchars($_POST['input-note']);

            // $queryString = "SELECT * FROM user WHERE id = ".$_SESSION['ID'];
            // $user = $connDB->query($queryString)->fetch_assoc();
            // if ($user['reports_to'] == 0){
            //     //$insertOk = 0;
            //     array_push($reason, "User tidak memiliki atasan !");
            // }
            // else {
            //     if(isset($_FILES['input-attachment']) && $_FILES['input-attachment']['name'][0] !== ""){
            //         $files = parseFilesObject($_FILES['input-attachment']);
    
            //         $i = 0;
            //         foreach ($files as $file){
            //             $uploadObj = uploadFile($file, $i);
    
            //             if ($uploadObj['status'] == 0){
            //                 $insertOk = $uploadObj['status'];
            //                 array_push($reason, $uploadObj['reason']);
            //             }
            //             else {
            //                 $attachment = $attachment . $uploadObj['pathname'];
            //                 $i = $i + 1;
            //                 if ($i != count($_FILES['input-attachment'])){
            //                     $attachment = $attachment . ';';
            //                 }
            //             }
            //         }
            //     }
            // }
            // if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            //     $request_body = file_get_contents('php://input');
            //     $data = json_decode($request_body, true);
            //     foreach($data['reportData'] as $report){
                   
            //         $project = $report['input-project'];
            //         $tanggal = $report['input-tanggal-aktivitas'];
            //         $tanggal = getdate(strtotime($tanggal));
            //         $tanggal = $tanggal["mday"]."/".$tanggal["mon"]."/".$tanggal['year'];
            //         $lokasi = $report['instansi'];
            //         $kota = $report['input-kota'];
            //         $kode = $report['input-kode-proyek'];
            //         $nama_proyek = $report['input-nama-proyek'];
            //         $target = $report['input-target'];
            //         $progress = $report['input-progress'];
            //         $kegiatan = $report['input-kegiatan'];

            //         $sql = "INSERT into full_report (project, tanggal_aktivitas, instansi, kota, kode_proyek, nama_proyek, target, progress, kegiatan) values('$project','$tanggal', '$lokasi', '$kota', '$kode', '$nama_proyek', '$target', '$progress', '$kegiatan')";
            //         mysqli_query($connDB,$sql);
            //     }
            // }
            
            //jika hanya mengisi spv
            if($input_role_spv != NULL){
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."'".$input_role_spv."', '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //jika hanya mengisi manager
            else if($input_role_manager != NULL){
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_2, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."'".$input_role_manager."', '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //jika hanya mengisi gmanager
            else if($input_role_gmanager != NULL){
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_3, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."'".$input_role_gmanager."', '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //jika hanya mengisi direktur 1
            else if($input_role_director != NULL){
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."'".$input_role_director."', '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //jika hanya mengisi direktur 2
            else if($input_role_director_2 != NULL){
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."'".$input_role_director_2."', '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //jika mengisi direktur 1 dan 2
            else if($input_role_director != NULL && $input_role_director_2 != NULL){
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_4, need_approval_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."'".$input_role_director."', '".$input_role_director_2."', '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //case sales barat gak punya spv, tapi ngisi manager, gmanager, direktur 1
            else if($input_role_spv == NULL && $input_role_manager != NULL && $input_role_gmanager != NULL && $input_role_director != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_2, need_approval_by_3, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_manager.", ".$input_role_gmanager.", ".$input_role_director.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //case sales barat gak punya spv, tp ngisi manager, gmanager, direktur 1, direktur 2
            else if($input_role_spv == NULL && $input_role_manager != NULL && $input_role_gmanager != NULL && $input_role_director != NULL && $input_role_director_2 != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_2, need_approval_by_3, need_approval_by_4, need_approval_by_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_manager.", ".$input_role_gmanager.", ".$input_role_director.", ".$input_role_director_2.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //case sales timur ngisi spv, manager, gmanager, direktur 1
            else if($input_role_spv != NULL && $input_role_manager != NULL && $input_role_gmanager != NULL && $input_role_director != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_2, need_approval_by_3, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_manager.", ".$input_role_gmanager.", ".$input_role_director.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //case sales timur ngisi spv, manager, gmanager, direktur 1, direktur 2
            else if($input_role_spv != NULL && $input_role_manager != NULL && $input_role_gmanager != NULL && $input_role_director != NULL && $input_role_director_2 != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_2, need_approval_by_3, need_approval_by_4, need_approval_by_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_manager.", ".$input_role_gmanager.", ".$input_role_director.", ".$input_role_director_2.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
              //case sales timur ngisi spv, gmanager, direktur 1
            else if($input_role_spv != NULL && $input_role_manager == NULL && $input_role_gmanager != NULL && $input_role_director != NULL && $input_role_director_2 == NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_3, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_gmanager.", ".$input_role_director.", ".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
             //case sales timur ngisi spv, gmanager, direktur 1, direktur 2
            else if($input_role_spv != NULL && $input_role_manager == NULL && $input_role_gmanager != NULL && $input_role_director != NULL && $input_role_director_2 != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_3, need_approval_by_4, need_approval_by_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_gmanager.", ".$input_role_director.", ".$input_role_director_2.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
               //case sales timur ngisi spv, direktur 1
            else if($input_role_spv != NULL && $input_role_manager == NULL && $input_role_gmanager == NULL && $input_role_director != NULL && $input_role_director_2 == NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_director.", ".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
                //case sales timur ngisi spv, direktur 1
            else if($input_role_spv != NULL && $input_role_manager == NULL && $input_role_gmanager == NULL && $input_role_director != NULL && $input_role_director_2 != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_4, need_approval_by_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_director.", ".$input_role_director_2.", ".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //case user role manager ngisi gmanager dan direktur 1
            else if($input_role_manager == NULL && $input_role_spv == NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_3, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_manager.", ".$input_role_gmanager.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //case isi manager dan direktur 1 aja
            else if($input_role_manager != NULL && $input_role_director != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_2, need_approval_by_4, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_manager.", ".$input_role_director.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
             //case isi manager dan direktur 1 dan 2 aja
            else if($input_role_manager != NULL && $input_role_director != NULL && $input_role_director_2 != NULL){
                
                $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by_2, need_approval_by_4, need_approval_by_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_manager.", ".$input_role_director.", ".$input_role_director_2.",'".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
            }
            //jika mengisi semua atasan
            else{
               $insertQueryString = "INSERT INTO full_report (project, sppd, durasi, spi, tanggal_spi, tanggal_kontrak, need_approval_by, need_approval_by_2, need_approval_by_3, need_approval_by_4, need_approval_5, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL()."".$input_role_spv.", ".$input_role_manager.", ".$input_role_gmanager.", ".$input_role_director.", ".$input_role_director_2.", '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";
                
            }
            
                
            if($insertOk === 1){
                if ($connDB->query($insertQueryString) === TRUE){
                    
                    setAlert("Sukses membuat laporan baru", "success");
                    createLog($_SESSION['ID'], "CREATE_REPORT", $connDB);
                    if($_SESSION['ROLE'] === "sales"){
                        header("location: user_dashboard.php");
                    }
                    else if($_SESSION['ROLE'] === "supervisor" || $_SESSION['ROLE'] === "manager" || $_SESSION['ROLE'] === "gmanager"){
                        header("location: manager_dashboard.php");
                    }
                }
                else if($connDB->query($insertQueryString) === FALSE){
                    setAlert("Gagal membuat laporan baru", "danger");
                    header("location: error.php");
                }
                else {
                    
                    setAlert("Data belum diisi secara lengkap!", "danger");
                    header("location: logout.php");
                }
            }
            else {
                
                if(count($reason) > 0){
                    setAlert($reason[0], "danger");
                }
                header("location: user_dashboard.php");
            }
        }
        else {
            header("location: error.php");
        }
    }
    elseif (checkSession()){
        // User is not allowed to create a report
        setAlert("You are not allowed to create a report !", "danger");
        header("location: logout.php");
    }
    elseif(checkSessionRole(["sales", "manager", "gmanager", "supervisor"])){
        setAlert("Your credentials have expired, please re-enter your credentials !", "danger");
        header("location: logout.php");
    }
    else {
        header("location: error.php");
    }
?>