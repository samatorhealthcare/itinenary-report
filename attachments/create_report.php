<?php 
    session_start();
    include_once "db_conn.php";
    include_once "utilities/util.php";
    include_once "utilities/session_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/log_handler.php";

    // Check whether the user is currently logged in or not
    // If so then add the report
    // If not the return back to the login page

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

    if(checkSession() && checkSessionRole(["sales", "manager", "gmanager"])){
        if (isset($_POST['input-location'])){
            $location = $_POST['input-location'];
            $note = '';
            $attachment = '';
            $latitude = isset($_POST['input-latitude'])? sanitizeInput($_POST['input-latitude']): '';
            $longitude = isset($_POST['input-longitude'])? sanitizeInput($_POST['input-longitude']): '';
            $project = isset($_POST['input-project'])? sanitizeInput($_POST['input-project']): '';
            $city = isset($_POST['input-city'])? sanitizeInput($_POST['input-city']): '';
            $visit_number = isset($_POST['input-visit'])? sanitizeInput($_POST['input-visit']): '';
            $prospect = isset($_POST['input-prospect'])? sanitizeInput($_POST['input-prospect']): '';
            $chance = isset($_POST['input-opportunity'])? sanitizeInput($_POST['input-opportunity']): '';
            $competitor = isset($_POST['input-competitor'])? sanitizeInput($_POST['input-competitor']): '';
            $deadline = isset($_POST['input-due'])? sanitizeInput($_POST['input-due']): '';
            $sales_note = isset($_POST['input-sales-note'])? sanitizeInput($_POST['input-sales-note']): '';

            $insertOk = 1;
            $reason = [];

            $deadline = getdate(strtotime($deadline));
            $deadline = $deadline["mday"]."/".$deadline["mon"]."/".$deadline['year'];

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


            $note = htmlspecialchars($_POST['input-note']);

            $queryString = "SELECT * FROM user WHERE id = ".$_SESSION['ID']."";
            $result = $conn->query($queryString);
            $user = mysqli_fetch_assoc($result);

            $insertQueryString = "INSERT INTO report (upload_at, location, attachment, note, report_by, need_approval_by, longitude, latitude, project, city, visit_number, prospect, chance, competitor, deadline, sales_note) VALUES (".parseTimestampsToSQL().",'".$location."','".$attachment."', '".$note."', ".strval($_SESSION['ID']).", ".$user['reports_to'].", '".$longitude."', '".$latitude."', '".$project."', '".$city."', ".$visit_number.", ".$prospect.", ".$chance.", '".$competitor."', STR_TO_DATE('".$deadline."', '%d/%m/%Y'), '".$sales_note."')";

            if($insertOk === 1){
                if ($conn->query($insertQueryString) === TRUE){
                    createLog($_SESSION['ID'], "CREATE_REPORT", $conn);
                    // header("location: user_dashboard.php");
                }
                else {
                    header("location: error.php?error=".$insertQueryString);
                }
            }
            else {
                header("location: user_dashboard.php");
            }
        }
        else {
            header("location: error.php");
        }
    }
    elseif (checkSession()){
        // User is not allowed to create a report
        setAlert("You are not allowed to create a report !", "WARNING");
        header("location: logout.php");
    }
    elseif(checkSessionRole(["sales", "manager", "gmanager"])){
        setAlert("Your credentials have expired, please re-enter your credentials !", "WARNING");
        header("location: logout.php");
    }
    else {
        header("location: error.php");
    }
?>