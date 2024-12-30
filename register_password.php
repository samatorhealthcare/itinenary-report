<?php 
    session_start();
    include "logged_user_check.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Register</title>
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
    </style>
</head>
<body class="bg-main">
    <section id="section-set_password" class="d-flex align-items-center justify-content-center vh-100">
        <div class="card rounded-4" style="width: 400px;">
            <div class="card-body p-4">
                <div class="card-title">
                    <h3>Registrasi Password</h3>
                </div>
                <hr>
                <form class="bg-white" id="form-set-password" method="POST" action="change_password.php">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floating-input-password" name="input-password" placeholder="">
                        <label for="floating-input-password">Password</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floating-input-confirm_password" placeholder="">
                        <label for="floating-input-confirm_password">Konfirmasi Password</label>
                    </div>
                    <input type="hidden" id="hidden-input-id" name="input-id">
                    <button type="button" class="btn btn-outline-primary w-100 mt-2 btn-save" id="btn-save-<?= $_SESSION['ID'] ?>">Simpan</button>
                </form>
            </div>
        </div>
    </section>
</body>
<script>
    $(document).ready(function() {
        $('.btn-save').on('click', function() {
            checkPasswordConfirmation();
            if ($('#form-set-password')[0].checkValidity()) {
                $('#hidden-input-id').val($(':button').attr('id').split('-')[2]);
                $('#form-set-password').submit();
            } else {
                $('#form-set-password')[0].reportValidity();
            }
        });

        function checkPasswordConfirmation() {
            console.log($('#floating-input-password').val());
            console.log($('#floating-input-confirm_password').val());
            if ($('#floating-input-password').val() != $('#floating-input-confirm_password').val()) {
                $('#floating-input-confirm_password')[0].setCustomValidity("Password tidak sama.");
            }
            else {         
                $('#floating-input-confirm_password')[0].setCustomValidity(""); // Unset the custom validity     
            }
        }
    });
</script>
</html>
