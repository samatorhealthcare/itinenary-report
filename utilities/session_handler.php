<?php
    function checkSession(){
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            if (time() - $_SESSION['LAST_ACTIVITY'] > 1800){
                // last request was more than 30 minutes ago
                session_unset();     // unset $_SESSION variable for the run-time 
                session_destroy();   // destroy session data in storage
                return false;
            }
        }
        return true;
    }

    function updateSession(){
        if (checkSession()){
            $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
            session_regenerate_id(true);
        }
    }

    function createSession($user_id, $role, $username, $emp_id){
        $_SESSION['ID'] = $user_id;
        $_SESSION['ROLE'] = $role;
        $_SESSION['USERNAME'] = $username;
        $_SESSION['LAST_ACTIVITY'] = time();
        $_SESSION['EMP_ID'] = $emp_id;
    }

    function checkSessionRole($role){
        if(isset($_SESSION['ROLE'])){
            return in_array($_SESSION['ROLE'], $role);
        }
        return false;
    }
?>