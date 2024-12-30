<?php
//import koneksi ke database
session_start();
include_once "db_conn.php";
include_once "utilities/util.php";

 if (isset($_GET['item_id'])){
        $queryString = "SELECT * FROM report WHERE ID=".$_GET['item_id']."";
        
        $result = $conn->query($queryString);

        $item = mysqli_fetch_assoc($result);

        $queryString = "SELECT r.action_at as action_at, r.comment as comment, u.username as username, u.role as role, u.emp_id as id FROM report_action r, user u where r.report_id=".$_GET['item_id']." and r.user = u.id ORDER BY action_at DESC";

        $result = $conn->query($queryString);
        

        $history = [];
        
        while($row = mysqli_fetch_assoc($result)){
            array_push($history, $row);
            
        }
    }
    else {
        echo "No item ID given";
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Export Data from database in excel sheet using PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
    
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-4">

                <?php
                if(isset($_SESSION['message']))
                {
                    echo "<h4>".$_SESSION['message']."</h4>";
                    unset($_SESSION['message']);
                }
                ?>

                <div class="card mt-5">
                    <div class="card-header">
                        <h4>Export report to Excel</h4>
                    </div>
                    <div class="card-body">

                        <form action="code.php?item_id=<?= $_GET['item_id']; ?>" method="POST">

                            <select name="export_file_type" class="form-control">
                                <option value="xlsx">XLSXs</option>
                            </select>

                            <button type="submit" name="export_excel_btn" class="btn btn-primary mt-3">Export</button>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>