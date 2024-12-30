<?php 
    session_start();
    include_once "db_conn.php";
    include_once "db_conn_itinerary.php";
    include_once "utilities/validation_message_handler.php";
    include_once "utilities/alert_handler.php";
    include_once "logged_user_check.php";

    $queryString = "SELECT * FROM user";

    $result = $conn->query($queryString);
    
    $users = [];
    while($temp = mysqli_fetch_assoc($result)){
        array_push($users, $temp);
    }   

    if(isset($_GET['item_id'])){
        $queryString = "SELECT U.id as id, U.emp_id as emp_id, U.username as username, U.password as password, U.role as role, U.reports_to as reports_to, REP.role as reports_to_role FROM user U, user REP WHERE U.ID=".$_GET['item_id']." AND U.REPORTS_TO = REP.id";
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
            <form method="GET" action="admin_dashboard.php">
                <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
            </form>
            <form method="GET" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </section>
    <section id="section-content">
        <div class="container my-5" style="max-width: 720px;">
            <div class="card">
                <div class="card-body">
                    <?php if(isset($user)) {?>
                        <h2>Perbaharui Data <?= $user['emp_id']; ?></h2>
                        <form action="update_user.php" method="POST">
                    <?php } else { ?>
                        <h2>Buat User Baru</h2>
                        <form action="create_user.php" method="POST">
                    <?php } ?>
                        <hr>
                        <div class="row">
                            <?php if(!isset($user)) { ?>
                                <div class="col-12 mb-3">
                                    <label for="input-employee-id" class="form-label mb-0 fw-bold">ID Pengguna</label>
                                    <input type="text" class="form-control" id="input-employee-id" name="input-emp-id" value="<?= isset($user) ? $user['emp_id'] : ''; ?>" required>
                                    <?php if(isset($_SESSION['ERROR']['input-emp-id'])) { ?>
                                        <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                            <?= $_SESSION['ERROR']['input-emp-id'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="input-username" class="form-label mb-0 fw-bold">Username</label>
                                    <input type="text" class="form-control" id="input-username" name="input-username" required>
                                    <?php if(isset($_SESSION['ERROR']['input-username'])) { ?>
                                        <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                            <?= $_SESSION['ERROR']['input-username'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="input-password" class="form-label mb-0 fw-bold form-create-only">Password</label>
                                    <input type="password" class="form-control" id="input-password" name="input-password" required>
                                    <?php if(isset($_SESSION['ERROR']['input-password'])) { ?>
                                        <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                            <?= $_SESSION['ERROR']['input-password'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } else { ?>
                                <div class="col-12 mb-3">
                                    <label for="input-employee-id" class="form-label mb-0 fw-bold">ID Pengguna</label>
                                    <input type="text" class="form-control" id="input-employee-id" name="input-emp-id" value="<?= isset($user) ? $user['emp_id'] : ''; ?>" disabled required>
                                    <?php if(isset($_SESSION['ERROR']['input-emp-id'])) { ?>
                                        <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                            <?= $_SESSION['ERROR']['input-emp-id'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="input-username" class="form-label mb-0 fw-bold">Username</label>
                                    <input type="text" class="form-control" id="input-username" name="input-username" value="<?= isset($user) ? $user['username'] : ''; ?>" required>
                                    <?php if(isset($_SESSION['ERROR']['input-username'])) { ?>
                                        <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                            <?= $_SESSION['ERROR']['input-username'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <input type="hidden" name="input-id" value="<?= $user['id']; ?>">
                            <?php } ?>
                            
                            <div class="col-12 mb-3">
                                <label for="select-role" class="form-label mb-0 fw-bold">Role</label>
                                <select name="input-role" class="form-select" id="select-role" required>
                                    <?php if(isset($user)) { ?>
                                        <option value="" hidden disabled>-</option>
                                        <option value="sales" <?= $user['role'] == 'sales' ? 'selected' : ''; ?>>Staff</option>
                                        <option value="supervisor" <?= $user['role'] == 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                        <option value="manager" <?= $user['role'] == 'manager' ? 'selected' : ''; ?>>Manager</option>
                                        <option value="gmanager" <?= $user['role'] == 'gmanager' ? 'selected' : ''; ?>>General Manager</option>
                                        <option value="director" <?= $user['role'] == 'director' ? 'selected' : ''; ?>>Direktur</option>
                                    <?php } else { ?>
                                        <option value="" hidden selected disabled>-</option>
                                        <option value="sales">Staff</option>
                                        <option value="supervisor">Supervisor</option>
                                        <option value="manager">Manager</option>
                                        <option value="gmanager">General Manager</option>
                                        <option value="director">Direktur</option>
                                    <?php } ?>
                                </select>
                                <?php if(isset($_SESSION['ERROR']['input-role'])) { ?>
                                    <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                        <?= $_SESSION['ERROR']['input-role'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                            
                        </div>
                        <hr>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary" name="submit">Submit</button>
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
</script>

<?php 
    unsetAlert();
?>