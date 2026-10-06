<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>SimuRithm PDF Test</title>
</head>
<body>

<h1>SimuRithm PDF Test</h1>

<p>PDF generation is working.</p>

</body>
</html>
';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    'SimuRithm_Test.pdf',
    [
        'Attachment' => false
    ]
);
