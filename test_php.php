<?php
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Create a new Dompdf instance
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Sample HTML content
$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test PDF</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            color: #007BFF;
        }
        p {
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Test PDF Generation</h1>
    <p>This is a test PDF document generated using Dompdf.</p>
    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
    <div class="footer">
        <p>Generated on ' . date('Y-m-d H:i:s') . '</p>
    </div>
</body>
</html>
';

// Load HTML content into Dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render the PDF
$dompdf->render();

// Output the generated PDF (force download)
$dompdf->stream("test_pdf.pdf", ["Attachment" => true]);
?>
