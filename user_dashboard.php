<?php 
    session_start();
    include_once "db_conn.php";
    include_once "utilities/util.php";
    include_once "utilities/alert_handler.php";
    include_once "logged_user_check.php";

    $queryString = "SELECT * FROM user";
    
    $result = $conn->query($queryString);
    $uuuser = "SELECT * FROM user WHERE id ='".$_SESSION['ID']."'";
    $report = $conn->query($uuuser);
    $user_login = mysqli_fetch_assoc($report);
        $report_user = "SELECT * FROM user WHERE id='".$user_login['reports_to']."'";
        $result_report = $conn->query($report_user);
        $reports_to = mysqli_fetch_assoc($result_report);

        $report_supervisor = "SELECT * FROM user WHERE role='supervisor'";
        $result_s = $conn->query($report_supervisor);
        $reports_to_spv = mysqli_fetch_assoc($result_s);

        $report_manager = "SELECT * FROM user WHERE role='manager'";
        $result_m = $conn->query($report_manager);
        $reports_to_manager = mysqli_fetch_assoc($result_m);

        $report_gmanager = "SELECT * FROM user WHERE role='gmanager'";
        $result_gm = $conn->query($report_gmanager);
        $reports_to_gmanager = mysqli_fetch_assoc($result_gm);

        $report_director = "SELECT * FROM user WHERE role='director'";
        $result_dr = $conn->query($report_director);
        $reports_to_director = mysqli_fetch_assoc($result_dr);

        $report_director_2 = "SELECT * FROM user WHERE role='director'";
        $result_dr_2 = $conn->query($report_director_2);
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
    
    // var_dump($gmanager);
    
    if(isset($_GET['item_id'])){
        $queryString = "SELECT U.id as id, U.emp_id as emp_id, U.username as username, U.password as password, U.role as role, U.reports_to as reports_to, U.reports_to_lead_1 as reports_to_supervisor, U.reports_to_lead_2 as reports_to_manager, U.reports_to_lead_3 as reports_to_gmanager, U.reports_to_lead_4 as reports_to_director, REP.role as reports_to_role FROM user U, user REP WHERE U.ID=".$_GET['item_id']." AND U.REPORTS_TO = REP.id";
        $result = $conn->query($queryString);
            
        $user = mysqli_fetch_assoc($result);
        
        if ($user == null){
            $queryString = "SELECT * from user WHERE ID=".$_GET['item_id']."";
            $result = $conn->query($queryString);
            $user = mysqli_fetch_assoc($result);
        }
    }
    // var_dump($manager);
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Dashboard</title>
    <link rel="icon" href="asset/logo.png" type="image/x-icon">
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    
    <style>
        .main {
            color: rgb(30, 30, 60);
        }

        .bg-main {
            background-color: rgb(30, 30, 60);
        }

        a.nav-link, a.nav-link:hover{
            color: white;
        }

        a.nav-link.active {
            color: black;
        }

        .input-number {
            outline: none;
            border: none;
        }
    </style>
