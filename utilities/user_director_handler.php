<?php
    include_once "util.php";

    function fetchDirectorFromUser($user_id, $conn){
        $queryString = "SELECT * FROM user_director_relation WHERE user_id = ".$user_id;

        $result = $conn->query($queryString);

        return $result;
    }

    function createUserDirectorRelation($user_id, $director_id, $conn){
        $queryString = "INSERT INTO user_director_relation(user_id, director_id) VALUES(".$user_id.", ".$director_id.")";

        if($conn->query($queryString)){
            return 1;
        }
        return 0;
    }

    function fetchUser($user_id, $conn){
        $queryString = "SELECT * FROM user WHERE id = ".$user_id;

        return $conn->query($queryString)->fetch_assoc();
    }

    function updateUserDirectorRelation($user_id, $conn){
        $currentUser = fetchUser($user_id, $conn);

        $loop = true;
        $currentRole = fetchNumericRole($currentUser['role']);
        $success = true;

        // Isn't a director
        if($currentRole !== 3){
            while($loop){
                $currentUser = fetchUser($currentUser['reports_to'], $conn);
                
                if(fetchNumericRole($currentUser['role']) === 3){
                    $loop = false;
                }

                if(!isset($currentUser) || $currentUser['role'] === ""){
                    $success = false;
                    $loop = false;
                }
            }
            
            if($success){
                //variable memasukkan atasan
                $director_id = $currentUser['id'];

                $queryString = "SELECT * from user_director_relation WHERE user_id = ".$user_id;

                $relation = $conn->query($queryString)->fetch_assoc();

                if(isset($relation)){
                    $queryString = "UPDATE user_director_relation SET director_id = ".$director_id." WHERE user_id = ".$user_id;
                    if ($conn->query($queryString)){
                        return 1;
                    }
                    else {
                        return 0;
                    }
                }
                else {
                    //membuat atasan sesuai klik id yang dipilih user
                    createUserDirectorRelation($user_id, $director_id, $conn);
                    return 1;
                }
            }
        }
        return 0;
    }

    function deleteUserDirectorRelation($user_id, $conn){
        $queryString = "DELETE FROM user_director_relation WHERE user_id = ".$user_id;

        if ($conn->query($queryString)){
            return 1;
        }
        return 0;
    }

    function fetchDirectorReports($director_id, $conn){
        $queryString = "SELECT r.id as id, u.username as username, r.upload_at as upload_at, r.location as location FROM report r, user u WHERE r.report_by IN (SELECT user_id FROM user_director_relation WHERE director_id = ".$director_id.") AND r.report_by = u.id";
        $result = $conn->query($queryString);
        $arr = [];
        $num = mysqli_num_rows($result);
        if ($num <= 1) {
            array_push($arr, $result->fetch_assoc());
        } else {
            while($row = $result->fetch_assoc()) {
                array_push($arr, $row);
            }
        }
        return $arr;
    }
?>