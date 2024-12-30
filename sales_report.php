<html>
    <head>
        <title>Sales Report</title>
    </head>
    <body>
        <div>
            <form action="create_report.php" method="POST" enctype="multipart/form-data">
                <label for="location" require>Instansi : </label>
                <input name="location" type="dropdown"><br>
                <label for="note">Keterangan :  </label>
                <textarea name="note"></textarea><br>
                <label for="attachment">Lampiran : </label>
                <input name="attachment" type="file"><br>
                <button type="submit">Submit</button>
            </form>
            <form action="sales_dashboard.php" method="GET">
                <button type="submit">Back</button>
            </form>
        </div>
    </body>
</html>