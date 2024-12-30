<?php 
    include_once "utilities/alert_handler.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandana Intranet | Login </title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <!-- component -->
<!-- This is an example component -->
<div class="max-w-2xl mx-auto">
	<div
		class="bg-white shadow-md border border-gray-200 rounded-lg max-w-sm p-4 sm:p-6 lg:p-8 dark:bg-gray-800 dark:border-gray-700">
         <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 dark:text-white">
          <img class="px-12" src="./asset/new_logo.png" alt="logo-sandana">
        </a>
		<form class="space-y-6" action="login.php" method="POST">
			<h3 class="text-xl font-medium text-gray-900 dark:text-white">Login</h3>
			<div>
				<label for="floating-input-emp_id" class="text-sm font-medium text-gray-900 block mb-2 dark:text-gray-300">ID Pegawai</label>
				<input type="text" name="input-emp_id" id="floating-input-emp_id" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" placeholder="" required="">
            </div>
				<div>
					<label for="floating-input-password" class="text-sm font-medium text-gray-900 block mb-2 dark:text-gray-300">Password</label>
					<input type="password" name="input-password" id="floating-input-password" placeholder="" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required="">
                </div>
                <?php if(isset($_SESSION['ALERT'])) { ?>
                    <div class="alert alert-danger py-2 mb-3" role=<?=$_SESSION['ALERT']['TYPE'] ?>>
                        <?= $_SESSION['ALERT']['MESSAGE'] ?>
                    </div>
                <?php } unsetAlert(); ?>
					<div class="flex items-start">
						<button type="submit" class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Login</button>
					</div>
		</form>
	</div>
</div>
        
</section>
</body>
</html>