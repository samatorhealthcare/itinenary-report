<?php 
    session_start();
    include "logged_user_check.php";
    include_once "db_conn.php";
    include_once "utilities/util.php";

    function updateReportAction($comment, $reportId, $conn){
        $queryString = "UPDATE report_action SET comment = '".$comment." WHERE id='".$_SESSION['ID']."' AND report_id = '".$reportId."' ";
        $conn->query($queryString);
    }

    if (isset($_GET['item_id'])){
        $queryString = "SELECT * FROM report WHERE ID=".$_GET['item_id']."";
        
        $result = $conn->query($queryString);

        $item = mysqli_fetch_assoc($result);

        $queryString = "SELECT r.action_at as action_at, r.comment as comment, u.username as username, u.role as role, u.emp_id as id FROM report_action r, user u where r.report_id=".$_GET['item_id']." and r.user = u.id ORDER BY action_at DESC";

        $result = $conn->query($queryString);

        $history = [];

        while($row = mysqli_fetch_assoc($result)){
            array_push($history, $row);
        }
    }
    else {
        echo "No item ID given";
    }

    if ($_SESSION['ROLE'] == "sales"){
        $backToPage = "user_dashboard.php";
        $canShowLocation = false;
    }
    elseif ($_SESSION['ROLE'] == "supervisor" || $_SESSION['ROLE'] == "manager" || $_SESSION['ROLE'] == "gmanager" || $_SESSION['ROLE'] == "director" ){
        $backToPage = "manager_dashboard.php";
        $canShowLocation = true;
    }
    elseif($_SESSION['ROLE'] == "admin"){
        $backToPage = "admin_dashboard.php";
        $canShowLocation = true;    
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Detail Report</title>
    <link rel="icon" href="asset/logo.png" type="image/x-icon">
    <!-- bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/934b5328b9.js" crossorigin="anonymous"></script>
    <!-- Google Maps API -->
    <script async src="https://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&callback=console.debug&libraries=maps,marker&v=beta"></script>

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

        #report-card input, #report-card textarea {
            pointer-events: none;
        }

        .input-number {
            outline: none;
            border: none;
        }
    </style>
