<?php 
    session_start();
    // include "logged_user_check.php";
    include "db_conn.php";
?>

<?php if(isset($_GET['item_id'])) { ?>
    <html>
    <head>
        <title></title>
    </head>
        <body>
            <?php 
                $queryString = "SELECT * FROM report WHERE id = " . $_GET['item_id'] . ";";

                $result = $conn->query($queryString);

                echo mysqli_fetch_assoc($result);
            ?>
        </body>
    </html>
<?php } ?>