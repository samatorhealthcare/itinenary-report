<?php
    session_start();
    include_once "db_conn.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/sanitizer.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/validation_message_handler.php";
    include_once "utilities/log_handler.php";

    $username = $_POST['input-username'];
    $password = $_POST['input-password'];
    $emp_id = isset($_POST['input-emp-id']) ? $_POST['input-emp-id'] : '';
    $reports_to = isset($_POST['input-reports-to']) ? $_POST['input-reports-to'] : '';
    //$reports_to_manager = isset($_POST['input-reports-to-lead-1']) ? $_POST['input-reports-to-lead-1'] : '';
    $role = $_POST['input-role'];

    $username = sanitizeInput($username);
    $password = sanitizeInput($password);
    $emp_id = sanitizeInput($emp_id);
    $reports_to = sanitizeInput($reports_to);
    $reports_to_manager = sanitizeInput($reports_to_manager);
    $role = sanitizeInput($role);

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    //Input Validation
    $passedValidation = true;

    if (isEmptyValidation($username)){
        $passedValidation = false;
        setValidationErrorMessage('input-username', "Username tidak boleh kosong");
    }

    if (isEmptyValidation($emp_id)){
        $passedValidation = false;
        setValidationErrorMessage('input-emp-id', "ID tidak boleh kosong");
    }

    if (isEmptyValidation($password)){
        $passedValidation = false;
        setValidationErrorMessage('input-password', "Password tidak boleh kosong");
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

        $queryString = "SELECT * FROM user WHERE emp_id = '".$emp_id."'";
        $queryItinerary = "SELECT * FROM user WHERE emp_id = '".$emp_id."'";

        $user = $conn->query($queryString)->fetch_assoc();
        $userItinerary =  $connDB->query($queryItinerary)->fetch_assoc();

        if(isset($user)){
            setAlert("User dengan employee ID yang sama sudah ada", "danger");
            header("location: manage_user.php");
        }
        else {
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
            $queryItinerary = "INSERT INTO user ".$columns." values".$values."";

            try{
                if($conn->query($queryString) === TRUE){
                    $connDB->query($queryString);
                    createLog($_SESSION['ID'], "CREATE_USER", $conn);
                    createLog($_SESSION['ID'], "CREATE_USER", $connDB);

                    // $queryString = "SELECT * FROM user WHERE emp_id = '".$emp_id."'";
                    // $user = $conn->query($queryString)->fetch_assoc();
                    // updateUserDirectorRelation($user['id'], $conn);

                    setAlert("Berhasil membuat user", "success");
                    header("location:  admin_dashboard.php");
                }
            }
            catch (Exception $e){
                setAlert("Gagal membuat user, harap kontak admin", "warning");
                header("location: manage_user.php");
            }
        }
    }
    else {
        header("location: manage_user.php");
    }
?>