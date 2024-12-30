<?php
    include_once "db_conn_itinerary.php";
    include_once "utilities/alert_handler.php";

    if(isset($_POST['input-id'])){
        try {
            $kota = $_POST['input-kota'];
            $kode_proyek = $_POST['input-kode-proyek'];
            $nama_proyek = $_POST['input-nama-proyek'];
            $instansi = $_POST['input-instansi'];
            
            $queryString = "UPDATE full_report SET kota='".$kota."', kode_proyek = ".$kode_proyek.", nama_proyek = '".$nama_proyek."' WHERE id = ".$_POST['input-id']."";
            $result = $conn->query($queryString);

            //updateUserDirectorRelation($_POST['input-id'], $conn);

            header("location: admin_dashboard.php");

            setAlert("Berhasil mengupdate user", "success");
        } 
        catch(Exception $error) {
            header("location: admin_dashboard.php");

            setAlert("Gagal mengupdate user", "danger");
        }
    }
    else {
        header("location: logout.php");
    }
?>