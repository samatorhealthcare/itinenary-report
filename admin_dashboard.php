<?php 
    session_start();
    include "db_conn.php";
    include "utilities/alert_handler.php";
    include "utilities/util.php";
    include "logged_user_check.php";

    function fetchUsers($conn){
        $queryString = "SELECT * FROM user";

        $result = $conn->query($queryString);
        
        $users = [];
        while($user = mysqli_fetch_assoc($result)){
            array_push($users, $user);
        }   
        
        return $users;
    }

    function fetchReports($conn){
        $queryString = "SELECT * FROM report";

        $result = $conn->query($queryString);

        $reports = [];
        while($report = mysqli_fetch_assoc($result)){
            array_push($reports, $report);
        }

        return $reports;
    }

    function getUser($users, $user_id){
        foreach($users as $user){
            if ($user['id'] == $user_id){
                return $user;
            }
        }
    }

    //Page Constructor
    $users = fetchUsers($conn);
    $reports = fetchReports($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Admin Dashboard </title>
    <link rel="icon" href="asset/logo.png" type="image/x-icon">
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

        #list-action li:hover, .list-action li:hover{
            color: #fff;
            background-color: #0d6efd;
        }

        #list-action li, .list-action li {
            padding: 0px 12px;
        }

        #list-action, .list-action {
            width: 150px;
            padding: 6px 0px;
            z-index: 1000;
            position: absolute;
            display: none;
            cursor: default;
            top: 0;
            left: 0;
        }

        a.nav-link, a.nav-link:hover{
            color: white;
        }

        a.nav-link.active {
            color: black;
        }
    </style>
