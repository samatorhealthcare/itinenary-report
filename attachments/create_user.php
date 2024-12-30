<?php
    session_start();
    include_once "db_conn.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/validation_message_handler.php";
    include_once "utilities/log_handler.php";

    $username = $_POST['input-username'];
    $password = $_POST['input-password'];
    $emp_id = isset($_POST['input-emp-id']) ? $_POST['input-emp-id'] : '';
    $reports_to = isset($_POST['input-reports-to']) ? $_POST['input-reports-to'] : '';
    $role = $_POST['input-role'];

    $username = sanitizeInput($username);
    $password = sanitizeInput($password);
    $emp_id = sanitizeInput($emp_id);
    $reports_to = sanitizeInput($emp_id);
    $role = sanitizeInput($emp_id);

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    //Input Validation
    $passedValidation = true;

    if (isEmptyValidation($username)){
        $passedValidation = false;
        setValidationErrorMessage('input-username', "Username tidak boleh kosong");
    }

    if (isEmptyValidation($password)){
        $passedValidation = false;
        setValidationErrorMessage('input-password', "Password tidak boleh kosong");
    }

    if (isEmptyValidation($emp_id)){
        $passedValidation = false;
        setValidationErrorMessage('input-emp-id', "Employee ID tidak boleh kosong");
    }

    if (isEmptyValidation($reports_to)){
        $passedValidation = false;
        setValidationErrorMessage('input-reports-to', "Atasan tidak boleh kosong");
    }

    if (isEmptyValidation($role)){
        $passedValidation = false;
        setValidationErrorMessage('input-role', "Role tidak boleh kosong");
    }

    if (validateInput($username, "STRING_NO_SYMBOL")){
        $passedValidation = false;
        setValidationErrorMessage('input-username', "Username harus terdiri dari angka dan huruf saja");
    }

    if (validateInput($emp_id, "STRING_NO_SYMBOL")){
        $passedValidation = false;
        setValidationErrorMessage('input-emp-id', "Employee ID harus terdiri dari angka dan huruf saja");
    }

    if($passedValidation){
        $queryString = '';

        $columns = '(username, password, role';
        $values = "('".$username."', '".$hashed_password."', '".$role."'";

        if(isset($_POST['input-emp-id']))  {
            $columns = $columns . ', emp_id';
            $values = $values . ", '".$emp_id."'";
        }
        if(isset($_POST['input-reports-to'])){
            $columns = $columns . ', reports_to';
            $values = $values . ", '".$reports_to."'";
        } 

        $columns = $columns . ')';
        $values = $values . ")";

        $queryString = "INSERT INTO user ".$columns." values".$values."";

        try{
            if($conn->query($queryString) === TRUE){
                createLog($_SESSION['id'], "CREATE_USER", $conn);
                header("location:  admin_dashboard.php");
            }
        }
        catch (Exception $e){
            setAlert("Something unexpected happened, please contact admin for assistance", "warning");
            header("location: manage_user.php");
        }
    }
    else {
        header("location: manage_user.php");
    }
?>