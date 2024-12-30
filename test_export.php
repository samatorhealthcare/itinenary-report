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
                        <style> 
                            * {
                                font-family: helvetica;
                            } 

                            textarea {
                                border-radius: .375rem; 
                                padding: auto .25rem;
                            }

                            #canvas {
                                position: relative;
                                height: 185px;
                            }

                            #canvas div, #canvas textarea {
                                position: absolute;
                            }

                            #canvas textarea {
                                width: 100%;
                                max-width: 692px;
                                height: 75px;
                            }

                            .page-break {
                                page-break-before: always;
                            }
                        </style>
                    </head>
                    <body>
                        <h2>Detail Report</h2>
                        <hr>
                        <div id="canvas">
                            <div style="top: 0px; left: 0px;">Tanggal pembuatan</div>
                            <div style="top: 0px; left: 175px;">:&nbsp;' . (new DateTime($item['upload_at']))->format("d F Y") . '</div>
                            <div style="top: 0px; left: 350px;">Dibutuhkan kapan</div>
                            <div style="top: 0px; left: 525px;">:&nbsp;' . DateTime::createFromFormat('Y-m-d', $item['deadline'])->format('d F Y') . '</div>

                            <div style="top: 25px; left: 0px;">Instansi</div>
                            <div style="top: 25px; left: 175px;">:&nbsp;' . $item['location'] . '</div>
                            <div style="top: 25px; left: 350px;">Kota kunjungan</div>
                            <div style="top: 25px; left: 525px;">:&nbsp;' . $item['city'] . '</div>

                            <div style="top: 50px; left: 0px;">Kunjungan ke</div>
                            <div style="top: 50px; left: 175px;">:&nbsp;' . $item['visit_number'] . '</div>
                            <div style="top: 50px; left: 350px;">Nilai prospek</div>
                            <div style="top: 50px; left: 525px;">:&nbsp;Rp ' . number_format($item['prospect'], 0, ',', '.') . '</div>
                                
                            <div style="top: 75px; left: 0px;">Peluang</div>
                            <div style="top: 75px; left: 175px;">:&nbsp;' . $item['chance'] . '</div>
                            <div style="top: 75px; left: 350px;">Pesaing</div>
                            <div style="top: 75px; left: 525px;">:&nbsp;' . $item['competitor'] . '</div>
                                
                            <div style="top: 100px; left: 0px;">Keterangan</div>
                            <textarea style="top: 125px; left: 0px;">' . $item['note'] . '</textarea>
                            <div style="top: 225px; left: 0px;">Komentar dari Sales</div>
                            <textarea style="top: 250px; left: 0px;">' . $item['sales_note'] . '</textarea>
                        </div>

                        <div>';
                        $fileArr = explode(';', $item['attachment']);
                        array_pop($fileArr);
                        foreach($fileArr as $filePath) {
                            // orientation
                            list($width, $height) = getimagesize($filePath);
                            $orientation = ($width > $height) ? 'landscape' : 'portrait';

                            // parse to base64
                            $imagePath = "data:image/png;base64," . base64_encode(file_get_contents($filePath));
    //                         if ($orientation === 'landscape') {
    // $htmlContent .=         '<div style="width: 1000px; transform-origin: top left; transform: translate(0, ' . $height * 1.12 . 'px) rotate(-90deg); -webkit-transform: translate(0, ' . $height * 1.12 . 'px) rotate(-90deg);"><img src="' . $imagePath . '" style="display: block; max-width: 100%; width: 100%;"></div>';
    //                         } else {
    $htmlContent .=         '<div class="page-break"></div><h2>Lampiran</h2><hr><div style="height: 950px; width: 703px; text-align: center; overflow: hidden;"><img src="' . $imagePath . '" style="max-height: 100%; height: 100%;"></div>';
    //                         }
                        }
    $htmlContent .=     '</div>
                    </body>';

    // echo $htmlContent;
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
?>