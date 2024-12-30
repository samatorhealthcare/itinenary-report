<?php 
    session_start();
    include_once "db_conn_itinerary.php";
    include_once "utilities/util.php";
    include_once "utilities/alert_handler.php";
    include_once "logged_user_check.php";

    // $queryString = "SELECT * FROM user";
    
    // $result = $connDB->query($queryString);
    // $uuuser = "SELECT * FROM user WHERE id ='".$_SESSION['ID']."'";
    // $report = $connDB->query($uuuser);
    // $user_login = mysqli_fetch_assoc($report);
    //     $report_user = "SELECT * FROM user WHERE id='".$user_login['reports_to']."'";
    //     $result_report = $connDB->query($report_user);
    //     $reports_to = mysqli_fetch_assoc($result_report);

    //     $report_supervisor = "SELECT * FROM user WHERE role='supervisor'";
    //     $result_s = $connDB->query($report_supervisor);
    //     $reports_to_spv = mysqli_fetch_assoc($result_s);

    //     $report_manager = "SELECT * FROM user WHERE role='manager'";
    //     $result_m = $connDB->query($report_manager);
    //     $reports_to_manager = mysqli_fetch_assoc($result_m);

    //     $report_gmanager = "SELECT * FROM user WHERE role='gmanager'";
    //     $result_gm = $connDB->query($report_gmanager);
    //     $reports_to_gmanager = mysqli_fetch_assoc($result_gm);

    //     $report_director = "SELECT * FROM user WHERE role='director'";
    //     $result_dr = $connDB->query($report_director);
    //     $reports_to_director = mysqli_fetch_assoc($result_dr);

    //     $report_director_2 = "SELECT * FROM user WHERE role='director'";
    //     $result_dr_2 = $connDB->query($report_director_2);
    //     $reports_to_director_2 = mysqli_fetch_assoc($result_dr_2);
    
    //     $statusQuery = "SELECT * FROM full_report";
    //     $status = $connDB->query($statusQuery);
    
    // $users = [];
    // $spv = [];
    // $manager = [];
    // $gmanager = [];
    // $director = [];
    // $director_2 = [];


    // while($temp = mysqli_fetch_assoc($result_s)){
    //     array_push($spv, $temp);
    // }   
    // while($temp = mysqli_fetch_assoc($result_m)){
    //     array_push($manager, $temp);
    // }  
    // while($temp = mysqli_fetch_assoc($result_gm)){
    //     array_push($gmanager, $temp);
    // } 
    // while($temp = mysqli_fetch_assoc($result_dr)){
    //     array_push($director, $temp);
    // } 
    // while($temp = mysqli_fetch_assoc($result_dr_2)){
    //     array_push($director_2, $temp);
    // } 
    
    
    
    // if(isset($_GET['item_id'])){
    //     $queryString = "SELECT U.id as id, U.emp_id as emp_id, U.username as username, U.password as password, U.role as role, U.reports_to as reports_to, U.reports_to_lead_1 as reports_to_supervisor, U.reports_to_lead_2 as reports_to_manager, U.reports_to_lead_3 as reports_to_gmanager, U.reports_to_lead_4 as reports_to_director, REP.role as reports_to_role FROM user U, user REP WHERE U.ID=".$_GET['item_id']." AND U.REPORTS_TO = REP.id";
    //     $result = $connDB->query($queryString);
            
            
    //     $user = mysqli_fetch_assoc($result);
        
    //     if ($user == null){
    //         $queryString = "SELECT * from user WHERE ID=".$_GET['item_id']."";
    //         $result = $connDB->query($queryString);
    //         $user = mysqli_fetch_assoc($result);
    //     }
    // }
    
    if (isset($_GET['item_id'])){
        $queryString = "SELECT * FROM full_report WHERE ID=".$_GET['item_id']."";
        
        $result = $connDB->query($queryString);

        $item = mysqli_fetch_assoc($result);

        $queryString = "SELECT r.action_at as action_at, r.comment as comment, u.username as username, u.role as role, u.emp_id as id FROM report_action r, user u where r.report_id=".$_GET['item_id']." and r.user = u.id ORDER BY action_at DESC";

        $result = $connDB->query($queryString);

        $history = [];

        while($row = mysqli_fetch_assoc($result)){
            array_push($history, $row);
        }
    }
    else {
        echo "No item ID given";
    }

    
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
        <div class="position-absolute end-0 me-3" style="margin-top: 40px;">
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
                                <!-- <button id="btn-create-report" class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-create-report">Buat Itenenary</button>   -->
                                <form method="GET" action="manage_itinerary_report.php">
                                    <button class="btn btn-primary" type="submit" >Buat itinerary</button>
                                </form>
                                
                            </li>
                            <li class="nav-item py-3">
                                <form method="GET" action="user_dashboard.php">
                                    <button type="submit" class="btn btn-info text-white">Report Dashboard</button>
                                </form>
                            </li>
                            <li class="nav-item py-1">
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
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-unapproved-report" class="nav-link active">Estimasi</a></li>
                <li role="presentation" class="nav-item"><a role="tab" data-bs-toggle="tab" href="#tab-evaluated-report" class="nav-link">Realisasi</a></li>
            </ul>
        
            
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-unapproved-report">
                    <div class="card" style="border-top-left-radius: 0;">
                        <div class="card-body">
                            <form action="excel_itinerary.php?item_id=<? $_GET['item_id']; ?>" method="POST" name="export_file_type">
                                            <button type="submit" class="btn btn-primary" name="export_excel_itinerary" value="xls">Export EXCEL</button>
                                        </form>
                            <div class="table-responsive">
                                <table class="table table-hover" id="table-unapproved-report">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>No SPPD</th>
                                        <th>Tanggal Upload</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th>Jenis Laporan</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    //$queryString = "SELECT * FROM full_report WHERE report_by = '".$_SESSION['ID']."'";
                                    $queryString = "SELECT * 
                                                    FROM full_report
                                                    WHERE report_by =  " . $_SESSION['ID'] . "
                                                    GROUP BY sppd";
                                    
                                    $result = $connDB->query($queryString);

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['sppd'] ?></td>
                                        <td><?= date('d-M-Y', strtotime($row['upload_at'])) ?></td>
                                        <td><?= $row['instansi'] ?></td>
                                        <td><?= parseReportStatus($row['status']) ?></td>
                                        <td><?= parseJenisLaporan($row['jenis_laporan']) ?></td>
                                        
                                        <td class="py-1">
                                            <div class="d-flex justify-content-end">
                                                <?php if($row['jenis_laporan'] == 1 && $row['status'] <= 0) { ?>
                                                    <form class="mb-0 ms-2" method="GET" action="edit_estimasi_itinerary.php">
                                                        <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                        <button class="btn btn-sm btn-outline-primary" type="submit">Edit</button>
                                                    </form> 
                                                <?php } else { ?>
                                                     <form class="mb-0 ms-2" method="GET" action="edit_realisasi_itinerary.php">
                                                        <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                        <button class="btn btn-sm btn-outline-primary" type="submit">Edit</button>
                                                    </form> 
                                                <?php } ?>
                                                <?php if($row['jenis_laporan'] == 1) { ?>
                                                    <form class="mb-0 ms-2" method="GET" action="detail_report_itinerary.php">
                                                        <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                        <button class="btn btn-sm btn-outline-info" type="submit">Preview</button>
                                                    </form>
                                                <?php } else { ?>
                                                    <form class="mb-0 ms-2" method="GET" action="detail_realisasi_itinerary.php">
                                                        <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                        <button class="btn btn-sm btn-outline-info" type="submit">Preview</button>
                                                    </form>
                                                <?php } ?>
                                                <form class="mb-0 ms-2" method="GET" action="manage_report_itinerary_realisasi.php">
                                                    <input type="hidden" name="item_id" value=<?=$row['id']?>>
                                                    <?php if($_SESSION['ID'] != 165 || $row['status_estimasi_dir'] > 0 || $row['status_estimasi_dir_2'] > 0) { 
                                                       ?> <button class="btn btn-sm btn-outline-secondary" type="submit">Realisasi</button> 
                                                    <?php } else { ?>
                                                      <?php  ?> <button disabled class="btn btn-sm btn-outline-secondary" type="submit">Realisasi</button> 
                                                    <?php } ?>
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
                                        <th>SPPD</th>
                                        <th>Tanggal Upload</th>
                                        <th>Instansi</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $queryString = "SELECT * FROM report WHERE report_by = '".$_SESSION['ID']."' AND STATUS <> 0";
                                    
                                    $result = $connDB->query($queryString);

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['sppd'] ?></td>
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
        let tableUnapprovedReport = new DataTable('#table-unapproved-report',  {
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

         $('#btn-create-activity').on('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $('#input-latitude').val(position.coords.latitude);
                    $('#input-longitude').val(position.coords.longitude);
                });
            } else { 
                alert("Geolocation is not supported by this browser.");
            }
        });
        $(window).on('shown.bs.modal', function() { 
            $('#modal-create-activity').modal('show');
            alert('shown');
        });

        $('#btn-save-report-itenenary').on('click', function() {
            let projects = '';
            let type_activities = '';

            $('.form-check-type-activity').each(function(index, element) {
                if ($(element).is(':checked')) {
                    if ($(element).next().is('LABEL')) {
                        type_activities += $(element).next().text();
                    }
                    else {
                        type_activities += $(element).next().val();
                    }
                    type_activities += ';';
                }
            });

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

            $('#input-type-activity').val(type_activities);
            $('#input-project').val(projects);
            $('#form-create-report-itenenary').submit();
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

     reportId = 0;
        $('#table-unapproved-report').on('click', '.btn-show-action', function() {
            reportId = $(this).attr('id').substring(4);
        });


        $('#action-update').on('click', function() {
            window.location = window.location.origin + '/samator/edit_itenenary_report.php?item_id=' + reportId;
        });

         $(document).ready( function () {
  var table = $('#example').DataTable({
    dom: 'Bftrip',
    buttons: [
      {
        extend: 'pdf',
        
        exportOptions: {
          modifier: {
            page: 'current'
          }
        }
      }
    ]
  });
} );
</script>
