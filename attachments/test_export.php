<?php
    session_start();
    // Include the Dompdf autoloader
    require 'libraries/dompdf/autoload.inc.php';
    include_once "db_conn.php";

    use Dompdf\Dompdf;
    use Dompdf\Options;

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

    // Get the HTML content
    $htmlContent = '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <style> * {font-family: helvetica;} textarea {border-radius: .375rem; padding: auto .25rem;}</style>
                    </head>
                    <body>
                        <h2>Detail Report ' . $_GET["item_id"] . '</h2>
                        <hr>
                        <div><label>Instansi:&nbsp;</label>' . $item['location'] . '</div>
                        <div><label>Kota kunjungan:&nbsp;</label>' . $item['city'] . '</div>
                        <div><label>Kunjungan ke:&nbsp;</label>' . $item['visit_number'] . '</div>
                        <div><label>Nilai prospek:&nbsp;</label>' . 'Rp ' . number_format($item['prospect'], 0, ',', '.') . '</div>
                        <div><label>Peluang:&nbsp;</label>' . $item['chance'] . '</div>
                        <div><label>Pesaing:&nbsp;</label>' . $item['competitor'] . '</div>
                        <div><label>Dibutuhkan kapan:&nbsp;</label>' . DateTime::createFromFormat('Y-m-d', $item['deadline'])->format('d F Y') . '</div>
                        <div><label>Keterangan:&nbsp;</label><textarea>' . $item['note'] . '</textarea></div>
                        <div><label>Komentar dari Sales:&nbsp;</label><textarea>' . $item['sales_note'] . '</textarea></div>
                        <div>
                            <label>Lampiran</label><br>';
                        $fileArr = explode(';', $item['attachment']);
                        array_pop($fileArr);
                        foreach($fileArr as $filePath) {
                            // orientation
                            list($width, $height) = getimagesize($filePath);
                            $orientation = ($width > $height) ? 'landscape' : 'portrait';

                            // parse to base64
                            $imagePath = "data:image/png;base64," . base64_encode(file_get_contents($filePath));
                            if ($orientation === 'landscape') {
    $htmlContent .=         '<div style="width: 1000px; transform-origin: top left; transform: translate(0, ' . $height * 1.12 . 'px) rotate(-90deg); -webkit-transform: translate(0, ' . $height * 1.12 . 'px) rotate(-90deg);"><img src="' . $imagePath . '" style="display: block; max-width: 100%; width: 100%;"></div>';
                            } else {
    $htmlContent .=         '<div style="height: 1000px;"><img src="' . $imagePath . '" style="display: block; margin-left: auto; margin-right: auto; max-height: 100%; height: 100%;"></div>';
                            }
                        }
    $htmlContent .=     '</div>
                    </body>';

    // Initialize Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Load HTML content
    $dompdf->loadHtml($htmlContent);

    // Set paper size (A4 by default)
    $dompdf->setPaper('A4', 'portrait');

    // Render PDF (first parameter is used to save the PDF to a file)
    $dompdf->render();

    // Output the generated PDF to the browser
    $dompdf->stream("exported_page.pdf", array("Attachment" => false));

    echo "HTML page exported to PDF successfully!";
?>