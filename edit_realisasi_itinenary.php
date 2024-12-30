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
        $queryString = "SELECT * from full_report WHERE ID=".$_GET['item_id']."";
        $result = $connDB->query($queryString);
            
            
        $report = mysqli_fetch_assoc($result);
        
        if ($report == null){
            $queryString = "SELECT * from full_report WHERE ID=".$_GET['item_id']."";
            $result = $connDB->query($queryString);
            $report = mysqli_fetch_assoc($result);
        }
    }
    ?>




<html>
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
                        ?>  <form method="GET" action="user_dashboard_itinerary.php">
                                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                            </form>
                    <?php } else {
                        ?> <form method="GET" action="manager_dashboard_itinerary.php">
                                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                            </form>
                <?php } 
                ?>
                <form method="GET" action="logout.php">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </nav>
        </section>
        <div class="container">
            <div class="card mt-5 mb-5">
                <div class="card-header fw-bold h3">
                    Edit itinerary
                </div>
                <form id="dynamic-form" method="POST" action="update_itinerary_report.php">
                <div class="card-body">
                      
                   <div class="col-12 mb-3">
                                        <label class="fw-bold">Pilih salah satu</label>
                                        <div class="row">
                                            <div class="form-group">

                                                <select name="input-type-activity" id="input-type-activity" class="form-select">
                                                    <option value="Dinas Luar Kota">Dinas Luar Kota</option>
                                                    <option value="Kunjungan">Kunjungan</option>
                                                </select>
                                            </div>
                                        </div>
                                    
                        </div>
                                    <div class="col-12 mb-3">
                                        <label for="input-sppd" class="form-label mb-0 fw-bold">No SPPD</label>
                                        <input type="text" class="form-control" id="input-sppd" name="input-sppd">
                                        <?php if(isset($_SESSION['ERROR']['input-sppd'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-sppd'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label for="input-duration" class="form-label mb-0 fw-bold">Durasi (Hari)</label>
                                        <input type="number" min="0" class="form-control" id="input-duration" name="input-duration" step="1" label="Hari" required>
                                        <?php if(isset($_SESSION['ERROR']['input-duration'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-duration'] ?>
                                            </div>
                                            
                                        <?php } ?>
                                    </div> 
                  
                    
                        <div class="row">
                            <div class="col">
                                <label class="form-label mb-0 fw-bold h4 ">Aktivitas</label>
                                <button onclick="add_more()" class="btn btn-dark btn-sm">Add More</button>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                                        <label class="fw-bold">Proyek</label>
                                        <div class="row">
                                            <div class="form-group">
                                                <label for="dropdown">Choose an option:</label>
                                                <select name="input-project[]" id="input-project" class="form-select">
                                                <option value="Alkes Radiologi">Alkes Radiologi</option>
                                                <option value="Alkes Non Radiologi">Alkes Non Radiologi</option>
                                                <option value="IGM">IGM</option>
                                                <option value="PTS">PTS</option>
                                                <option value="MOT">MOT</option>
                                                <option value="Oxycan">Oxycan</option>
                                                <option value="Sippol - Personal Hygiene">
                                                    Sippol - Personal Hygiene
                                                </option>
                                                <option value="BMHP-Drymist">BMHP-Drymist</option>
                                                <option value="Food Chemical">Food Chemical</option>
                                                </select>
                                            </div>
                                        </div>
                                    
                        </div>
                        
                        <div id="product_row1" class="row">
                            <!-- <input type="text" name="input-project[]" placeholder="Project" required /> -->
                            <div class="col-12 mb-3">
                                <label for="input-tanggal-aktivitas" class="form-label mb-0 fw-bold">Tanggal Aktivitas</label>
                                <input type="date" class="form-control" id="input-tanggal-aktivitas" name="input-tanggal-aktivitas">
                                <?php if(isset($_SESSION['ERROR']['input-tanggal-aktivitas'])) { ?>
                                    <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                        <?= $_SESSION['ERROR']['input-tanggal-aktivitas'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="input-kota" class="form-label mb-0 fw-bold">Kota Kunjungan</label>
                                    <input type="text" class="form-control" id="input-kota${counter}" name="input-kota[]" value="<?= $report['kota']?>">
                                    <?php if(isset($_SESSION['ERROR']['input-kota'])) { ?>
                                        <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                            <?= $_SESSION['ERROR']['input-kota'] ?>
                                        </div>
                                    <?php } ?>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="input-instansi" class="form-label mb-0 fw-bold">Instansi</label>
                                <input type="text" class="form-control" id="input-instansi" name="input-instansi[]" value="<?= $report['instansi']?>"> 
                                <?php if(isset($_SESSION['ERROR']['input-instansi'])) { ?>
                                    <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                        <?= $_SESSION['ERROR']['input-instansi'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="input-kode-proyek" class="form-label mb-0 fw-bold">Kode Proyek</label>
                                <input type="text" class="form-control" id="input-kode-proyek" name="input-kode-proyek[]" value="<?= $report['kode_proyek']?>">
                                <?php if(isset($_SESSION['ERROR']['input-kode-proyek'])) { ?>
                                    <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                        <?= $_SESSION['ERROR']['input-kode-proyek'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-6 mb-3">
                                <label for="input-nama-proyek" class="form-label mb-0 fw-bold">Nama Proyek</label>
                                <input type="text" class="form-control" id="input-nama-proyek" name="input-nama-proyek[]" value="<?= $report['nama_proyek']?>">
                                <?php if(isset($_SESSION['ERROR']['input-nama-proyek'])) { ?>
                                    <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                        <?= $_SESSION['ERROR']['input-nama-proyek'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="col-6 mb-3">
                                        <label for="input-progress" class="form-label mb-0 fw-bold">Progres Saat Ini</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <input type="number" class="input-number w-100" id="input-progress" name="input-progress[]" max="100" min="0" value="<?= $report['progress']?>">
                                            <p class="mb-0">%</p>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-progress'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-progress'] ?>
                                            </div>
                                        <?php } ?>
                            </div>
                             <div class="col-6 mb-3">
                                        <label for="input-target" class="form-label mb-0 fw-bold">Target</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <input type="number" class="input-number w-100" id="input-target" name="input-target[]" max="100" min="0" value="<?= $report['target']?>">
                                            <p class="mb-0"> %</p>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-target'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-target'] ?>
                                            </div>
                                        <?php } ?>
                            </div>
                            <div class="col-sm-12 mb-3">
                                        <label for="input-kegiatan" class="form-label mb-0 fw-bold">Kegiatan</label>
                                        <textarea name="input-kegiatan[]" class="form-control" id="input-kegiatan"></textarea>
                                        <?php if(isset($_SESSION['ERROR']['input-kegiatan'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-kegiatan'] ?>
                                            </div>
                                        <?php } ?>
                            </div>
                            <div class="col">
                                <button onclick="delete_row('1')" type="button" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    
            </div>
             <div class="card-footer text-end">
                <button type="submit" class="btn btn-info" id="btn-save-report-itenenary">Submit</button>
             </div>
        </form>
    </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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




    var counter = 1;
    var activity = 1;
    function add_more() {
    counter++
    activity++
    var newDiv = `<div id="product_row${counter}" class="row">
                    <label class="form-label mb-0 fw-bold h4 mt-2">Aktivitas ${activity}</label><br>
                    <div class="col-12 mb-3">
                                        <label class="fw-bold">Proyek</label>
                                        <div class="row">
                                             <div class="form-group">
                                                <select name="input-project[]" id="input-project" class="form-select">
                                                    <option value="Alkes Radiologi">Alkes Radiologi</option>
                                                    <option value="Alkes Non Radiologi">Alkes Non Radiologi</option>
                                                    <option value="IGM">IGM</option>
                                                    <option value="PTS">PTS</option>
                                                    <option value="MOT">MOT</option>
                                                    <option value="Oxycan">Oxycan</option>
                                                    <option value="Sippol - Personal Hygiene">
                                                        Sippol - Personal Hygiene
                                                    </option>
                                                    <option value="BMHP-Drymist">BMHP-Drymist</option>
                                                    <option value="Food Chemical">Food Chemical</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-proyek'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-proyek'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                    <div class="col-12 mb-3">
                        <label for="input-tanggal-aktivitas" class="form-label mb-0 fw-bold">Tanggal Aktivitas</label>
                            <input type="date" class="form-control" id="input-tanggal-aktivitas${counter}" name="input-tanggal-aktivitas[]">
                            <?php if(isset($_SESSION['ERROR']['input-tanggal-aktivitas'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-tanggal-aktivitas'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-6 mb-3">
                        <label for="input-kota" class="form-label mb-0 fw-bold">Kota Kunjungan</label>
                            <input type="text" class="form-control" id="input-kota${counter}" name="input-kota[]">
                            <?php if(isset($_SESSION['ERROR']['input-kota'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-kota'] ?>
                                </div>
                            <?php } ?>
                    </div>
                     <div class="col-6 mb-3">
                            <label for="input-instansi" class="form-label mb-0 fw-bold">Instansi</label>
                            <input type="text" class="form-control" id="input-instansi${counter}" name="input-instansi[]" required>
                            <?php if(isset($_SESSION['ERROR']['input-instansi'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-instansi'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-6 mb-3">
                            <label for="input-kode-proyek" class="form-label mb-0 fw-bold">Kode Proyek</label>
                            <input type="text" class="form-control" id="input-kode-proyek${counter}" name="input-kode-proyek[]" required>
                            <?php if(isset($_SESSION['ERROR']['input-kode-proyek'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-kode-proyek'] ?>
                                </div>
                            <?php } ?>
                    </div>
                     <div class="col-6 mb-3">
                            <label for="input-nama-proyek" class="form-label mb-0 fw-bold">Nama Proyek</label>
                            <input type="text" class="form-control" id="input-nama-proyek${counter}" name="input-nama-proyek[]" required>
                            <?php if(isset($_SESSION['ERROR']['input-nama-proyek'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-nama-proyek'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-6 mb-3">
                                        <label for="input-progress" class="form-label mb-0 fw-bold">Progres Saat Ini</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <input type="number" class="input-number w-100" id="input-progress${counter}" name="input-progress[]" max="100" min="0">
                                            <p class="mb-0">%</p>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-progress'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-progress'] ?>
                                            </div>
                                        <?php } ?>
                            </div>
                             <div class="col-6 mb-3">
                                        <label for="input-target" class="form-label mb-0 fw-bold">Target</label>
                                        <div class="form-control d-flex align-items-center justify-content-between">
                                            <input type="number" class="input-number w-100" id="input-target${counter}" name="input-target[]" max="100" min="0">
                                            <p class="mb-0"> %</p>
                                        </div>
                                        <?php if(isset($_SESSION['ERROR']['input-target'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-target'] ?>
                                            </div>
                                        <?php } ?>
                            </div>
                            <div class="col-sm-12 mb-3">
                                        <label for="input-kegiatan" class="form-label mb-0 fw-bold">Kegiatan</label>
                                        <textarea name="input-kegiatan[]" class="form-control" id="input-kegiatan${counter}"></textarea>
                                        <?php if(isset($_SESSION['ERROR']['input-kegiatan'])) { ?>
                                            <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                                <?= $_SESSION['ERROR']['input-kegiatan'] ?>
                                            </div>
                                        <?php } ?>
                            </div>
                     <div class="col">
                        <button onclick="add_more(), remove(this)" class="btn btn-dark">Add More</button>
                        <button onclick="delete_row('${counter}')" type="button" class="btn btn-danger">Delete</button>
                    </div>
                </div>`
    var form = document.getElementById('dynamic-form')
    form.insertAdjacentHTML('beforeend', newDiv);
}

function remove(el) {
  var element = el;
  element.remove();
}

function delete_row(id) {
    activity--;
    document.getElementById('product_row'+id).remove()
}


    //    $('#btn-save-report-itenenary').on('click', function() {
    //         let projects = [];
    //         let type_activities = [];

    //         $('.form-check-type-activity').each(function(index, element) {
    //             if ($(element).is(':checked')) {
    //                 let value = $(element).next().is('LABEL') ? $(element).next().text() : $(element).next().val();
    //                 type_activities.push(value);
    //             }
    //         });

    //         $('.form-check-input').each(function(index, element) {
    //             if ($(element).is(':checked')) {
    //                 let value = $(element).next().is('LABEL') ? $(element).next().text() : $(element).next().val();
    //                 projects.push(value);
    //             }
    //         });

    //         // Assigning the arrays to hidden input fields to send them with the form
    //         // $('<input>').attr({
    //         //     type: 'hidden',
    //         //     name: 'projects',
    //         //     value: JSON.stringify(projects) // Convert array to JSON string
    //         // }).appendTo('form');

    //         // $('<input>').attr({
    //         //     type: 'hidden',
    //         //     name: 'type_activities',
    //         //     value: JSON.stringify(type_activities) // Convert array to JSON string
    //         // }).appendTo('form');
            
    //         $('#input-project').val(projects);
    //         // Submit the form after processing
    //         //$('#dynamic-form').submit();
    //     });


        // $('#btn-save-report-itenenary').on('click', function() {
        //     let projects = '';
        //     let type_activities = '';

        //     $('.form-check-type-activity').each(function(index, element) {
        //         if ($(element).is(':checked')) {
        //             if ($(element).next().is('LABEL')) {
        //                 type_activities += $(element).next().text();
        //             }
        //             else {
        //                 type_activities += $(element).next().val();
        //             }
        //             type_activities += ';';
        //         }
        //     });

        //     $('.form-check-input').each(function(index, element) {
        //         if ($(element).is(':checked')) {
        //             if ($(element).next().is('LABEL')) {
        //                 projects += $(element).next().text();
        //             }
        //             else {
        //                 projects += $(element).next().val();
        //             }
        //             projects += ';';
        //         }
        //     });

        //     // $('#input-type-activity').val(type_activities);
        //     $('#input-project').val(projects);
        //     // $('#form-create-report-itenenary').submit();
        // });

</script>