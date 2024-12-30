<?php
    include_once "utilities/util.php";
    /*
        Action Types:
        CREATE_REPORT
        DELETE_REPORT
        CREATE_USER
        DELETE_USER
        MODIFY_USER
        LOGIN
        APPROVE
        REJECT
    */

    function createLog($user_id, $action_type, $conn, $ip = ""){
        $queryString = "INSERT INTO log (action, account, ip, timestamp) values ('".$action_type."', ".$user_id.", '".$ip."', ".parseTimestampsToSQL().")";
        
        try{
            $conn->query($queryString);
        }
        catch(Exception $e){
            header("location: error.php");
        }
    }
?>