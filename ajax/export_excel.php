<?php

declare(strict_types=1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    header("Content-Type: text/plain; charset=UTF-8");
    exit("Method Not Allowed.");
}

if (!isset($_POST["analysis"])) {
    http_response_code(400);
    header("Content-Type: text/plain; charset=UTF-8");
    exit("No analysis data received.");
}

$analysis = json_decode($_POST["analysis"], true);

if (!is_array($analysis)) {
    http_response_code(400);
    header("Content-Type: text/plain; charset=UTF-8");
    exit("Invalid analysis data.");
}

session_start();
$_SESSION["simurithm_analysis"] = $analysis;

require_once __DIR__ . "/../export_excel.php";