</head>
<body class="bg-main">
    <section id="section-header">
        <nav class="navbar navbar-expand-lg bg-body-tertiary p-3 d-flex justify-content-between">
            <form method="GET" action="<?= $backToPage ?>">
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
            </form>
            <div class="d-flex flex-nowrap">
                <?php
                    if(($_SESSION['ROLE'] == "supervisor" || $_SESSION['ROLE'] == "manager" || $_SESSION['ROLE'] == "gmanager" || $_SESSION['ROLE'] == "director") 
                    && ($item['status'] >= 0 && $item['report_by'] != $_SESSION['ID'])) { 
                ?>  
                        <?php
                           if($_SESSION['ROLE'] == "manager" && $item['status_m'] == 1){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }  
                           else if($_SESSION['ROLE'] == "gmanager" && $item['status_gm'] == 2){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }
                           else if($_SESSION['ROLE'] == "supervisor" && $item['status_spv'] == 4){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }
                           else { ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report">Tanggapi</button>
                            <?php } ?> 
            </div>
                        
                        <div class="modal fade" role="dialog" tabindex="-1" id="modal-approve-report">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Tanggapan Laporan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="approve.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <textarea name="input-comment" class="form-control" id="input-comment" style="height: 150px;" required></textarea>
                                            <?php if(isset($_SESSION['ERROR']['input-comment'])) { ?>
                                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                    <?= $_SESSION['ERROR']['input-comment'] ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <input id="input-item-id" type="hidden" name="item_id" value="<?= $_GET['item_id']; ?>">
                                            <div class="d-flex flex-fill">
                                                <div class="me-2 w-50">
                                                    <button type="submit" name="reject" class="btn btn-outline-danger w-100">Reject</button>
                                                </div>
                                                <div class="ms-2 w-50">
                                                    <button type="submit" name="approve" class="btn btn-outline-primary w-100">Approve</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" role="dialog" tabindex="-1" id="modal-edit-comment">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Edit Tanggapan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="comment.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <textarea name="edit-comment" class="form-control" id="edit-comment" style="height: 150px;" required></textarea>
                                            <?php if(isset($_SESSION['ERROR']['edit-comment'])) { ?>
                                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                    <?= $_SESSION['ERROR']['edit-comment'] ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <div class="modal-footer">
                                            <input id="input-item-id" type="hidden" name="item_id" value="<?= $_GET['item_id']; ?>">
                                            <div class="d-flex flex-fill">
                                                <div class="ms-2 w-50">
                                                    <button type="submit" name="comment" class="btn btn-outline-primary w-100">Selesai</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    </div>
                <?php } ?>
                <form class="ms-2" method="GET" action="logout.php">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </nav>
        <div class="modal fade" role="dialog" tabindex="-1" id="modal-edit-attachment">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title">Edit Lampiran</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="form-edit-attachment" action="update_attachment.php" method="POST" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-sm-12 mb-3">
                                                    <label for="edit-attachment" class="form-label mb-0 fw-bold">Lampiran</label>
                                                    <input type="file" class="form-control" id="edit-attachment" name="edit-attachment[]" accept="image/png, image/jpg, image/jpeg, image/jfif" multiple>
                                                    <?php if(isset($_SESSION['ERROR']['edit-attachment'])) { ?>
                                                        <div class="alert alert-danger fade show" role="alert">
                                                            <?= $_SESSION['ERROR']['edit-attachment'] ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <div class="d-flex flex-fill">
                                                <div class="ms-2 w-50">
                                                    <input id="input-item-id" type="hidden" name="item_id" value="<?= $_GET['item_id']; ?>">
                                                    <button type="submit" class="btn btn-primary" id="btn-upload-attachment">Upload</button>
                                                </div>
                                            </div>
                                        </div>
                            </form>
                        </div>
                    </div>
            </div>

    </section>
    <section id="section-content">

        <div class="container my-5">
            <div class="row">
                <?php 
                    if (count($history) > 0){
                        echo "<div class='col-12 col-lg-8'>";
                    } else {
                        echo "<div class='col-12'>";
                    }
                ?>
                    <div class="card" id="report-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-0">
                                <h2 class="mb-0">Detail Report <?= $_GET['item_id']; ?></h2>
                                <div class="d-flex">
                                    <div class="p-2"><a href="test_export.php?item_id=<?= $_GET['item_id']; ?>">
                                            <button type="button" class="btn btn-primary">Export PDF</button></a>
                                    </div>
                                    <div class="p-2">
                                        <form action="excel_export.php?item_id=<?= $_GET['item_id']; ?>" method="POST" name="export_file_type">
                                            <button type="submit" class="btn btn-primary" name="export_excel_btn" value="xls">Export EXCEL</button>
                                        </form>
                                    </div>
                                    <?php
                                        if($_SESSION['ROLE'] == "admin"){ ?>
                                            <div class="p-2">
                                                <button id="btn-edit-attachment" type="button" data-bs-toggle="modal" class="btn btn-primary" data-bs-target="#modal-edit-attachment">Edit Lampiran</button></a>
                                            </div>
                                    <?php }  ?>
                                    
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                    <label for="input-location" class="form-label mb-0 fw-bold">Instansi</label>
                                    <input type="text" class="form-control" id="input-location" value="<?= $item['location'] ?>">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                    <label for="input-city" class="form-label mb-0 fw-bold">Kota Kunjungan</label>
                                    <input type="text" class="form-control" id="input-city" value="<?= $item['city'] ?>">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 mb-3 d-flex flex-column">
                                    <label class="form-label mb-0 fw-bold d-none d-sm-block invisible">Lokasi Pembuatan</label>
                                    <?php if ($canShowLocation && ($item['latitude'] != '' && $item['longitude'] != '')) {?>
                                        <a href="https://www.google.com/maps/search/?api=1&query=<?= $item['latitude'] . '%2C' . $item['longitude'] ?>" target="_blank">
                                            <button type="button" class="btn btn-outline-primary w-100 text-nowrap">Lokasi Pembuatan</button>
                                        </a>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-secondary w-100 text-nowrap" disabled>Lokasi Pembuatan</button>
                                    <?php } ?>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                    <label for="input-visit" class="form-label mb-0 fw-bold">Kunjungan Ke</label>
                                    <input type="text" class="form-control" id="input-visit" value="<?= $item['visit_number'] ?>">
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                    <label for="input-prospect" class="form-label mb-0 fw-bold">Nilai Prospek</label>
                                    <div class="form-control d-flex align-items-center justify-content-between">
                                        <p class="mb-0 me-3">Rp</p>
                                        <input type="text" class="input-number w-100" id="input-prospect" value="<?= number_format($item['prospect'], 0, ',', '.') ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                    <label for="input-opportunity" class="form-label mb-0 fw-bold">Peluang</label>
                                    <div class="form-control d-flex align-items-center justify-content-between">
                                        <input type="text" class="input-number w-100" id="input-opportunity" value="<?= $item['chance'] ?>">
                                        <p class="mb-0">%</p>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <label for="input-competitor" class="form-label mb-0 fw-bold">Pesaing / Kandidat Pesaing</label>
                                    <input type="text" class="form-control" id="input-competitor" value="<?= $item['competitor'] ?>">
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <label for="input-due" class="form-label mb-0 fw-bold">Dibutuhkan Kapan</label>
                                    <input type="date" class="form-control" id="input-due" value="<?= $item['deadline'] ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="input-note" class="form-label mb-0 fw-bold">Keterangan</label>
                                    <textarea name="note" class="form-control" id="input-note"><?= $item['note'] ?></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="input-sales-note" class="form-label mb-0 fw-bold">Komentar dari Staff</label>
                                    <textarea name="note" class="form-control" id="input-sales-note"><?= $item['sales_note'] ?></textarea>
                                </div>
                                <div class="col-12 mb-2">
                                    <label for="input-attachment" class="form-label mb-0 fw-bold">Lampiran</label>
                                    <div id="carousel-lampiran" class="carousel slide">
                                        <div class="carousel-inner bg-dark bg-opacity-10 rounded">
                                            <?php 
                                                $fileArr = explode(';', $item['attachment']);
                                                array_pop($fileArr);
                                                $ctr = 0;
                                                foreach($fileArr as $filepath) {
                                            ?>
                                                <div class="carousel-item<?= $ctr == 0 ? " active" : "" ?>">
                                                    <img src="<?= $filepath ?>" class="d-block w-100"  alt="" style="object-fit: scale-down; max-height: 450px; height: 450px;">
                                                </div>
                                            <?php
                                                    $ctr++;
                                                }
                                            ?>
                                        </div>
                                        <button class="carousel-control-prev" type="button" data-bs-target="#carousel-lampiran" data-bs-slide="prev">
                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Previous</span>
                                        </button>
                                        <button class="carousel-control-next" type="button" data-bs-target="#carousel-lampiran" data-bs-slide="next">
                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                            <span class="visually-hidden">Next</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 text-end">
                                    <p class="mb-0"><span class="text-muted">Tanggal pembuatan laporan:&nbsp;</span><?= (new DateTime($item['upload_at']))->format("d-M-Y") ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    if (count($history) > 0 || $_SESSION['role']=="sales"){
                ?>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h2>Tanggapan</h2>
                                
                                <?php 
                                    foreach ($history as $user) {
                                        if (fetchNumericRole($user['role']) <= fetchNumericRole($_SESSION['ROLE']) || (fetchNumericRole($user['role'])-1 === fetchNumericRole($_SESSION['ROLE'])) || (fetchNumericRole($user['role'])-2 === fetchNumericRole($_SESSION['ROLE'])) || (fetchNumericRole($user['role']) === fetchNumericRole($_SESSION['ROLE'])) || $user['id'] === $user_reports_to){
                                ?>
                                    <hr>
                                    <h4 class="fw-bold"><?= parseRoles($user['role']) . ' - ' . $user['username']; ?></h4>
                                    <h5>Tanggapan</h5>
                                    <textarea class="form-control" style="resize: none;" readonly><?= $user['comment']?></textarea>
                                    <?php 
                                        if($_SESSION['USERNAME'] == $user['username']){ ?>
                                            <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-edit-comment" >Edit Tanggapan</button>
                                       <?php }
                                       
                                    ?> 
                                    
                                     <p class="mb-0 fw-light text-end text-body-secondary">Terakhir di edit 
                                        <?= $user['action_last_update']?>
                                    </p>
                                    <p class="mb-0 fw-light text-end text-body-secondary">Riwayat <?= $user['action_at']?></p>
                                <?php    
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        
                    </div>
                <?php
                    }
                ?>
            </div>
        </div>
    </section>
</body>
</html>

<script>
    $('#btn-edit-attachment').on('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $('#input-latitude').val(position.coords.latitude);
                    $('#input-longitude').val(position.coords.longitude);
                });
            } else { 
                alert("Geolocation is not supported by this browser.");
            }
        });
    
    $('#btn-upload-attachment').on('click', function() {
            $('#form-edit-attachment').submit();
        });
</script>