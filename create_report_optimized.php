<?php 
    session_start();
    include_once "db_conn.php";
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
            $input_role_spv = isset($_POST['input-reports-to-lead-1']) ? sanitizeInput($_POST['input-reports-to-lead-1']) : null;
            $input_role_manager = isset($_POST['input-reports-to-lead-2']) ? sanitizeInput($_POST['input-reports-to-lead-2']) : null;
            $input_role_gmanager = isset($_POST['input-reports-to-lead-3']) ? sanitizeInput($_POST['input-reports-to-lead-3']) : null;
            $input_role_director = isset($_POST['input-reports-to-lead-4']) ? sanitizeInput($_POST['input-reports-to-lead-4']) : null;
            $input_role_director_2 = isset($_POST['input-reports-to-lead-5']) ? sanitizeInput($_POST['input-reports-to-lead-5']) : null;

            $insertOk = 1;
            $reason = [];

            
            $deadline = getdate(strtotime($deadline));
            $deadline = $deadline["mday"]."/".$deadline["mon"]."/".$deadline['year'];
            $note = htmlspecialchars($_POST['input-note']);

            $queryString = "SELECT * FROM user WHERE id = ".$_SESSION['ID'];
            $user = $conn->query($queryString)->fetch_assoc();
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

        $insertQueryString = "INSERT INTO report (
            upload_at, location, attachment, note, report_by, $columns_str, 
            longitude, latitude, project, city, visit_number, prospect, chance, 
            competitor, deadline, sales_note
        ) VALUES (
            " . parseTimestampsToSQL() . ", 
            '" . $location . "', 
            '" . $attachment . "', 
            '" . $note . "', 
            " . strval($_SESSION['ID']) . ", 
            $values_str, 
            '" . $longitude . "', 
            '" . $latitude . "', 
            '" . $project . "', 
            '" . $city . "', 
            " . ($visit_number !== NULL ? $visit_number : "NULL") . ", 
            " . ($prospect !== NULL ? $prospect : "NULL") . ", 
            " . ($chance !== NULL ? $chance : "NULL") . ", 
            '" . $competitor . "', 
            " . ($deadline !== NULL ? "STR_TO_DATE('" . $deadline . "', '%d/%m/%Y')" : "NULL") . ", 
            '" . $sales_note . "'
        )";

            
                
            // Check if insertion is OK
            if ($insertOk === 1) {
                // Execute the insert query once
                $queryResult = $conn->query($insertQueryString);

                // Check if the query execution was successful
                if ($queryResult === TRUE) {
                    // Successful insertion
                    setAlert("Sukses membuat laporan baru", "success");
                    createLog($_SESSION['ID'], "CREATE_REPORT", $conn);

                    // Redirect based on user role
                    switch ($_SESSION['ROLE']) {
                        case "sales":
                            header("Location: user_dashboard.php");
                            break;
                        case "supervisor":
                        case "manager":
                        case "gmanager":
                            header("Location: manager_dashboard.php");
                            break;
                        default:
                            header("Location: error.php");
                            break;
                    }
                    exit();
                } else {
                    // Query execution failed
                    setAlert("Gagal membuat laporan baru", "danger");
                    header("Location: error.php");
                    exit();
                }
            } else {
                // Handle cases where $insertOk is not 1
                if (count($reason) > 0) {
                    setAlert($reason[0], "danger");
                }
                header("Location: user_dashboard.php");
                exit();
            }

            // Additional checks for session state and user permissions
            if (checkSession()) {
                setAlert("You are not allowed to create a report!", "danger");
                header("Location: logout.php");
                exit();
            } elseif (checkSessionRole(["sales", "manager", "gmanager", "supervisor"])) {
                setAlert("Your credentials have expired, please re-enter your credentials!", "danger");
                header("Location: logout.php");
                exit();
            } else {
                header("Location: error.php");
                exit();
            }
        } 
    }
    
?>