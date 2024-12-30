<?php 
    session_start();
    // include "logged_user_check.php";
    include_once "db_conn.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/util.php";
    include_once "utilities/alert_handler.php";
    include_once "utilities/session_handler.php";
    include_once "utilities/user_director_handler.php";
    include "logged_user_check.php";

    $queryString = "SELECT * FROM user";
    

    // $result = $conn->query($queryString);
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Manager Dashboard </title>
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
<body class="bg-main">
    <?php if(isset($_SESSION['ALERT'])) { ?>
        <div class="position-absolute end-0 me-3" style="margin-top: 84px;">
            <div class="alert alert-<?= $_SESSION['ALERT']['TYPE'] ?> mb-0 p-2 fade show" role="alert">
                <?= $_SESSION['ALERT']['MESSAGE'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="width: 10px; height: 10px;"></button>
            </div>
        </div>
    <?php } unsetAlert(); ?>
    <section id="section-header">
        <nav class="navbar bg-light fixed-top">
            <div class="container-fluid">
                <h5 class="me-3 mb-0">Masuk sebagai, <?= $_SESSION['USERNAME'] ?> !</h5>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="offcanvasNavbarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                        <?php if($_SESSION['ROLE'] != "director") { ?> 
                            <li class="nav-item">
                                 <form method="GET" action="manage_itinerary_report.php">
                                    <button class="btn btn-primary" type="submit" >Buat itinerary</button>
                                </form>
                            </li>
                        <?php } ?>
                        <li class="nav-item py-3">
                                <form method="GET" action="manager_dashboard_itinerary.php">
                                    <button type="submit" class="btn btn-info text-white">Report Dashboard</button>
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
    </section>
    <section id="section-content">
        <div class="container" style="max-width: sm-576px;">

            <ul role="tablist" class="nav nav-tabs position-relative border-bottom-0 mt-5">
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-unapproved-report" class="nav-link active">Dashboard</a></li>
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-report-history" class="nav-link">History</a></li>
                <?php if($_SESSION['ROLE'] == "director") { ?>
                    
                <?php } else { ?>    
                    <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-my-unapproved-report" class="nav-link">Laporan Saya</a></li>
                    <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-my-evaluated-report" class="nav-link">Evaluasi</a></li>
                <?php } ?>
                 
            </ul>
                    
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-unapproved-report">
                    <div class="card" style="border-top-left-radius: 0;">
                        <div class="card-body">
                            <h2>Aktivitas Laporan</h2>
                                <form action="excel_itinerary.php?item_id=<? $_GET['item_id']; ?>" method="POST" name="export_file_type">
                                            <button type="submit" class="btn btn-primary" name="export_excel_itinerary" value="xls">Export EXCEL</button>
                                </form>
                                
                            <hr>
                            <div class="table-responsive">
                                 <table class="table table-hover" id="table-unapproved-report">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>SPPD</th>
                                        <th>Tanggal Upload</th>
                                        <th>Pelapor</th>
                                        <th>Instansi</th>
                                        <th>Jenis Laporan</th>
                                        <th>Status</th>
                                        
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $statusReport = 0;

                                    if ($_SESSION['ROLE'] === "manager"){
                                        $statusReport = 6;
                                        $queryString = "SELECT r.id as 'id', r.sppd as 'sppd', r.instansi as 'instansi', r.upload_at as 'upload_at', u.emp_id as 'emp_id', r.status as 'status', r.jenis_laporan as 'jenis_laporan', u.username as 'username' FROM full_report r, user u WHERE r.report_by = u.id AND r.need_approval_by_2 = " . $_SESSION['ID'] . " AND r.status < " . $statusReport . " GROUP BY r.sppd";
                                    }
                                    else if($_SESSION['ROLE'] === "gmanager"){
                                         $statusReport = 7;
                                         $queryString = "SELECT r.id as 'id', r.sppd as 'sppd', r.instansi as 'instansi', r.upload_at as 'upload_at', u.emp_id as 'emp_id', r.status as 'status', r.jenis_laporan as 'jenis_laporan', u.username as 'username' FROM full_report r, user u WHERE  r.report_by = u.id AND r.need_approval_by_3 = " . $_SESSION['ID'] . " AND r.status < " . $statusReport . " AND r.jenis_laporan > 0  GROUP BY r.sppd" ;
                                    }
                                    else if($_SESSION['ROLE'] === "director"){
                                        $statusReport = 8;
                    
                                        $queryString = "SELECT 
                                                            r.id as 'id', 
                                                            r.sppd as 'sppd',
                                                            r.upload_at as 'upload_at', 
                                                            r.instansi as 'instansi',
                                                            u.emp_id as 'emp_id', 
                                                            r.status as 'status',
                                                            r.jenis_laporan as 'jenis_laporan', 
                                                            u.username as 'username'
                                                        FROM 
                                                            full_report r, 
                                                            user u 
                                                        WHERE 
                                                            r.report_by = u.id 
                                                            AND r.status < " . $statusReport . " 
                                                            AND (r.need_approval_by_4 = " . $_SESSION['ID'] . " 
                                                            OR r.need_approval_by_5 = " . $_SESSION['ID'] . ")
                                                            GROUP BY sppd";
                                    }
                                    else if($_SESSION['ROLE'] === "supervisor"){
                                        $statusReport = 9;
                                        $queryString = "SELECT r.id as 'id', r.sppd as 'sppd', r.instansi as 'instansi', r.upload_at as 'upload_at', u.emp_id as 'emp_id', r.status as 'status', r.jenis_laporan as 'jenis_laporan', u.username as 'username' FROM full_report r, user u WHERE r.report_by = u.id AND r.need_approval_by = " . $_SESSION['ID'] . " AND r.status < " . $statusReport;
                                    }

                                    try{
                                        $result = $connDB->query($queryString);
                                        // $res = $connDB->query($queryString);
                                    }
                                    catch(Exception $e){
                                        echo $e;
                                    }

                                    while($row = mysqli_fetch_assoc($result)){
                                        
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['sppd'] ?></td>
                                        <td><?= date('d-M-Y', strtotime($row['upload_at'])) ?></td>
                                        <td><?= $row['username'] ?></td>
                                        <td><?= $row['instansi'] ?></td>
                                        <td><?= parseJenisLaporan($row['jenis_laporan']) ?></td>
                                        <td><?= parseReportStatus($row['status']) ?></td>
                                        
                                        <td class="py-1 px-2 text-end">
                                            <form method="GET" action="detail_report_itinerary.php">
                                                <input type="hidden" name="item_id" value=<?= $row['id'] ?>>
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Details</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            </div>
                           
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab-report-history">
                    <div class="card">
                        <div class="card-body">
                            <h2>Riwayat Laporan</h2>
                            <hr>
                            <div class="table-responsive">
                                 <table class="table table-hover" id="table-report-history">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>Tanggal Upload</th>
                                        <th>Pelapor</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $queryString = "SELECT r.id as id, r.attachment as attachment, r.note as note, r.location as location, r.upload_at as upload_at, r.report_by as report_by, ra.type as status, u.username as username FROM report r, report_action ra, user u where r.id = ra.report_id and u.id = r.report_by and ra.user = ".$_SESSION['ID']." ORDER BY ra.action_at DESC";

                                    try{
                                        $result = $conn->query($queryString);
                                    }
                                    catch(Exception $e){
                                        echo $e;
                                    }
                                    
                                    $numericNum = 1;
                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= date('d-M-Y', strtotime($row['upload_at'])) ?></td>
                                        <td><?= $row['username'] ?></td>
                                        <td><?= $row['location'] ?></td>
                                        <td>
                                            <?php 
                                                if ($row['status'] == 1) {
                                                    echo "Approved";
                                                } else if ($row['status'] == 0) {
                                                    echo "Rejected";
                                                }
                                            ?>
                                        </td>
                                        <td class="py-1">
                                            <form method="GET" action="detail_report_itinerary.php">
                                                <input type="hidden" name="item_id" value=<?= $row['id'] ?>>
                                                <button class="btn btn-sm btn-outline-primary" type="submit">Details</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php 
                                    $numericNum++;
                                    }
                                ?>
                                </tbody>
                            </table>
                            </div>
        
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="tab-my-unapproved-report">
                        <div class="card">
                            <div class="card-body">
                                <h2>Laporan saya yang belum di setujui</h2>
                                <hr>
                                <div class="table-responsive">
                                    <table class="table table-hover" id="table-my-unapproved-report">
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
                                                <td><?= date('d-M-Y', strtotime($row['upload_at'])) ?></td>
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
                    <div role="tabpanel" class="tab-pane" id="tab-my-evaluated-report">
                        <div class="card">
                            <div class="card-body">
                                <h2>Laporan saya yang sudah di evaluasi</h2>
                                <hr>
                                <div class="table-responsive">
                                     <table class="table table-hover" id="table-my-evaluated-report">
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
                                            <td><?= date('d-M-Y', strtotime($row['upload_at'])) ?></td>
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
</html>

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
            $('#2-select-lapor-ke').empty();
            $('.2-option-lapor-ke').hide();
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
                null,
                null,
                null,
        
                { orderable: false }
            ]
        });

        let tableReportHistory = new DataTable('#table-report-history',  {
            columns: [
                null,
                null,
                null,
                null,
                null,
                { orderable: false }
            ]
        });

        let tableSubordinateReport;
        if ($('#tab-director').length) {
            let tableSubordinateReport = new DataTable('#table-subordinate-report',  {
                columns: [
                    null,
                    null,
                    null,
                    null,
                    { orderable: false }
                ]
            });
        }

        if ($('#tab-my-unapproved-report').length) {
            let tableMyUnapprovedReport = new DataTable('#table-my-unapproved-report',  {
                columns: [
                    null,
                    null,
                    null,
                    null,
                    { orderable: false }
                ]
            });
        }

        if ($('#tab-my-unapproved-report').length) {
            let tableMyEvaluatedReport = new DataTable('#table-my-evaluated-report',  {
                columns: [
                    null,
                    null,
                    null,
                    null,
                    { orderable: false }
                ]
            });
        }

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

        $('.input-thousand-separator').on('focus', function() {
            let val = $(this).val();
            $(this).attr('type', 'number');
            if (val != '') {
                while(val.indexOf('.') != -1) {
                    val = val.replace('.', '');
                }
                let number = parseInt(val);
                $(this).val(number);
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

        $('input[type=number]').on('change', function() {
            let maxValue = parseInt($(this).attr('max'));
            let enteredValue = parseInt($(this).val());

            if (enteredValue > maxValue) {
                $(this).val(maxValue);
            }
        });
    });
</script>