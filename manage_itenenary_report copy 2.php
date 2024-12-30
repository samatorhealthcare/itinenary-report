<?php 
    session_start();
    include_once "db_conn_itinerary.php";
    include_once "utilities/util.php";
    include_once "utilities/alert_handler.php";
    include_once "logged_user_check.php";

    $queryString = "SELECT * FROM user";
    
    $result = $connDB->query($queryString);
    $uuuser = "SELECT * FROM user WHERE id ='".$_SESSION['ID']."'";
    $report = $connDB->query($uuuser);
    $user_login = mysqli_fetch_assoc($report);
        $report_user = "SELECT * FROM user WHERE id='".$user_login['reports_to']."'";
        $result_report = $connDB->query($report_user);
        $reports_to = mysqli_fetch_assoc($result_report);

        $report_supervisor = "SELECT * FROM user WHERE role='supervisor'";
        $result_s = $connDB->query($report_supervisor);
        $reports_to_spv = mysqli_fetch_assoc($result_s);

        $report_manager = "SELECT * FROM user WHERE role='manager'";
        $result_m = $connDB->query($report_manager);
        $reports_to_manager = mysqli_fetch_assoc($result_m);

        $report_gmanager = "SELECT * FROM user WHERE role='gmanager'";
        $result_gm = $connDB->query($report_gmanager);
        $reports_to_gmanager = mysqli_fetch_assoc($result_gm);

        $report_director = "SELECT * FROM user WHERE role='director'";
        $result_dr = $connDB->query($report_director);
        $reports_to_director = mysqli_fetch_assoc($result_dr);

        $report_director_2 = "SELECT * FROM user WHERE role='director'";
        $result_dr_2 = $connDB->query($report_director_2);
        $reports_to_director_2 = mysqli_fetch_assoc($result_dr_2);
    
    
    
    $users = [];
    $spv = [];
    $manager = [];
    $gmanager = [];
    $director = [];
    $director_2 = [];


    while($temp = mysqli_fetch_assoc($result_s)){
        array_push($spv, $temp);
    }   
    while($temp = mysqli_fetch_assoc($result_m)){
        array_push($manager, $temp);
    }  
    while($temp = mysqli_fetch_assoc($result_gm)){
        array_push($gmanager, $temp);
    } 
    while($temp = mysqli_fetch_assoc($result_dr)){
        array_push($director, $temp);
    } 
    while($temp = mysqli_fetch_assoc($result_dr_2)){
        array_push($director_2, $temp);
    } 
    
    
    
    if(isset($_GET['item_id'])){
        $queryString = "SELECT U.id as id, U.emp_id as emp_id, U.username as username, U.password as password, U.role as role, U.reports_to as reports_to, U.reports_to_lead_1 as reports_to_supervisor, U.reports_to_lead_2 as reports_to_manager, U.reports_to_lead_3 as reports_to_gmanager, U.reports_to_lead_4 as reports_to_director, REP.role as reports_to_role FROM user U, user REP WHERE U.ID=".$_GET['item_id']." AND U.REPORTS_TO = REP.id";
        $result = $connDB->query($queryString);
            
            
        $user = mysqli_fetch_assoc($result);
        
        if ($user == null){
            $queryString = "SELECT * from user WHERE ID=".$_GET['item_id']."";
            $result = $connDB->query($queryString);
            $user = mysqli_fetch_assoc($result);
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>itinerary Report</title>
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/934b5328b9.js" crossorigin="anonymous"></script>
    
    <style>
        .main {
            color: rgb(30, 30, 60);
        }

        .bg-main {
            background-color: rgb(30, 30, 60);
        }

        hr {
            margin: 1rem -1rem;
        }
    </style>
</head>
<body class="bg-main">
    <?php if(isset($_SESSION['ALERT'])) { ?>
        <div class="position-absolute end-0 me-3" style="margin-top: 84px;">
            <div class="alert alert-<?= $_SESSION['ALERT']['TYPE'] ?> mb-0 p-2 fade show" role="alert">
                <?= $_SESSION['ALERT']['MESSAGE'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="width: 10px; height: 10px;"></button>
            </div>
        </div>
    <?php } ?>
    <section id="section-header">
        <nav class="navbar navbar-expand-lg bg-body-tertiary p-3 d-flex justify-content-between">
            <?php 
                if($_SESSION['ROLE'] == "sales"){
                    ?>  <form method="GET" action="user_itenenary_dashboard.php">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                        </form>
                <?php } else {
                    ?> <form method="GET" action="user_itenenary_dashboard.php">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                        </form>
               <?php } 
            ?>
           
            <form method="GET" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </section>
    <section id="section-content">
        <div class="container my-5" style="max-width: 720px;">
            <div class="card">
                <div class="card-body">
                    <!-- <?php if(isset($user)) {?>
                        <h2>Perbaharui Data <?= $user['emp_id']; ?></h2>
                        <form action="update_user.php" method="POST">
                    <?php } else { ?>
                        <h2>Buat Itenenary</h2>
                        <form action="create_user.php" method="POST">
                    <?php } ?> -->
                        <hr>
                        <div class="row">
                            <div class="col-sm-5 mb-3">
                                        <label for="select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="select-lapor-ke-role" disabled>
                                                    <option value="supervisor" <?= $reports_to_spv['role'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                                 </select>
                                            <label for="2-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="2-select-lapor-ke-role" disabled>
                                                    <option value="manager" <?= $reports_to_manager['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                                                </select>
                                            <label for="3-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="3-select-lapor-ke-role" disabled>
                                                    <option value="gmanager" <?= $reports_to_gmanager['role'] == 'gmanager' ? 'selected' : ''; ?>>General Manager</option>
                                                </select>
                                            <label for="4-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="4-select-lapor-ke-role" disabled>
                                                   <option value="director" <?= $reports_to_director['role'] == 'director' ? 'selected' : ''; ?>>Director Bidang</option>
                                                </select>
                                            <label for="4-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="4-select-lapor-ke-role" disabled>
                                                   <option value="director" <?= $reports_to_director['role'] == 'director' ? 'selected' : ''; ?>>Director Pembimbing</option>
                                                </select>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label for="select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-1" class="form-select" id="select-lapor-ke">
                                                    
                                                    <?php if($user['reports_to_lead_1'] == 0) { ?>
                                                        <option value="<?= $reports_to_spv['id']; ?>" class="<?="option-lapor-ke role-" . $reports_to_spv['role'];?>" style="display: none;" selected><?= $reports_to_spv['username']; ?></option>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($spv as $temp) { ?>
                                                            <?php if($reports_to_spv['id'] == $temp['reports_to_lead_1']) { ?>
                                                                <option value="<?= $reports_to_spv['id']; ?>" class="<?="option-lapor-ke role-" . $reports_to_spv['role'];?>" style="display: none;" selected><?= $reports_to_spv['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                    <?php } else{ ?>
                                                        <option value="" hidden selected disabled>-</option>
                                                    <?php } ?>
                                                </select>
                                                <label for="2-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-2" class="form-select" id="2-select-lapor-ke">
                                                    <?php if( $user['reports_to_lead_2'] == 0) { ?>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($manager as $temp) { ?>
                                                            <?php if($reports_to_manager['id'] == $temp['reports_to_lead_2']) { ?>
                                                                <option value="<?= $reports_to_manager['id']; ?>" class="<?="2-option-lapor-ke role-" . $reports_to_manager['role'];?>" style="display: none;" selected><?= $reports_to_manager['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="2-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                    <?php } else{ ?>
                                                        <option value="" hidden selected disabled>-</option>
                                                    <?php } ?>
                                                </select>
                                                <label for="3-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-3" class="form-select" id="3-select-lapor-ke">
                                                    <?php if( $user['reports_to_lead_3'] == 0) { ?>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($gmanager as $temp) { ?>
                                                            <?php if($reports_to_gmanager['id'] == $temp['reports_to_lead_3']) { ?>
                                                                <option value="<?= $reports_to_gmanager['id']; ?>" class="<?="3-option-lapor-ke role-" . $reports_to_gmanager['role'];?>" style="display: none;" selected><?= $reports_to_gmanager['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="3-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                    <?php } else{ ?>
                                                        <option value="" hidden selected disabled>-</option>
                                                    <?php } ?>
                                                </select>
                                                <label for="4-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-4" class="form-select" id="4-select-lapor-ke">
                                                    <?php if( $user['reports_to_lead_4'] == 0) { ?>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($director as $temp) { ?>
                                                            <?php if($reports_to_director['id'] == $temp['reports_to_lead_4']) { ?>
                                                                <option value="<?= $reports_to_director['id']; ?>" class="<?="4-option-lapor-ke role-" . $reports_to_director['role'];?>" style="display: none;" selected><?= $reports_to_director['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="4-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                    <?php } else{ ?>
                                                        <option value="" hidden selected disabled>-</option>
                                                    <?php } ?>
                                                </select>
                                                <label for="5-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-5" class="form-select" id="5-select-lapor-ke">
                                                    <?php if( $user['reports_to_lead_4'] == 0) { ?>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($director_2 as $temp) { ?>
                                                            <?php if($reports_to_director['id'] == $temp['reports_to_lead_4']) { ?>
                                                                <option value="<?= $reports_to_director['id']; ?>" class="<?="5-option-lapor-ke role-" . $reports_to_director['role'];?>" style="display: none;" selected><?= $reports_to_director['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="4-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                        
                                                    <?php } else{ ?>
                                                        <option value="" hidden selected disabled>-</option>
                                                    <?php } ?>
                                                </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="fw-bold">Pilih salah satu</label>
                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-type-activity" type="checkbox" name="input-type-activity" id="input-check-type-activity-1">
                                                    <label class="form-check-label" for="input-check-proyek-1">Dinas Luar Kota</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-type-activity" type="checkbox" name="input-type-activity" id="input-check-type-activity-2">
                                                    <label class="form-check-label" for="input-check-proyek-2">Kunjungan</label>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-type-activity'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-type-activity'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-sppd" class="form-label mb-0 fw-bold">No SPPD</label>
                                        <input type="text" class="form-control" id="input-sppd" name="input-sppd">
                                        <?php if(isset($_SESSION['ERROR']['input-sppd'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-sppd'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-duration" class="form-label mb-0 fw-bold">Durasi (Hari)</label>
                                        <input type="number" min="0" class="form-control" id="input-duration" name="input-duration" step="1" label="Hari" required>
                                        <?php if(isset($_SESSION['ERROR']['input-duration'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-duration'] ?>
                                            </div>
                                            
                                        <?php } ?>
                                    </div>
                                    <button id="btn-create-activity" class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-create-activity">Tambah Aktivitas</button>
                                    <div class="col-6 mb-3">
                                        <label for="input-spi" class="form-label mb-0 fw-bold">No SPI</label>
                                        <input type="text" class="form-control" id="input-spi" name="input-spi">
                                        <?php if(isset($_SESSION['ERROR']['input-spi'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-spi'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                     <div class="col-6 mb-3">
                                        <label for="input-date-spi" class="form-label mb-0 fw-bold">Tanggal SPI</label>
                                        <input type="date" class="form-control" id="input-date-spi" name="input-date-spi">
                                        <?php if(isset($_SESSION['ERROR']['input-date-spi'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-date-spi'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-date-contract" class="form-label mb-0 fw-bold">Tanggal PO/SPK/Kontrak</label>
                                        <input type="date" class="form-control" id="input-date-contract" name="input-date-contract">
                                        <?php if(isset($_SESSION['ERROR']['input-date-contract'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-date-contract'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                        </div>

                            <div class="col-md-1">
                                <br>
                                <button onclick="add_more()" class="btn btn-dark">Add More</button>
                                <button onclick="delete_row('1')" type="button" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </form>
                </div>










                                    <div class="col-12 mb-3">
                                        <label class="fw-bold">Proyek</label>
                                        <div class="row">
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-1">
                                                    <label class="form-check-label" for="input-check-proyek-1">Alkes - Radiologi</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-2">
                                                    <label class="form-check-label" for="input-check-proyek-2">Alkes - Non Radiologi</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-3">
                                                    <label class="form-check-label" for="input-check-proyek-3">Nurse Call - NC</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-4">
                                                    <label class="form-check-label" for="input-check-proyek-4">IGM</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-5">
                                                    <label class="form-check-label" for="input-check-proyek-5">PTS</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-6">
                                                    <label class="form-check-label" for="input-check-proyek-6">MOT</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-8">
                                                    <label class="form-check-label" for="input-check-proyek-8">Industrial Chemical</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-9">
                                                    <label class="form-check-label" for="input-check-proyek-9">Food Chemical</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-10">
                                                    <label class="form-check-label" for="input-check-proyek-10">Oxycan</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-11">
                                                    <label class="form-check-label" for="input-check-proyek-11">BMHP- Drymist</label>
                                                </div>
                                            </div>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-12">
                                                    <label class="form-check-label" for="input-check-proyek-12">Sippol - Personal Hygiene</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check d-flex align-items-center">
                                                    <input class="form-check-input" type="checkbox" name="input-proyek" id="input-check-proyek-7">
                                                    <input type="text" class="ms-2 form-control" placeholder="lainnya">
                                                </div>
                                            </div>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-proyek'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-proyek'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-location" class="form-label mb-0 fw-bold">Instansi</label>
                                        <input type="text" class="form-control" id="input-location" name="input-location[]" required>
                                        <?php if(isset($_SESSION['ERROR']['input-proyek'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-proyek'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-city" class="form-label mb-0 fw-bold">Kota Kunjungan</label>
                                        <input type="text" class="form-control" id="input-city" name="input-city[]" required>
                                        <?php if(isset($_SESSION['ERROR']['input-city'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-city'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    
                                    <div class="col-3 mb-3">
                                        <label for="input-opportunity" class="form-label mb-0 fw-bold">Peluang</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <input type="number" class="input-number w-100" id="input-opportunity" name="input-opportunity[]" max="100" min="0">
                                            <p class="mb-0">%</p>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-opportunity'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-opportunity'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
<?php destroyValidationErrorMessages(); ?>



<script>
    $(document).ready(function() {
        if ($('#select-lapor-ke').val() != '') {
            $('.role-' + $('#select-lapor-ke-role').val()).show();
        }

        $('#select-lapor-ke-role').on('change', function() {
            $('#select-lapor-ke').val('');
            $('.option-lapor-ke').hide();
            let role = $('#select-lapor-ke-role').val();
            $('.role-' + role).show();
        });
    });

 var activity = 0;
    function add_more() {
    counter++
    activity++
    var newDiv = `<div id="product_row${counter}" class="row">
                    <label>Buat Aktivitas ${activity}</label><br>
                    <div class="col-6 mb-3">
                        <label for="input-due" class="form-label mb-0 fw-bold">Tanggal Aktivitas</label>
                            <input type="date" class="form-control" id="input-date${counter}" name="input-due">
                            <?php if(isset($_SESSION['ERROR']['input-date'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-date'] ?>
                                </div>
                            <?php } ?>
                    </div>
                     <div class="col-6 mb-3">
                            <label for="input-instansi" class="form-label mb-0 fw-bold">Instansi</label>
                            <input type="text" class="form-control" id="input-instansi${counter}" name="input-instansi" required>
                            <?php if(isset($_SESSION['ERROR']['input-instansi'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-instansi'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-6 mb-3">
                            <label for="input-kode-proyek" class="form-label mb-0 fw-bold">Kode Proyek</label>
                            <input type="text" class="form-control" id="input-kode-proyek${counter}" name="input-kode-proyek" required>
                            <?php if(isset($_SESSION['ERROR']['input-kode-proyek'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-kode-proyek'] ?>
                                </div>
                            <?php } ?>
                    </div>
                     <div class="col-6 mb-3">
                            <label for="input-nama-proyek" class="form-label mb-0 fw-bold">Nama Proyek</label>
                            <input type="text" class="form-control" id="input-nama-proyek${counter}" name="input-nama-proyek" required>
                            <?php if(isset($_SESSION['ERROR']['input-nama-proyek'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-nama-proyek'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <label>Product Name</label>
                        <input id="name${counter}" type="text" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Price</label>
                        <input id="price${counter}" type="number" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <br>
                        <button onclick="delete_row('${counter}')" type="button" class="btn btn-danger">Delete</button>
                    </div>
                </div>`
    var form = document.getElementById('input-form')
    form.insertAdjacentHTML('beforeend', newDiv);
}

function delete_row(id) {
    activity--;
    document.getElementById('product_row'+id).remove()
}

function submit_form() {
    var productData = []
    for (var i = 1; i <= counter; i++){
        var product_row = document.getElementById('product_row'+i)
        if (product_row) {
            var product_name = document.getElementById('name' + i).value
            var price = document.getElementById('price' + i).value
            var data = {
                name: product_name,
                price: price
            }
            productData.push(data)
        }
    }

    axios.post('/dynamicinput/create_report_itenenary.php', {
        productData: productData
    }).then(resp => {
        window.location.reload()
    })
}










</script>








<?php 
    unsetAlert();
?>