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
        $queryString = "SELECT U.id as id, U.emp_id as emp_id, U.username as username, U.password as password, U.role as role, U.reports_to as reports_to, U.reports_to_lead_1 as reports_to_supervisor, U.reports_to_lead_2 as reports_to_manager, U.reports_to_lead_3 as reports_to_gmanager, U.reports_to_lead_4 as reports_to_director, REP.role as reports_to_role FROM user U, user REP WHERE U.ID=".$_GET['item_id']." AND U.REPORTS_TO = REP.id";
        $result = $connDB->query($queryString);
            
            
        $user = mysqli_fetch_assoc($result);
        
        if ($user == null){
            $queryString = "SELECT * from user WHERE ID=".$_GET['item_id']."";
            $result = $connDB->query($queryString);
            $user = mysqli_fetch_assoc($result);
        }
    }
    ?>

<!DOCTYPE html>
<html lang="en">
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
                    ?>  <form method="GET" action="user_itenenary_dashboard.php">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                        </form>
                <?php } else {
                    ?> <form method="GET" action="user_itenenary_dashboard.php">
                            <button type="submit" class="btn btn-outline-primary"><i class="fa-solid fa-chevron-left"></i>&nbsp;Back</button>
                        </form>
               <?php } 
            ?>
           
            <form method="GET" action="logout.php">
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </nav>
    </section>
    <section id="section-content">
       <div class="container">
      <div class="card mt-5">
        <div class="card-header">Purchase Product</div>
        <div class="card-body">
          <form id="input-form">
            <div id="product_row1" class="row">
              <div class="col-md-7">
                <label>Product Name</label>
                <input id="name1" type="text" class="form-control" />
              </div>
              <div class="col-md-4">
                <label>Price</label>
                <input id="price1" type="number" class="form-control" />
              </div>
              <div class="col-md-1">
                <br />
                <button
                  onclick="delete_row('1')"
                  type="button"
                  class="btn btn-danger"
                >
                  Delete
                </button>
              </div>
            </div>
          </form>
        </div>
        <div class="card-footer text-end">
          <button onclick="add_more()" class="btn btn-dark">Add More</button>
          <button onclick="submit_form()" class="btn btn-info">Save</button>
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

 var activity = 0;
    function add_more() {
    counter++
    activity++
    var newDiv = `<div id="product_row${counter}" class="row">
                    <label>Buat Aktivitas ${activity}</label><br>
                    <div class="col-6 mb-3">
                        <label for="input-due" class="form-label mb-0 fw-bold">Tanggal Aktivitas</label>
                            <input type="date" class="form-control" id="input-date${counter}" name="input-due">
                            <?php if(isset($_SESSION['ERROR']['input-date'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-date'] ?>
                                </div>
                            <?php } ?>
                    </div>
                     <div class="col-6 mb-3">
                            <label for="input-instansi" class="form-label mb-0 fw-bold">Instansi</label>
                            <input type="text" class="form-control" id="input-instansi${counter}" name="input-instansi" required>
                            <?php if(isset($_SESSION['ERROR']['input-instansi'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-instansi'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-6 mb-3">
                            <label for="input-kode-proyek" class="form-label mb-0 fw-bold">Kode Proyek</label>
                            <input type="text" class="form-control" id="input-kode-proyek${counter}" name="input-kode-proyek" required>
                            <?php if(isset($_SESSION['ERROR']['input-kode-proyek'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-kode-proyek'] ?>
                                </div>
                            <?php } ?>
                    </div>
                     <div class="col-6 mb-3">
                            <label for="input-nama-proyek" class="form-label mb-0 fw-bold">Nama Proyek</label>
                            <input type="text" class="form-control" id="input-nama-proyek${counter}" name="input-nama-proyek" required>
                            <?php if(isset($_SESSION['ERROR']['input-nama-proyek'])) { ?>
                                <div class="alert alert-danger mb-0 py-2 fade show" role="alert">
                                    <?= $_SESSION['ERROR']['input-nama-proyek'] ?>
                                </div>
                            <?php } ?>
                    </div>
                    <div class="col-md-7">
                        <label>Product Name</label>
                        <input id="name${counter}" type="text" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Price</label>
                        <input id="price${counter}" type="number" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <br>
                        <button onclick="delete_row('${counter}')" type="button" class="btn btn-danger">Delete</button>
                    </div>
                </div>`
    var form = document.getElementById('input-form')
    form.insertAdjacentHTML('beforeend', newDiv);
}

function delete_row(id) {
    activity--;
    document.getElementById('product_row'+id).remove()
}

function submit_form() {
    var productData = []
    for (var i = 1; i <= counter; i++){
        var product_row = document.getElementById('product_row'+i)
        if (product_row) {
            var product_name = document.getElementById('name' + i).value
            var price = document.getElementById('price' + i).value
            var data = {
                name: product_name,
                price: price
            }
            productData.push(data)
        }
    }

    axios.post('/dynamicinput/create_report_itenenary.php', {
        productData: productData
    }).then(resp => {
        window.location.reload()
    })
}










</script>








<?php 
    unsetAlert();
?>