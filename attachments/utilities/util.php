<?php
    function parseTimestampsToSQL(){
        $timestamp = new DateTime(date("Y-m-d H:i:s"), new DateTimeZone("Asia/Jakarta"));

        // $newTimezone = new DateTimeZone("Asia/Jakarta");
        // $timestamp->setTimezone($newTimezone);
        return "STR_TO_DATE('".$timestamp->format('d-m-Y H:i:s')."', '%d-%m-%Y %T')";
    }

    function parseReportStatus($statusNumber){
        $returnText = "";
        if ($statusNumber == -1){
            $returnText = "Rejected";
        }
        else if ($statusNumber == 0){
            $returnText = "Waiting for Approval";
        }
        else if ($statusNumber == 1){
            $returnText = "Manager Approved";
        }
        else if ($statusNumber == 2){
            $returnText = "General Manager Approved";
        }
        else if ($statusNumber == 3){
            $returnText = "Director Approved";
        }
         else if ($statusNumber == 4){
            $returnText = "Supervisor Approved";
        }
        return $returnText;
    }

    function parseRoles($role){
        $returnText = "";
        if ($role == "sales"){
            $returnText = "Sales";
        }
        else if($role == "manager"){
            $returnText = "Manager";
        }
        else if($role == "admin"){
            $returnText = "Admin";
        }
        else if($role == "gmanager"){
            $returnText = "General Manager";
        }
        else if($role == "director"){
            $returnText = "Direktur";
        }
        else if($role == "supervisor"){
            $returnText = "Supervisor";
        }
        return $returnText;
    }

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    function fetchCurrentTimestamp(){
        return time();
    }

    function generateFileName($filetype, $suffix){
        // Create file name generation using current timestamp

        $user = $_SESSION['USERNAME'];
        $timestamp = fetchCurrentTimestamp();

        $filename = $user.'~'.$timestamp.'~'.$suffix.'.'.$filetype;

        return $filename;
    }

    function fetchNumericRole($role){
        if ($role == "sales"){
            return 0;
        }
        else if($role == "manager"){
            return 1;
        }
        else if($role == "gmanager"){
            return 2;
        }
        else if($role == "director"){
            return 3;
        }
        else if($role == "admin"){
            return 4;
        }
        else if($role == "supervisor"){
            return 5;
        }
    }
?>