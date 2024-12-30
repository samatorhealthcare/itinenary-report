<?php 
    session_start();
    include_once "db_conn.php";
    include_once "utilities/validation_message_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "logged_user_check.php";

    $queryString = "SELECT * FROM user";

    $result = $conn->query($queryString);
    
    $users = [];
    while($temp = mysqli_fetch_assoc($result)){
        array_push($users, $temp);
    }   

    $result = $conn->query($queryString);
    $uuuser = "SELECT * FROM user WHERE id ='".$_SESSION['ID']."'";
    $report = $conn->query($uuuser);
    $user_login = mysqli_fetch_assoc($report);
        $report_user = "SELECT * FROM user WHERE id='".$user_login['reports_to']."'";
        $result_report = $conn->query($report_user);
        $reports_to = mysqli_fetch_assoc($result_report);

        $report_supervisor = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_1']."'";
        $result_s = $conn->query($report_supervisor);
        $reports_to_spv = mysqli_fetch_assoc($result_s);

        $report_manager = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_2']."'";
        $result_m = $conn->query($report_manager);
        $reports_to_manager = mysqli_fetch_assoc($result_m);

        $report_gmanager = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_3']."'";
        $result_gm = $conn->query($report_gmanager);
        $reports_to_gmanager = mysqli_fetch_assoc($result_gm);

        $report_director = "SELECT * FROM user WHERE id='".$user_login['reports_to_lead_4']."'";
        $result_dr = $conn->query($report_director);
        $reports_to_director = mysqli_fetch_assoc($result_dr);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
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
            <?php if($_SESSION['ROLE'] == "sales") { ?>
            <form method="GET" action="user_dashboard.php">
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
            </form>
            <?php } else { ?>
                 <form method="GET" action="manager_dashboard.php">
                    <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                </form>
            <?php } ?>
            <form method="GET" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
            
        </nav>
    </section>
    <section id="section-content">
        <div class="container my-5" style="max-width: 720px;">
            <div class="card">
                <div class="card-body">
                    <?php if(isset($user_login)) {?>
                        <h2>Perbaharui Data Atasan  <?= $user_login['emp_id']; ?></h2>
                        <form action="update_atasan.php" method="POST">
                    <?php } else { ?>
                        <h2>Input Atasan</h2>
                        <form action="create_atasan.php" method="POST">
                    <?php } ?>
                        <hr>
                        <div class="row">
                           
                            <label><i>Lapor ke Atasan</i><br></label>
                            
                            <div class="col-12 col-md-5">
                                <?php if($_SESSION['ROLE'] == "manager") { ?>
                                    <label for="3-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                    <select class="form-select" id="3-select-lapor-ke-role">
                                     <?php 
                                    if($user_login['reports_to_lead_3']!=0) { ?>
                                       
                                        <option value="gmanager" <?= $reports_to_gmanager['role'] == 'gmanager' ? 'selected' : ''; ?>>General Manager</option>
                                   
                                    <?php } else { ?>                                        
                                        <option value="" selected>-</option>
                                        <option value="gmanager">General Manager</option>

                                    <?php } ?>
                                    
                                   </select>
                                    <?php } elseif($_SESSION['ROLE'] == "gmanager") { ?>
                                        <label for="4-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                            <select class="form-select" id="4-select-lapor-ke-role">
                                                <?php 
                                                    if($user_login['reports_to_lead_4']!=0) { ?>
                                       
                                                    <option value="director" <?= $reports_to_director['role'] == 'director' ? 'selected' : ''; ?>>Director</option>
                                   
                                                <?php } else { ?>                                        
                                                    <option value="" selected>-</option>
                                                    <option value="director">Director</option>

                                                <?php } ?>
                                    
                                            </select>
                                        <?php } else { ?> 
                                            <label for="select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="select-lapor-ke-role">
                                                    <?php 
                                                        if($user_login['reports_to_lead_1']!=0) { ?>
                                                        <option value="supervisor" <?= $reports_to_spv['role'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                                    <?php } else { ?>                                        
                                                        <option value="" selected>-</option>
                                                        <option value="supervisor">Supervisor</option>
                                                    <?php } ?>
                                    
                                                 </select>
                                            <label for="2-select-lapor-ke-role" <?= $_SESSION['ROLE'] == "manager" ? "invisible" : "" ?> class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="2-select-lapor-ke-role">
                                                    <?php 
                                                        if($user_login['reports_to_lead_2']!=0) { ?>
                                                
                                                        <option value="manager" <?= $reports_to_manager['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                                            
                                                    <?php } else { ?>                                        
                                                        <option value="" selected>-</option>
                                                        <option value="manager">Manager</option>

                                                    <?php } ?>
                                                
                                                </select>
                                            <label for="3-select-lapor-ke-role" class="form-label mb-0 fw-bold">Jabatan Atasan</label>
                                                <select class="form-select" id="3-select-lapor-ke-role">
                                                    <?php 
                                                        if($user_login['reports_to_lead_3']!=0) { ?>
                                       
                                                        <option value="gmanager" <?= $reports_to_gmanager['role'] == 'gmanager' ? 'selected' : ''; ?>>General Manager</option>
                                   
                                                    <?php } else { ?>                                        
                                                        <option value="" selected>-</option>
                                                        <option value="gmanager">General Manager</option>

                                                    <?php } ?>
                                    
                                                </select>
                                                 <input type="hidden" name="input-id" value="<?= $user_login['id']; ?>">
                                    <?php } ?>
                               
                            </div>
                            <div class="col-12 col-md-7">
                                <?php if($_SESSION['ROLE'] == "manager") { ?>
                                    <label for="3-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                        <select name="input-reports-to-lead-3" class="form-select" id="3-select-lapor-ke">
                                            <?php if( $user['reports_to_lead_3'] == 0) { ?>
                                                <option value="" hidden disabled>-</option>
                                            <?php foreach($users as $temp) { ?>
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
                                    <?php } elseif($_SESSION['ROLE'] == "gmanager") { ?>
                                        <label for="4-select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                        <select name="input-reports-to-lead-4" class="form-select" id="4-select-lapor-ke">
                                            <?php if( $user['reports_to_lead_4'] == 0) { ?>
                                                <option value="" hidden disabled>-</option>
                                            <?php foreach($users as $temp) { ?>
                                            <?php if($reports_to_gmanager['id'] == $temp['reports_to_lead_4']) { ?>
                                                <option value="<?= $reports_to_gmanager['id']; ?>" class="<?="4-option-lapor-ke role-" . $reports_to_director['role'];?>" style="display: none;" selected><?= $reports_to_director['username']; ?></option>
                                                <?php } else { ?>
                                                <option value="<?= $temp['id']; ?>" class="<?="4-option-lapor-ke role-" . $temp['role'];?>" style="display: none;"><?= $temp['username']; ?></option>
                                                <option value="" hidden selected disabled>-</option>
                                            <?php } ?>
                                            <?php } ?>
                                        
                                            <?php } else{ ?>
                                                <option value="" hidden selected disabled>-</option>
                                            <?php } ?>
                                        </select>
                                        <?php } else { ?>
                                            <label for="select-lapor-ke" class="form-label mb-0 fw-bold">Nama</label>
                                                <select name="input-reports-to-lead-1" class="form-select" id="select-lapor-ke">
                                                    <?php if($user['reports_to_lead_1'] == 0) { ?>
                                                        <option value="" hidden disabled>-</option>
                                                        <?php foreach($users as $temp) { ?>
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
                                                        <?php foreach($users as $temp) { ?>
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
                                                        <?php foreach($users as $temp) { ?>
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

                                        <?php } ?>
                            
                               
                                 <?php if(isset($_SESSION['ERROR']['input-reports-to'])) { ?>
                                    <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                        <?= $_SESSION['ERROR']['input-reports-to'] ?>
                                    </div>
                                <?php } ?>
                            
                        </div>
                        <hr>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="submit">Selesai</button>
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
      
    $(document).ready(function() {
        if ($('#2-select-lapor-ke').val() != '') {
            $('.role-' + $('#2-select-lapor-ke-role').val()).show();
        }

        $('#2-select-lapor-ke-role').on('change', function() {
            $('#2-select-lapor-ke').val('');
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





</script>

<?php 
    unsetAlert();
?>