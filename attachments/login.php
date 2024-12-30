<?php
    session_start();
    include_once "db_conn.php";
    include_once "utilities/util.php";
    include_once "utilities/session_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/log_handler.php";

    if(isset($_POST['input-username']) && isset($_POST['input-password'])){
        
        $uname = validate($_POST['input-username']);
        $pass = validate($_POST['input-password']);

        $sql = "SELECT * FROM user WHERE username = '$uname'";

        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);

            if ($row['username'] === $uname && password_verify($pass, $row['password'])){
                                
                createSession($row['id'], $row['role'], $row['username']);
                createLog($_SESSION['ID'], "LOGIN", $conn);

                if ($row['role'] == "admin"){
                    header("location: admin_dashboard.php");
                }
                elseif ($row['role'] == "sales"){
                    header("location: user_dashboard.php");
                }
                elseif ($row['role'] == "manager" || $row['role'] == "gmanager" || $row['role'] == "director"){
                    header("location: manager_dashboard.php");
                }
                else{
                    header("location: index.php?error=User's role not found");
                }
            }
            else {
                createLog($_SESSION['ID'], "LOGIN_ATTEMPT", $conn);
                setAlert("Unknown username or password", "WARNING");
                header("location: index.php");
            }
        }
        else {
            createLog($_SESSION['ID'], "LOGIN_ATTEMPT", $conn);
            setAlert("Unknown username or password", "WARNING");
            header("location: index.php");
        }
    }
    else {
        createLog($_SESSION['ID'], "LOGIN_ATTEMPT", $conn);
        header("location: error.php");
    }
?>