</head>
<body class="bg-main position-relative">
    <?php if(isset($_SESSION['ALERT'])) { ?>
        <div class="position-absolute end-0 " style="margin-top: 10px;">
            <div class="alert alert-<?= $_SESSION['ALERT']['TYPE'] ?> mb-0 p-2 fade show" role="alert">
                <?= $_SESSION['ALERT']['MESSAGE'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="width: 10px; height: 10px;"></button>
            </div>
        </div>
    <?php } unsetAlert(); ?>
    <section id="section-header">
        <nav class="navbar bg-light fixed-top shadow">
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
                        <li class="nav-item">
                            <button id="btn-create-user" class="btn btn-primary" type="button">Buat User</button>  
                        </li>
                         <li class="nav-item py-3">
                                <form method="GET" action="admin_dashboard_itinerary.php">
                                    <button type="submit" class="btn btn-danger">Dashboard itinerary</button>
                                </form>
                        </li>
                        <li class="nav-item">
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
        <div class="container">

            <ul role="tablist" class="nav nav-tabs position-relative border-bottom-0 mt-5">
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-master-user" class="nav-link active">Akun</a></li>
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-master-report" class="nav-link">Laporan</a></li>
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-log-history" class="nav-link">Log</a></li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-master-user">
                    <div class="card" style="border-top-left-radius: 0;">
                        <div class="card-body">
                            <h2>Master Akun</h2>
                            <hr>
                            <div class="table-responsive">
                                <table class="table table-hover" id="table-user">
                                <thead>
                                    <tr>
                                        <th>ID Akun</th>
                                        <th>Username</th>
                                        <th>Role Akun</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($users as $user) { ?>
                                    <tr>
                                        <td><?= $user['emp_id']; ?></td>
                                        <td><?= $user['username']; ?></td>
                                        <td><?= ucfirst($user['role']); ?></td>
                                        <td class="py-1 cell-action">
                                            <div class="d-flex h-100 align-items-center justify-content-end">
                                                <button id="btn-<?= $user['id']; ?>" class="btn btn-primary btn-sm btn-show-action" type="button">
                                                    <i class="fas fa-bars"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            
                            <ul class="list-unstyled form-control" id="list-action" style="display: none;">
                                <li id="action-update">Perbaharui Data</li>
                                <li id="action-change-password">Ganti Password</li>
                                <li id="action-delete">Hapus Data</li>
                            </ul>

                            <div class="modal fade" role="dialog" tabindex="-1" id="modal-change-password">
                                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Ganti Password <span></span></h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="change_password.php" method="POST" id="form-change-password">
                                            <div class="modal-body">
                                                <div class="col-12 mb-3">
                                                    <label for="input-new-password" class="form-label mb-0 fw-bold">Password Baru</label>
                                                    <input type="password" class="form-control" id="input-new-password" name="input-password">
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label for="input-confirmation-password" class="form-label mb-0 fw-bold">Konfirmasi Password</label>
                                                    <input type="password" class="form-control" id="input-confirmation-password">
                                                </div>
                                            </div>
                                            <div class="modal-footer text-end"> 
                                                <input id="input-id" type="hidden" name="id">
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="tab-master-report">
                    <div class="card">
                        <div class="card-body">
                            <h2>Master Laporan</h2>
                             <form action="excel_export_admin.php" method="POST" name="export_file_type">
                                            <button type="submit" class="btn btn-primary" name="export_excel_admin" value="xls">Export EXCEL</button>
                                        </form>
                            <hr>
                            <table class="table table-hover" id="table-report">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>Tanggal Upload</th>
                                        <th>Pelapor</th>
                                        <th>Instansi</th>
                                        <th>Proyek</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $queryString = "SELECT r.id as 'id', r.location as 'location', r.upload_at as 'upload_at', r.project as 'project', u.emp_id as 'emp_id', r.status as 'status', u.username as 'username' FROM report r, user u WHERE r.report_by = u.id";

                                    try{
                                        $result = $conn->query($queryString);
                                    }
                                    catch(Exception $e){
                                        echo $e;
                                    }

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['upload_at'] ?></td>
                                        <td><?= $row['username'] ?></td>
                                        <td><?= $row['location'] ?></td>
                                        <td><?= $row['project'] ?></td>
                                        <td><?= parseReportStatus($row['status']) ?></td>
                                        <td class="py-1 text-end">
                                            <div class="d-flex justify-content-end">
                                                <form method="GET" action="detail_report.php">
                                                    <input type="hidden" name="item_id" value=<?= $row['id'] ?>>
                                                    <button class="btn btn-sm btn-outline-primary" type="submit">Details</button>
                                                </form>
                                                <form class="mb-0 ms-2" method="GET" action="delete_report.php">
                                                    <input type="hidden" name="item_id" value=<?= $row['id'] ?>>
                                                    <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="tab-log-history">
                    <div class="card">
                        <div class="card-body">
                            <h2>Log</h2>
                            <hr>
                            <div class="table-responsive">
                                 <table class="table table-hover" id="table-log">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jam</th>
                                        <th>Pengguna</th>
                                        <th>Aksi</th>
                                        <th>IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    $queryString = "SELECT l.timestamp as 'timestamp', u.username as 'username', l.action as 'action', l.ip as 'ip' FROM log l, user u WHERE l.account = u.id;"; //"SELECT DATE(l.timestamp) as 'date', TIME(l.timestamp) as 'time', u.username as 'username', l.action as 'action', l.ip as 'ip' FROM 'log' l, user u WHERE l.account = u.id";

                                    try{
                                        $result = $conn->query($queryString);
                                    }
                                    catch(Exception $e){
                                        echo $e;
                                    }

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= date("d-M-Y", strtotime($row['timestamp'])) ?></td>
                                        <td><?= date("H:i:s", strtotime($row['timestamp'])) ?></td>
                                        <td><?= $row['username'] ?></td><!-- harusnya username -->
                                        <td><?= $row['action'] ?></td>
                                        <td><?= $row['ip']?></td>
                                    </tr>
                                <?php } ?>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
        let tableUser = new DataTable('#table-user',  {
            columns: [
                null,
                null,
                null,
                { orderable: false }
            ]
        });

        let tableReport = new DataTable('#table-report',  {
            columns: [
                null,
                null,
                null,
                null,
                null,
                null,
                { orderable: false }
            ]
        });

        let tableLog = new DataTable('#table-log',  {
            order: [[ 0, 'desc' ], [ 1, 'desc' ]],
            columnDefs : [{
                targets: [0],
                render: function (data, type, row, meta) {
                    if (type === 'display' || type === 'filter') {
                        return moment(data).format('DD-MMM-YYYY');
                    }
                    return data;
                }
            }],
            columns: [
                null,
                null,
                null,
                null,
                { orderable: false }
            ]
        });

        var flag = false;
        var btnIndex = -1, userId = 0, username = '';
        $('#table-user').on('click', '.btn-show-action', function() {
            let lebarList = 150;
            let lebarBtn = $(this).css('width');
            let lebarTambahan = 2;
            lebarBtn = parseInt(lebarBtn.substr(0, lebarBtn.indexOf('px')));
            $('#list-action').css('left', $(this).offset().left - $(this).closest('.card').offset().left - lebarList + lebarBtn + lebarTambahan);

            let tinggiBtn = $(this).css('height');
            let tinggiHeader = 0;
            tinggiBtn = parseInt(tinggiBtn.substr(0, tinggiBtn.indexOf('px')));
            $('#list-action').css('top', $(this).offset().top - $(this).closest('.card').offset().top + tinggiBtn + tinggiHeader);

            $('#list-action').show();
            btnIndex = $(this).index('.btn-show-action') + 1;
            userId = $(this).attr('id').substring(4);
            username = $(this).closest('tr').children().eq(1).html();
            flag = true;
        });

        $(document).on('click', function() {
            setTimeout(function (){
                if (flag) {
                    flag = !flag;
                } else {
                    if ($('#list-action').css('display') == 'block') {
                        $('#list-action').hide();
                    }
                }
            }, 10);
        });

        $('#btn-create-user').on('click', function() {
            window.location = window.location.origin + '/samator/manage_user.php';
        });
        
        $('#action-update').on('click', function() {
            window.location = window.location.origin + '/samator/manage_user.php?item_id=' + userId;
        });

        $('#action-change-password').on('click', function() {
            $('#modal-change-password .modal-title span').text(username);
            $('#modal-change-password').modal('show');
        });

        $('#form-change-password').on('submit', function(e) {
            e.preventDefault();

            checkPasswordConfirmation();
            $('#input-id').val(userId);
            
            if ($('#form-change-password')[0].checkValidity()) {
                this.submit();
            } else {
                $('#form-change-password')[0].reportValidity();
            }
        });

        function checkPasswordConfirmation() {
            if ($('#input-new-password').val() != $('#input-confirmation-password').val()) {
                $('#input-confirmation-password')[0].setCustomValidity("Password tidak sama.");
            }
        }

        $('#action-delete').on('click', function() {
            if(confirm('Hapus data pengguna ?')) {
                window.location = window.location.origin + '/samator/delete_user.php?item_id=' + userId;
            }
        });
    });
</script>