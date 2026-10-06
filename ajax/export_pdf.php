<?php

/*
==========================================================
 SimuRithm
 AJAX PDF Export
 Release 1.3
==========================================================
*/

declare(strict_types=1);

error_reporting(E_ALL);

ini_set('display_errors', '1');

ini_set('display_startup_errors', '1');

ini_set('log_errors', '1');


/*
|--------------------------------------------------------------------------
| Request Method
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    header("Content-Type: text/plain; charset=UTF-8");

    exit("Method Not Allowed.");
}


/*
|--------------------------------------------------------------------------
| Check Analysis
|--------------------------------------------------------------------------
*/

if (!isset($_POST["analysis"])) {

    http_response_code(400);

    header("Content-Type: text/plain; charset=UTF-8");

    exit("No analysis data received.");
}


/*
|--------------------------------------------------------------------------
| Decode Analysis
|--------------------------------------------------------------------------
*/

$analysis = json_decode(
    $_POST["analysis"],
    true
);


/*
|--------------------------------------------------------------------------
| Validate Analysis
|--------------------------------------------------------------------------
*/

if (!is_array($analysis)) {

    http_response_code(400);

    header("Content-Type: text/plain; charset=UTF-8");

    exit("Invalid analysis data.");
}


/*
|--------------------------------------------------------------------------
| Make Analysis Available to export_pdf.php
|--------------------------------------------------------------------------
*/

$_POST["analysis"] = json_encode(
    $analysis,
    JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES
);


/*
|--------------------------------------------------------------------------
| Generate PDF
|--------------------------------------------------------------------------
*/

require_once __DIR__ . "/../export_pdf.php";

exit;
