<?php 
    include_once "utilities/alert_handler.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Login </title>
    <link rel="icon" href="asset/logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <style>
        .main {
            color: rgb(30, 30, 60);
        }

        .bg-main {
            background-color: rgb(30, 30, 60);
        }
    </style>
</head>
<body class="bg-main">
    <section id="section-login" class="d-flex align-items-center justify-content-center vh-100">
        <?php 
            session_start();
            
            if(isset($_SESSION['id'])){
                // User is already logged in, redirect to their corresponding dashboards
                if ($_SESSION['role'] == "sales"){
                    header("location: sales_dashboard.php");
                }
                elseif ($_SESSION['role'] == "admin"){
                    header("location: admin_dashboard.php");
                }
                elseif ($_SESSION['role'] == "manager"){
                    header("location: manager_dashboard.php");
                }
                elseif ($_SESSION['role'] == "gmanager"){
                    header("location: manager_dashboard.php");
                }
                elseif ($_SESSION['role'] == "director"){
                    header("location: manager_dashboard.php");
                }
            }
        ?>
        <div class="card rounded-4" style="width: 400px;">
            <div class="card-body p-4">
                <form class="bg-white" method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="input-username" class="form-label mb-0 fw-bold">Username</label>
                        <input type="text" class="form-control" id="input-username" name="input-username">
                    </div>
                    <div class="mb-3">
                        <label for="input-password" class="form-label mb-0 fw-bold">Password</label>
                        <input type="password" class="form-control" id="input-password" name="input-password">
                    </div>
                    <?php if(isset($_SESSION['ALERT'])) { ?>
                        <div class="alert alert-danger py-2 mb-3" role=<?=$_SESSION['ALERT']['TYPE'] ?>>
                            <?= $_SESSION['ALERT']['MESSAGE'] ?>
                        </div>
                    <?php } unsetAlert(); ?>
                    <button type="submit" class="btn btn-outline-primary w-100 mt-2">Login</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>