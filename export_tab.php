<?php
//import koneksi ke database
    session_start();
    include_once "db_conn_itinerary.php";
?>
<html>
<head>
  <title>Laporan itinerary</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
   <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- datatables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css">
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
</head>

<body>
<div class="container">
			<h2>Detail itinerary</h2>
			<h4>(Aktivitas)</h4>
				<div class="data-tables datatable-dark">
                    <table id="example" class="display nowrap" style="width:100%">
                             <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>Instansi</th>
                                        <th>Kode Proyek</th>
                                        <th>Kota Kunjungan</th>
                                        <th>Progres Saat Ini</th>
                                        <th>Target</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    //$queryString = "SELECT * FROM full_report WHERE report_by = '".$_SESSION['ID']."' AND upload_at = upload_at";
                                    $queryString = "SELECT * FROM full_report WHERE report_by = '".$_SESSION['ID']."' GROUP BY upload_at";
                                    // $queryString = "SELECT upload_at, GROUP_CONCAT(report_by SEPARATOR ', ') AS members 
                                    //                 FROM full_report 
                                    //                 WHERE report_by = '".$_SESSION['ID']."' 
                                    //                 GROUP BY upload_at";
                                    $result = $connDB->query($queryString);

                                    while($row = mysqli_fetch_assoc($result)){
                                ?>
                                    <tr>
                                        <td><?= $row['id'] ?></td>
                                        <td><?= $row['instansi'] ?></td>
                                        <td><?= $row['kode_proyek'] ?></td>
                                        <td><?= $row['kota'] ?></td>
                                        <td><?= $row['progress'] ?></td>
                                        <td><?= $row['target'] ?></td>
                                        
                                    </tr>
                                <?php
                                    }
                                ?>
                                </tbody>
                      </table>
          </div>
</div>
	
<script>

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

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

	

</body>

</html>