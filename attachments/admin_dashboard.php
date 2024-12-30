<?php 
    session_start();
    include "db_conn.php";
    include "utilities/alert_handler.php";
    include "utilities/util.php";

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
        <nav class="navbar navbar-expand-lg bg-body-tertiary p-3 d-flex justify-content-between">
            <button id="btn-create-user" class="btn btn-primary" type="button">Buat User</button>
            <form method="GET" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
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
                            <table class="table table-hover" id="table-user">
                                <thead>
                                    <tr>
                                        <th>ID Akun</th>
                                        <th>Username</th>
                                        <th>Role Akun</th>
                                        <th>Lapor Ke</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($users as $user) { ?>
                                    <tr>
                                        <td><?= $user['emp_id']; ?></td>
                                        <td><?= $user['username']; ?></td>
                                        <td><?= ucfirst($user['role']); ?></td>
                                        <td><?= $user['reports_to'] != 0 ? getUser($users, $user['reports_to'])['username'] : '-'; ?></td>
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
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="tab-master-report">
                    <div class="card">
                        <div class="card-body">
                            <h2>Master Laporan</h2>
                            <hr>
                            <table class="table table-hover" id="table-report">
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
                                    $queryString = "SELECT r.id as 'id', r.location as 'location', r.upload_at as 'upload_at', u.emp_id as 'emp_id', r.status as 'status' FROM report r, user u WHERE r.report_by = u.id";

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
                                        <td><?= $row['emp_id'] ?></td>
                                        <td><?= $row['location'] ?></td>
                                        <td><?= parseReportStatus($row['status']) ?></td>
                                        <td class="py-1 text-end">
                                            <form method="GET" action="detail_report.php">
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
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane" id="tab-log-history">
                    <div class="card">
                        <div class="card-body">
                            <h2>Log</h2>
                            <hr>
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
                                    $queryString = "SELECT * FROM log"; //"SELECT DATE(l.timestamp) as 'date', TIME(l.timestamp) as 'time', u.username as 'username', l.action as 'action', l.ip as 'ip' FROM 'log' l, user u WHERE l.account = u.id";

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
                                        <td><?= $row['account'] ?></td><!-- harusnya username -->
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
    </section>
</body>
</html>

<script>
    $(document).ready(function() {
        let tableUser = new DataTable('#table-user',  {
            columns: [
                null,
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
                { orderable: false }
            ]
        });

        let tableLog = new DataTable('#table-log',  {
            columnDefs : [{ targets: 0, type:"date-eu" }],
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
        $('.btn-show-action').on('click', function() {
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
            window.location = window.location.origin + '/manage_user.php';
        });
        
        $('#action-update').on('click', function() {
            window.location = window.location.origin + '/manage_user.php?item_id=' + userId;
        });

        $('#action-change-password').on('click', function() {
            $('#modal-change-password .modal-title span').text(username);
            $('#')
            $('#modal-change-password').modal('show');
        });

        $('#form-change-password').on('submit', function(e) {
            e.preventDefault();

            checkPasswordConfirmation();

            let formData = new FormData();
            formData.append('id', userId);
            formData.append('password', $('#input-new-password').val());
            
            if ($('#form-change-password')[0].checkValidity()) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    type: "post",
                    url: $('#form-change-password').attr('action'),
                    contentType: false,
                    processData: false,
                    data: formData,
                    success: function(res) {
                        window.location.reload();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log(xhr.status);
                        console.log(thrownError);
                    }
                });
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
                window.location = window.location.origin + '/delete_user.php?item_id=' + userId;
            }
        });
    });
</script>