</head>
<body class="bg-main position-relative">
    <?php if(isset($_SESSION['ALERT'])) { ?>
        <div class="position-absolute end-0 me-3" style="margin-top: 84px;">
            <div class="alert alert-<?= $_SESSION['ALERT']['TYPE'] ?> mb-0 p-2 fade show" role="alert">
                <?= $_SESSION['ALERT']['MESSAGE'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="width: 10px; height: 10px;"></button>
            </div>
        </div>
    <?php } unsetAlert(); ?>
     <section id="section-header">
        <nav class="navbar bg-light fixed-top shadow-sm">
            <div class="container-fluid">
                <h5 class="me-3 mb-0">Masuk sebagai, <?= $_SESSION['USERNAME'] ?> !</h5>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end " tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                            <li class="nav-item">
                                <button id="btn-create-report" class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-create-report">Buat Laporan</button>  
                            </li>
                            <li class="nav-item py-3">
                                <form method="GET" action="user_dashboard_itinerary.php">
                                    <button type="submit" class="btn btn-danger">Dashboard itinerary</button>
                                </form>
                            </li>
                            <li class="nav-item py-3">
                                <form method="GET" action="logout.php">
                                    <button type="submit" class="btn btn-danger">Logout</button>
                                </form>
                            </li>
                        </ul>
            
                    </div>
                </div>
            </div>
        </nav>
        <nav class="navbar navbar-expand-lg bg-body-tertiary p-3 d-flex justify-content-between">
          <div class="modal fade" role="dialog" tabindex="-1" id="modal-create-report">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Buat Laporan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="form-create-report" action="create_report_optimized.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="row">
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
                                        <input type="text" class="form-control" id="input-location" name="input-location" required>
                                        <?php if(isset($_SESSION['ERROR']['input-proyek'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-proyek'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-city" class="form-label mb-0 fw-bold">Kota Kunjungan</label>
                                        <input type="text" class="form-control" id="input-city" name="input-city" required>
                                        <?php if(isset($_SESSION['ERROR']['input-city'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-city'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-4 mb-3">
                                        <label for="input-visit" class="form-label mb-0 fw-bold">Kunjungan Ke</label>
                                        <input type="number" class="form-control" id="input-visit" name="input-visit" step="1" required>
                                        <?php if(isset($_SESSION['ERROR']['input-visit'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-visit'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-5 mb-3">
                                        <label for="input-prospect" class="form-label mb-0 fw-bold">Nilai Prospek</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <p class="mb-0 me-3">Rp</p>
                                            <input type="text" class="input-number input-thousand-separator w-100" id="input-prospect" required>
                                            <?php if(isset($_SESSION['ERROR']['input-prospect'])) { ?>
                                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                    <?= $_SESSION['ERROR']['input-prospect'] ?>
                                                </div>
                                            <?php } ?>
                                            <input type="hidden" name="input-prospect">
                                        </div>
                                    </div>
                                    <div class="col-3 mb-3">
                                        <label for="input-opportunity" class="form-label mb-0 fw-bold">Peluang</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <input type="number" class="input-number w-100" id="input-opportunity" name="input-opportunity" max="100" min="0">
                                            <p class="mb-0">%</p>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-opportunity'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-opportunity'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-competitor" class="form-label mb-0 fw-bold">Pesaing / Kandidat Pesaing</label>
                                        <input type="text" class="form-control" id="input-competitor" name="input-competitor">
                                        <?php if(isset($_SESSION['ERROR']['input-competitor'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-competitor'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <label for="input-due" class="form-label mb-0 fw-bold">Dibutuhkan Kapan</label>
                                        <input type="date" class="form-control" id="input-due" name="input-due">
                                        <?php if(isset($_SESSION['ERROR']['input-due'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-due'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="input-note" class="form-label mb-0 fw-bold">Keterangan</label>
                                        <textarea name="input-note" class="form-control" id="input-note"></textarea>
                                        <?php if(isset($_SESSION['ERROR']['input-note'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-note'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-12 mb-3">
                                        <label for="input-sales-note" class="form-label mb-0 fw-bold">Komentar untuk Management</label>
                                        <textarea name="input-sales-note" class="form-control" id="input-sales-note"></textarea>
                                        <?php if(isset($_SESSION['ERROR']['input-sales-note'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-sales-note'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-sm-9 mb-3">
                                        <label for="input-attachment" class="form-label mb-0 fw-bold">Lampiran</label>
                                        <input type="file" class="form-control" id="input-attachment" name="input-attachment[]" accept="image/png, image/jpg, image/jpeg, image/jfif" multiple>
                                        <?php if(isset($_SESSION['ERROR']['input-attachment'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-attachment'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
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
                                                   <option value="director" <?= $reports_to_director['role'] == 'director' ? 'selected' : ''; ?>>Director 1</option>
                                                </select>
                                            <label for="5-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="5-select-lapor-ke-role" disabled>
                                                   <option value="director" <?= $reports_to_director_2['role'] == 'director' ? 'selected' : ''; ?>>Director 2</option>
                                                </select>
                                    </div>
                                    <div class="col-sm-6 mb-3">
                                        <label for="select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-1" class="form-select" id="select-lapor-ke">
                                                     <option value="<?= $reports_to_spv['id']; ?>" class="<?="option-lapor-ke role-" . $reports_to_spv['role'];?>" style="display: none;" selected><?= $reports_to_spv['username']; ?></option>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($spv as $temp) { ?>
                                                            <?php if($reports_to_spv['id'] == $temp['id']) { ?>
                                                                <option value="<?= $reports_to_spv['id']; ?>" class="<?="option-lapor-ke role-" . $reports_to_spv['role'];?>" style="display: none;" selected><?= $reports_to_spv['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                </select>
                                                <label for="2-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-2" class="form-select" id="2-select-lapor-ke">
                                                    <option value="<?= $reports_to_manager['id']; ?>" class="<?="2-option-lapor-ke role-" . $reports_to_manager['role'];?>" style="display: none;" selected><?= $reports_to_manager['username']; ?></option>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($manager as $temp) { ?>
                                                            <?php if($reports_to_manager['id'] == $temp['id']) { ?>
                                                                <option value="<?= $reports_to_manager['id']; ?>" class="<?="2-option-lapor-ke role-" . $reports_to_manager['role'];?>" style="display: none;" selected><?= $reports_to_manager['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="2-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                </select>
                                                <label for="3-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-3" class="form-select" id="3-select-lapor-ke">
                                                    <option value="<?= $reports_to_gmanager['id']; ?>" class="<?="3-option-lapor-ke role-" . $reports_to_gmanager['role'];?>" style="display: none;" selected><?= $reports_to_gmanager['username']; ?></option>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($gmanager as $temp) { ?>
                                                            <?php if($reports_to_gmanager['id'] == $temp['id']) { ?>
                                                                <option value="<?= $reports_to_gmanager['id']; ?>" class="<?="3-option-lapor-ke role-" . $reports_to_gmanager['role'];?>" style="display: none;" selected><?= $reports_to_gmanager['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="3-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                </select>
                                                <label for="4-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-4" class="form-select" id="4-select-lapor-ke">
                                                    <option value="<?= $reports_to_director['id']; ?>" class="<?="4-option-lapor-ke role-" . $reports_to_director['role'];?>" style="display: none;" selected><?= $reports_to_director['username']; ?></option>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($director as $temp) { ?>
                                                            <?php if($reports_to_director['id'] == $temp['reports_to_lead_1']) { ?>
                                                                <option value="<?= $reports_to_director['id']; ?>" class="<?="4-option-lapor-ke role-" . $reports_to_director['role'];?>" style="display: none;" selected><?= $reports_to_director['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="4-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                </select>
                                                <label for="5-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-5" class="form-select" id="5-select-lapor-ke">
                                                   <option value="" hidden disabled>-</option>
                                                        <?php foreach($director_2 as $temp) {  ?>
                                                            
                                                            <?php if($reports_to_director_2['id'] == $temp['id']) { ?>
                                                                <option value="<?= $reports_to_director_2['id']; ?>" class="<?="5-option-lapor-ke role-" . $reports_to_director_2['role'];?>" style="display: none;" selected><?= $reports_to_director_2['username']; ?></option>
                                                                <?php } else { ?>
                                                                <option value="<?= $temp['id']; ?>" class="<?="5-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                                <option value="" hidden selected disabled>-</option>
                                                            <?php } ?>
                                                        <?php } ?>
                                                </select>
                                                
                                            </div>
                                        </div>
                            </div>
                            <input type="hidden" name="input-latitude" id="input-latitude">
                            <input type="hidden" name="input-longitude" id="input-longitude">
                            <input type="hidden" name="input-project" id="input-project">
                            <div class="modal-footer text-end">
                                <button type="submit" class="btn btn-primary" id="btn-save-report">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>
    </section>
        
        
    <section id="section-content">
        <div class="container">

            <ul role="tablist" class="nav nav-tabs position-relative border-bottom-0 mt-5">
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-unapproved-report" class="nav-link active">Menunggu</a></li>
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-evaluated-report" class="nav-link">Evaluasi</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-unapproved-report">
                    <div class="card" style="border-top-left-radius: 0;">
                        <div class="card-body">
                            <form action="excel_export_all.php" method="POST" name="export_file_type">
                                            <button type="submit" class="btn btn-primary" name="export_excel_all" value="xls">Export EXCEL</button>
                                        </form>
                            <div class="table-responsive">
                                <table class="table table-hover" id="table-unapproved-report">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>Tanggal Upload</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $queryString = "SELECT * FROM report WHERE report_by = '".$_SESSION['ID']."' AND STATUS = 0";
                                    
                                    $result = $conn->query($queryString);

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['upload_at'] ?></td>
                                        <td><?= $row['location'] ?></td>
                                        <td><?= parseReportStatus($row['status']) ?></td>
                                        <td class="py-1">
                                            <div class="d-flex justify-content-end">
                                                <form class="mb-0" method="GET" action="detail_report.php">
                                                    <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                    <button class="btn btn-sm btn-outline-primary" type="submit">Detail</button>
                                                </form>
                                                <form class="mb-0 ms-2" method="GET" action="delete_report.php">
                                                    <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                                </tbody>
                            </table>
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab-evaluated-report">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="table-evaluated-report">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>Tanggal Upload</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $queryString = "SELECT * FROM report WHERE report_by = '".$_SESSION['ID']."' AND STATUS <> 0";
                                    
                                    $result = $conn->query($queryString);

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['upload_at'] ?></td>
                                        <td><?= $row['location'] ?></td>
                                        <td><?= parseReportStatus($row['status']) ?></td>
                                        <td class="py-1 text-end">
                                            <form class="mb-0" method="GET" action="detail_report.php">
                                                <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Detail</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                ?>
                                </tbody>
                            </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

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

    $(document).ready(function() {
        if ($('#2-select-lapor-ke').val() != '') {
            $('.role-' + $('#2-select-lapor-ke-role').val()).show();
        }

        $('#2-select-lapor-ke-role').on('change', function() {
            $('#2-select-lapor-ke').val('');
            
            $('#2-option-lapor-ke').hide();
            let role = $('#2-select-lapor-ke-role').val();
            $('.role-' + role).show();
        });
    });

     $(document).ready(function() {
        if ($('#3-select-lapor-ke').val() != '') {
            $('.role-' + $('#3-select-lapor-ke-role').val()).show();
        }

        $('#3-select-lapor-ke-role').on('change', function() {
            $('#3-select-lapor-ke').val('');
            $('.3-option-lapor-ke').hide();
            let role = $('#3-select-lapor-ke-role').val();
            $('.role-' + role).show();
        });
    });
    $(document).ready(function() {
        if ($('#4-select-lapor-ke').val() != '') {
            $('.role-' + $('#4-select-lapor-ke-role').val()).show();
        }

        $('#4-select-lapor-ke-role').on('change', function() {
            $('#4-select-lapor-ke').val('');
            $('.4-option-lapor-ke').hide();
            let role = $('#4-select-lapor-ke-role').val();
            $('.role-' + role).show();
        });
    });
    $(document).ready(function() {
        if ($('#5-select-lapor-ke').val() != '') {
            $('.role-' + $('#5-select-lapor-ke-role').val()).show();
        }

        $('#5-select-lapor-ke-role').on('change', function() {
            $('#5-select-lapor-ke').val('');
            $('.5-option-lapor-ke').hide();
            let role = $('#5-select-lapor-ke-role').val();
            $('.role-' + role).show();
        });
    });



    $(document).ready(function() {
        let tableUnapprovedReport = new DataTable('#table-unapproved-report',  {
            columns: [
                null,
                null,
                null,
                null,
                { orderable: false }
            ]
        });

        let tableEvaluatedReport = new DataTable('#table-evaluated-report',  {
            columns: [
                null,
                null,
                null,
                null,
                { orderable: false }
            ]
        });

        $('#btn-create-report').on('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $('#input-latitude').val(position.coords.latitude);
                    $('#input-longitude').val(position.coords.longitude);
                });
            } else { 
                alert("Geolocation is not supported by this browser.");
            }
        });

        $('#btn-save-report').on('click', function() {
            let projects = '';
            $('.form-check-input').each(function(index, element) {
                if ($(element).is(':checked')) {
                    if ($(element).next().is('LABEL')) {
                        projects += $(element).next().text();
                    }
                    else {
                        projects += $(element).next().val();
                    }
                    projects += ';';
                }
            });
            $('#input-project').val(projects);
            $('#form-create-report').submit();
        });

        //untuk input nilai
        $('.input-thousand-separator').on('focus', function() {
            let val = $(this).val();
            $(this).attr('type', 'number');
       
            if (val != '') {
                while(val.indexOf('.') != -1) {
                    val = val.replace('.', '');
                }
                let number = parseInt(val);
                $(this).val(val);
            }
        }).on('blur', function() {
            let val = $(this).val();
            $(this).attr('type', 'text');
            if (val != '') {
                while(val.indexOf('.') != -1) {
                    val = val.replace('.', '');
                }
                let number = parseInt(val);
                $(this).siblings('input[type=hidden]').val(number);
                $(this).val(number.toLocaleString(['ban', 'id']));
            }
        });

        //untuk input kunjungan
        $('input[type=number]').on('change', function() {
            let maxValue = parseInt($(this).attr('max'));
            let enteredValue = parseInt($(this).val());
            
            if (enteredValue > maxValue) {
                $(this).val(maxValue);
            }
        });
    });
</script>
