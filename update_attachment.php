<?php
     session_start();
    include_once "db_conn.php";
    include_once "utilities/util.php";
    include_once "utilities/session_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/log_handler.php";
    include_once "utilities/image_util.php";
    include_once "utilities/util.php";
    include "logged_user_check.php";

    
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

    function updateLampiranAction($attachment, $reportId, $conn){
        
        $queryString = "UPDATE report SET attachment = '".$attachment."' WHERE id='".$reportId."'";
        $conn->query($queryString);

    }

     $attachment = '';
     if(isset($_FILES['edit-attachment']) && $_FILES['edit-attachment']['name'][0] !== ""){
                    $files = parseFilesObject($_FILES['edit-attachment']);
    
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
                            if ($i != count($_FILES['edit-attachment'])){
                                $attachment = $attachment . ';';
                            }
                        }
                    }
    }
      
    
    if ($_POST['item_id'] || $_POST['item_id'] != null){
        
        updateLampiranAction($attachment, $_POST['item_id'], $conn);
        createLog($_SESSION['ID'], "ATTACHMENT_EDITED", $conn);
        setAlert("Berhasil mengedit lampiran", "success");
        header("location: admin_dashboard.php");

    }
    else{
         header("location: logout.php");
        setAlert("Gagal mengedit lampiran", "danger");
    }
   

?>