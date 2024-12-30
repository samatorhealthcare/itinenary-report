<?php 
    session_start();
    // include "logged_user_check.php";
    include_once "db_conn.php";
    include_once "utilities/util.php";
    include_once "utilities/alert_handler.php";
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
        <nav class="navbar navbar-expand-lg bg-body-tertiary p-3 d-flex justify-content-between">
            <button id="btn-create-report" class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#modal-create-report">Buat Laporan</button>
            <div class="modal fade" role="dialog" tabindex="-1" id="modal-create-report">
                <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Buat Laporan</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="form-create-report" action="create_report.php" method="POST" enctype="multipart/form-data">
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
                                    <div class="col-12 mb-3">
                                        <label for="input-note" class="form-label mb-0 fw-bold">Keterangan</label>
                                        <textarea name="input-note" class="form-control" id="input-note"></textarea>
                                        <?php if(isset($_SESSION['ERROR']['input-note'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-note'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="input-sales-note" class="form-label mb-0 fw-bold">Komentar untuk Management</label>
                                        <textarea name="input-sales-note" class="form-control" id="input-sales-note"></textarea>
                                        <?php if(isset($_SESSION['ERROR']['input-sales-note'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-sales-note'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="input-attachment" class="form-label mb-0 fw-bold">Lampiran</label>
                                        <input type="file" class="form-control" id="input-attachment" name="input-attachment[]" accept="image/png, image/jpg, image/jpeg, image/jfif" multiple>
                                        <?php if(isset($_SESSION['ERROR']['input-attachment'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-attachment'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="input-latitude" id="input-latitude">
                            <input type="hidden" name="input-longitude" id="input-longitude">
                            <input type="hidden" name="input-project" id="input-project">
                            <div class="modal-footer text-end">
                                <button type="button" class="btn btn-primary" id="btn-save-report">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <h5 class="me-3 mb-0">Masuk sebagai, <?= $_SESSION['USERNAME'] ?> !</h5>
                <form method="GET" action="logout.php" class="mb-0">
                    <button type="submit" class="btn btn-danger">Keluar</button>
                </form>
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
                <div role="tabpanel" class="tab-pane" id="tab-evaluated-report">
                    <div class="card">
                        <div class="card-body">
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
    </section>
</body>

<script>
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