<?php 
    session_start();
    include "logged_user_check.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/util.php";
    include_once "utilities/alert_handler.php";

    function updateReportAction($comment, $reportId, $connDB){
        $queryString = "UPDATE report_action SET comment = '".$comment." WHERE id='".$_SESSION['ID']."' AND report_id = '".$reportId."' ";
        $connDB->query($queryString);
    }


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

    if ($_SESSION['ROLE'] == "sales"){
        $backToPage = "user_dashboard_itinerary.php";
        $canShowLocation = false;
    }
    elseif ($_SESSION['ROLE'] == "supervisor" || $_SESSION['ROLE'] == "manager" || $_SESSION['ROLE'] == "gmanager" || $_SESSION['ROLE'] == "director" ){
        $backToPage = "manager_dashboard_itinerary.php";
        $canShowLocation = true;
    }
    elseif($_SESSION['ROLE'] == "admin"){
        $backToPage = "admin_dashboard_itinerary.php";
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
                    <div>
                        <?php
                           if($_SESSION['ROLE'] == "manager" && $item['status_estimasi_m'] > 0){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }  
                           else if($_SESSION['ROLE'] == "gmanager" && $item['status_estimasi_gm'] > 0){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }
                           else if($_SESSION['ROLE'] == "supervisor" && $item['status_estimasi_spv'] > 0){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }
                           else if($_SESSION['ROLE'] == "director" && $item['status_estimasi_dir'] > 0 || $item['status_estimasi_dir_2'] > 0){ ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report" disabled>Tanggapi</button>
                           <?php }
                           else { ?>
                                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#modal-approve-report">Tanggapi</button>
                            <?php } ?> 
            
                        
                        <div class="modal fade" role="dialog" tabindex="-1" id="modal-approve-report">
                            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Tanggapan Laporan</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="approve_itinerary.php" method="POST" enctype="multipart/form-data">
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
                                    <form action="comment_itinerary.php" method="POST" enctype="multipart/form-data">
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
                    $query = "SELECT * FROM full_report WHERE id = ".$_GET['item_id']."";
                    $result_q = $connDB->query($query);
                    $res = mysqli_fetch_assoc($result_q);
                ?>
                    <div class="card" id="report-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-0">
                                <h2 class="mb-0">Detail <?= $res['sppd']; ?></h2>
                                <div class="d-flex">
                                    <div class="p-2"><a href="test_export.php?item_id=<?= $_GET['item_id']; ?>">
                                            <button type="button" class="btn btn-primary">Export PDF</button></a>
                                    </div>
                                    <div class="p-2">
                                        <form action="excel_itinerary.php?item_id=<?= $_GET['item_id']; ?>" method="POST" name="export_file_type">
                                            <button type="submit" class="btn btn-primary" name="export_excel_itinerary" value="xls">Export EXCEL</button>
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
                            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="tab-unapproved-report">
                    <div class="card" style="border-top-left-radius: 0;">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="example">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal Upload</th>
                                        <th>Proyek</th>
                                        <th>Instansi</th>
                                        <th>Kode Proyek</th>
                                        <th>Kota Kunjungan</th>
                                        <th rowspan="2">&emsp;&emsp;Estimasi <br>Progress&emsp;Target</th>
                                        
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                <?php 
                                  // Define the base query and column for need_approval_by based on the role
                                    $needApprovalColumn = '';

                                    switch ($_SESSION['ROLE']) {
                                        case 'supervisor':
                                            $needApprovalColumn = 'need_approval_by';
                                            break;
                                        case 'manager':
                                            $needApprovalColumn = 'need_approval_by_2';
                                            break;
                                        case 'gmanager':
                                            $needApprovalColumn = 'need_approval_by_3';
                                            break;
                                        case 'director':
                                            $needApprovalColumn = 'need_approval_by_4';
                                            break;
                                        case 'admin':
                                            $queryString = "
                                                            SELECT * 
                                                            FROM full_report
                                                            WHERE upload_at = (
                                                                SELECT upload_at
                                                                FROM full_report
                                                                WHERE id = ".$_GET['item_id']."
                                                            )";
                                        break;
                                        default:
                                            $needApprovalColumn = 'report_by';
                                            break;
                                    }
                                    

                                    // If a specific role column is needed, build the query accordingly
                                    if ($_SESSION['ROLE'] !== 'admin') {
                                        if (!empty($needApprovalColumn) && $needApprovalColumn !== 'report_by') {
                                            // Ensure the column name is valid before building the query
                                            $queryString = "
                                                SELECT * 
                                                FROM full_report
                                                WHERE upload_at = (
                                                    SELECT upload_at 
                                                    FROM full_report 
                                                    WHERE id = ?
                                                )
                                                AND $needApprovalColumn = ?";
                                        } else {
                                            // Default case when no specific approval column is needed
                                            $queryString = "
                                                SELECT * 
                                                FROM full_report
                                                WHERE upload_at = (
                                                    SELECT upload_at 
                                                    FROM full_report 
                                                    WHERE id = ?
                                                )
                                                AND report_by = ?";
                                        }
                                    }


                                    // Prepare the query
                                    $stmt = $connDB->prepare($queryString);

                                    // Bind the parameters: report ID and session ID
                                   if ($_SESSION['ROLE'] !== 'admin') {
                                        $reportID = $_GET['item_id'] ?? $_SESSION['ID']; // If no GET parameter, fallback to SESSION ID
                                        $sessionID = $_SESSION['ID'];
                                        $stmt->bind_param("ii", $reportID, $sessionID);
                                    }

                                    // Execute the query
                                    $stmt->execute();

                                    // Get the result
                                    $result = $stmt->get_result();

                                    // Initialize row numbering
                                    $num = 1;

                                    // Fetch and display the result
                                    while ($row = $result->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><?= $row['id'] ?></td>
                                            <td><?= $row['upload_at'] ?></td>
                                            <td><?= $row['project'] ?></td>
                                            <td><?= $row['instansi'] ?></td>
                                            <td><?= $row['kode_proyek'] ?></td>
                                            <td><?= $row['kota'] ?></td>
                                            <td>&emsp;<?= $row['progress'] ?>% &emsp; &emsp; <?= $row['target'] ?>%</td>
                                        </tr>
                                        <?php
                                        $num++;
                                    }
                                    ?>
                                    </tbody>
                                    </table>
                                    </div>
                                    Approval Status:
                            <?php
                                    // Prepare the query with a placeholder to avoid SQL injection
                                    $queryRun = $connDB->prepare("SELECT * FROM full_report WHERE id = ?");
                                    $queryRun->bind_param("i", $_GET['item_id']);
                                    $queryRun->execute();
                                    $res = $queryRun->get_result();
                                    $run = $res->fetch_assoc();
                                ?>

                                <div class="col-16-lg mb-2">
                                    <ul class="list-group list-group-horizontal-xl">
                                        <?php
                                            // Array of approval statuses and corresponding labels
                                            $approvals = [
                                                'status_estimasi_spv' => ['status' => 4, 'label' => 'Supervisor'],
                                                'status_estimasi_m'   => ['status' => 1, 'label' => ' Manager'],
                                                'status_estimasi_gm'  => ['status' => 2, 'label' => ' General Manager'],
                                                'status_estimasi_dir' => ['status' => 3, 'label' => ' Director'],
                                            ];

                                            // Loop through each approval status and generate the list items
                                            foreach ($approvals as $field => $data) {
                                                $isApproved = isset($run[$field]) && $run[$field] == $data['status'];
                                                $bgClass = $isApproved ? 'bg-success' :'bg-secondary'; // Add success class if approved
                                                $textClass = $isApproved ? 'text-white' : 'text-white'; // Add white text for approved status
                                        ?>
                                            <li class="list-group-item <?= $bgClass ?>">
                                                <div class="form-check-<?= strtolower(explode(' ', $data['label'])[0]) ?>">
                                                    <label class="form-check-label <?= $textClass ?>" for="input-check-proyek-<?= strtolower(explode(' ', $data['label'])[0]) ?>">
                                                        <?php if ($isApproved): ?>
                                                            <i class="fas fa-check me-1"></i> <!-- Add check icon if approved -->
                                                        <?php endif; ?>
                                                        <?= $data['label'] ?>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>

                                <?php
                                    // Close the database connection
                                    $connDB->close();
                                ?>                      
                        </div>
                    </div>
                </div>
                
            </div>
        
                        </div>
                    </div>
                </div>
                <?php 
                    if (count($history) > 0 || $_SESSION['role']=="sales"){
                ?>
                    <div class="col-lg-4 p-2">
                        <div class="card">
                            <div class="card-body">
                                <h2>Tanggapan</h2>
                                
                                <?php 
                                    foreach ($history as $user) {
                                        if (fetchNumericRole($user['role']) <= fetchNumericRole($_SESSION['ROLE']) || (fetchNumericRole($user['role'])-1 === fetchNumericRole($_SESSION['ROLE'])) || (fetchNumericRole($user['role'])-2 === fetchNumericRole($_SESSION['ROLE'])) || (fetchNumericRole($user['role']) === fetchNumericRole($_SESSION['ROLE'])) || (fetchNumericRole($user['role'])-1 === fetchNumericRole($_SESSION['ROLE'])) || (fetchNumericRole($user['role'])-3 === fetchNumericRole($_SESSION['ROLE']))){
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