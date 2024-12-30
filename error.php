<?php
    if(isset($_GET['error'])){
        echo($_GET['error']);
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Error </title>
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
    <section id="section-header">
        <nav class="navbar navbar-expand-lg bg-body-tertiary p-3 d-flex justify-content-between">
            <form method="GET" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </section>
    <section id="section-error" class="d-flex align-items-center justify-content-center vh-100">
        <div class="card">
            <div class="card-body">
                <h1 class="mb-0">Too bad</h1>
                <br>
                <h2 class="mb-0">Something happened, please contact the administrator</h2>
            </div>
        </div>
    </section>
</body>
</html>