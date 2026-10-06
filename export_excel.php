
<?php

/*
==========================================================
 SimuRithm
 Export Excel Report
 Version : Release 1 - Part 1
==========================================================
*/

session_start();

/*
==========================================================
 Load Composer
==========================================================
*/

require_once __DIR__ . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

/*
==========================================================
 Check Session
==========================================================
*/

if (!isset($_SESSION["simurithm_analysis"])) {

    exit("No analysis data found.");
}

$analysis = $_SESSION["simurithm_analysis"];

/*
==========================================================
 Create Workbook
==========================================================
*/

$spreadsheet = new Spreadsheet();

/*
==========================================================
 Summary Sheet
==========================================================
*/

$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle("Summary");

/*
==========================================================
 Report Title
==========================================================
*/

$sheet->mergeCells("A1:D1");

$sheet->setCellValue(

    "A1",

    "SimuRithm Machine Learning Analysis Report"

);

/*
==========================================================
 Title Style
==========================================================
*/

$sheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 18,

        "color" => [

            "rgb" => "FFFFFF"

        ]

    ],

    "alignment" => [

        "horizontal" => Alignment::HORIZONTAL_CENTER

    ],

    "fill" => [

        "fillType" => Fill::FILL_SOLID,

        "startColor" => [

            "rgb" => "198754"

        ]

    ]

]);

/*
==========================================================
 Report Information
==========================================================
*/

$sheet->setCellValue("A3", "Generated On");
$sheet->setCellValue("B3", date("F d, Y h:i A"));

$sheet->setCellValue("A4", "System");
$sheet->setCellValue("B4", "SimuRithm");

$sheet->setCellValue("A5", "Version");
$sheet->setCellValue("B5", "Release 1");

$sheet->setCellValue("A6", "Task Type");

$sheet->setCellValue(

    "B6",

    $analysis["task_type"] ?? "Unknown"

);

/*
==========================================================
 Recommended Algorithm
==========================================================
*/

$sheet->setCellValue(

    "A8",

    "Recommended Algorithm"

);

$sheet->setCellValue(

    "B8",

    $analysis["recommendation"]["best_algorithm"]

        ?? "-"

);

$sheet->setCellValue(

    "A9",

    "Recommendation Score"

);

$sheet->setCellValue(

    "B9",

    $analysis["recommendation"]["score"]

        ?? "-"

);

$sheet->setCellValue(

    "A10",

    "Recommendation Reason"

);

$sheet->setCellValue(

    "B10",

    $analysis["recommendation"]["reason"]

        ?? "-"

);

/*
==========================================================
 Basic Styling
==========================================================
*/

foreach (range('A', 'D') as $column) {

    $sheet->getColumnDimension(

        $column

    )->setAutoSize(true);
}

/*
==========================================================
 Label Style
==========================================================
*/

$sheet->getStyle("A3:A10")->applyFromArray([

    "font" => [

        "bold" => true

    ]

]);

/*
==========================================================
 Border
==========================================================
*/

$sheet->getStyle("A3:B10")->applyFromArray([

    "borders" => [

        "allBorders" => [

            "borderStyle" => Border::BORDER_THIN

        ]

    ]

]);

/*
==========================================================
 Statistics Worksheet
==========================================================
*/

$statisticsSheet = $spreadsheet->createSheet();

$statisticsSheet->setTitle("Statistics");

/*
==========================================================
 Title
==========================================================
*/

$statisticsSheet->mergeCells("A1:B1");

$statisticsSheet->setCellValue(

    "A1",

    "Dataset Statistics"

);

$statisticsSheet->getStyle("A1")->applyFromArray([

    "font" => [
        "bold" => true,
        "size" => 16,
        "color" => [
            "rgb" => "FFFFFF"
        ]
    ],

    "alignment" => [
        "horizontal" => Alignment::HORIZONTAL_CENTER
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,
        "startColor" => [
            "rgb" => "0D6EFD"
        ]
    ]

]);

/*
==========================================================
 Statistics Data
==========================================================
*/

$stats = $analysis["statistics"] ?? [];

$row = 3;

$items = [

    "Rows" => $stats["rows"] ?? "-",

    "Columns" => $stats["columns"] ?? "-",

    "Numeric Columns" => $stats["numeric_columns"] ?? "-",

    "Categorical Columns" => $stats["categorical_columns"] ?? "-",

    "Missing Values" => $stats["missing_values"] ?? "-",

    "Duplicate Rows" => $stats["duplicate_rows"] ?? "-",

    "Memory Usage" => $stats["memory_usage"] ?? "-",

    "File Size" => $stats["file_size"] ?? "-"

];

foreach ($items as $label => $value) {

    $statisticsSheet->setCellValue(

        "A" . $row,

        $label

    );

    $statisticsSheet->setCellValue(

        "B" . $row,

        $value

    );

    $row++;
}

/*
==========================================================
 Styling
==========================================================
*/

$statisticsSheet->getStyle("A3:A10")->applyFromArray([

    "font" => [
        "bold" => true
    ]

]);

$statisticsSheet->getStyle("A3:B10")->applyFromArray([

    "borders" => [

        "allBorders" => [

            "borderStyle" => Border::BORDER_THIN

        ]

    ]

]);

foreach (range('A', 'B') as $column) {

    $statisticsSheet
        ->getColumnDimension($column)
        ->setAutoSize(true);
}

/*
==========================================================
 Dataset Profile Worksheet
==========================================================
*/

$profileSheet = $spreadsheet->createSheet();

$profileSheet->setTitle("Dataset Profile");

/*
==========================================================
 Title
==========================================================
*/

$profileSheet->mergeCells("A1:D1");

$profileSheet->setCellValue(

    "A1",

    "Dataset Profile"

);

$profileSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [

            "rgb" => "FFFFFF"

        ]

    ],

    "alignment" => [

        "horizontal" => Alignment::HORIZONTAL_CENTER

    ],

    "fill" => [

        "fillType" => Fill::FILL_SOLID,

        "startColor" => [

            "rgb" => "6F42C1"

        ]

    ]

]);

/*
==========================================================
 Table Header
==========================================================
*/

$profileSheet->setCellValue("A3", "Column");
$profileSheet->setCellValue("B3", "Data Type");
$profileSheet->setCellValue("C3", "Missing Values");
$profileSheet->setCellValue("D3", "Unique Values");

$profileSheet->getStyle("A3:D3")->applyFromArray([

    "font" => [
        "bold" => true
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,
        "startColor" => [
            "rgb" => "E9ECEF"
        ]
    ]

]);

/*
==========================================================
 Dataset Profile
==========================================================
*/

$profile = $analysis["profile"] ?? [];

$row = 4;

foreach ($profile as $item) {

    $profileSheet->setCellValue(

        "A" . $row,

        $item["column"] ?? "-"

    );

    $profileSheet->setCellValue(

        "B" . $row,

        $item["dtype"] ?? "-"

    );

    $profileSheet->setCellValue(

        "C" . $row,

        $item["missing"] ?? "-"

    );

    $profileSheet->setCellValue(

        "D" . $row,

        $item["unique"] ?? "-"

    );

    $row++;
}

/*
==========================================================
 Styling
==========================================================
*/

if ($row > 4) {

    $profileSheet->getStyle(

        "A3:D" . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

foreach (range('A', 'D') as $column) {

    $profileSheet
        ->getColumnDimension($column)
        ->setAutoSize(true);
}

/*
==========================================================
 NLP Header Analysis Worksheet
==========================================================
*/

$nlpSheet = $spreadsheet->createSheet();

$nlpSheet->setTitle("NLP Analysis");

/*
==========================================================
 Title
==========================================================
*/

$nlpSheet->mergeCells("A1:C1");

$nlpSheet->setCellValue(

    "A1",

    "Natural Language Processing Header Analysis"

);

$nlpSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [

            "rgb" => "FFFFFF"

        ]

    ],

    "alignment" => [

        "horizontal" => Alignment::HORIZONTAL_CENTER

    ],

    "fill" => [

        "fillType" => Fill::FILL_SOLID,

        "startColor" => [

            "rgb" => "DC3545"

        ]

    ]

]);

/*
==========================================================
 Table Header
==========================================================
*/

$nlpSheet->setCellValue("A3", "Column");

$nlpSheet->setCellValue("B3", "Detected Tokens");

$nlpSheet->setCellValue("C3", "Detected Role");

$nlpSheet->getStyle("A3:C3")->applyFromArray([

    "font" => [

        "bold" => true

    ],

    "fill" => [

        "fillType" => Fill::FILL_SOLID,

        "startColor" => [

            "rgb" => "F8F9FA"

        ]

    ]

]);

/*
==========================================================
 NLP Results
==========================================================
*/

$headers = $analysis["header_analysis"] ?? [];

$row = 4;

foreach ($headers as $item) {

    $tokens = "";

    if (isset($item["tokens"])) {

        $tokens = implode(

            ", ",

            $item["tokens"]

        );
    }

    $nlpSheet->setCellValue(

        "A" . $row,

        $item["column"] ?? "-"

    );

    $nlpSheet->setCellValue(

        "B" . $row,

        $tokens

    );

    $nlpSheet->setCellValue(

        "C" . $row,

        $item["role"] ?? "-"

    );

    $row++;
}

/*
==========================================================
 Styling
==========================================================
*/

if ($row > 4) {

    $nlpSheet->getStyle(

        "A3:C" . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

foreach (range('A', 'C') as $column) {

    $nlpSheet->getColumnDimension(

        $column

    )->setAutoSize(true);
}

/*
==========================================================
 Correlation Matrix Worksheet
==========================================================
*/

$correlationSheet = $spreadsheet->createSheet();

$correlationSheet->setTitle("Correlation Matrix");

/*
==========================================================
 Title
==========================================================
*/

$correlationSheet->mergeCells("A1:Z1");

$correlationSheet->setCellValue(

    "A1",

    "Correlation Matrix"

);

$correlationSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [
            "rgb" => "FFFFFF"
        ]

    ],

    "alignment" => [
        "horizontal" => Alignment::HORIZONTAL_CENTER
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,
        "startColor" => [
            "rgb" => "0DCAF0"
        ]
    ]

]);

/*
==========================================================
 Correlation Data
==========================================================
*/

$correlation = $analysis["correlation"] ?? [];

if (!empty($correlation)) {

    $columns = array_keys($correlation);

    /*
    ------------------------------------------
    Column Headers
    ------------------------------------------
    */

    $columnIndex = 2;

    foreach ($columns as $column) {

        $correlationSheet->setCellValueByColumnAndRow(

            $columnIndex,

            3,

            $column

        );

        $columnIndex++;
    }

    /*
    ------------------------------------------
    Matrix Values
    ------------------------------------------
    */

    $rowNumber = 4;

    foreach ($correlation as $rowName => $values) {

        $correlationSheet->setCellValue(

            "A" . $rowNumber,

            $rowName

        );

        $columnIndex = 2;

        foreach ($columns as $column) {

            $value = $values[$column] ?? "";

            if (is_numeric($value)) {

                $value = round($value, 4);
            }

            $correlationSheet->setCellValueByColumnAndRow(

                $columnIndex,

                $rowNumber,

                $value

            );

            $columnIndex++;
        }

        $rowNumber++;
    }

    /*
    ------------------------------------------
    Header Style
    ------------------------------------------
    */

    $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(

        count($columns) + 1

    );

    $correlationSheet->getStyle(

        "A3:" . $lastColumn . "3"

    )->applyFromArray([

        "font" => [
            "bold" => true
        ],

        "fill" => [
            "fillType" => Fill::FILL_SOLID,
            "startColor" => [
                "rgb" => "E9ECEF"
            ]
        ]

    ]);

    /*
    ------------------------------------------
    Borders
    ------------------------------------------
    */

    $correlationSheet->getStyle(

        "A3:" . $lastColumn . ($rowNumber - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);

    /*
    ------------------------------------------
    Auto Size
    ------------------------------------------
    */

    for ($i = 1; $i <= count($columns) + 1; $i++) {

        $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);

        $correlationSheet

            ->getColumnDimension($letter)

            ->setAutoSize(true);
    }
}

/*
==========================================================
 Strongest Correlations Worksheet
==========================================================
*/

$strongSheet = $spreadsheet->createSheet();

$strongSheet->setTitle("Top Correlations");

/*
==========================================================
 Title
==========================================================
*/

$strongSheet->mergeCells("A1:C1");

$strongSheet->setCellValue(

    "A1",

    "Strongest Correlations"

);

$strongSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [

            "rgb" => "FFFFFF"

        ]

    ],

    "alignment" => [

        "horizontal" => Alignment::HORIZONTAL_CENTER

    ],

    "fill" => [

        "fillType" => Fill::FILL_SOLID,

        "startColor" => [

            "rgb" => "FD7E14"

        ]

    ]

]);

/*
==========================================================
 Header
==========================================================
*/

$strongSheet->setCellValue("A3", "Feature 1");

$strongSheet->setCellValue("B3", "Feature 2");

$strongSheet->setCellValue("C3", "Correlation");

$strongSheet->getStyle("A3:C3")->applyFromArray([

    "font" => [

        "bold" => true

    ],

    "fill" => [

        "fillType" => Fill::FILL_SOLID,

        "startColor" => [

            "rgb" => "F8F9FA"

        ]

    ]

]);

/*
==========================================================
 Data
==========================================================
*/

$pairs = $analysis["strongest_correlations"] ?? [];

$row = 4;

foreach ($pairs as $pair) {

    $strongSheet->setCellValue(

        "A" . $row,

        $pair["feature_1"] ?? "-"

    );

    $strongSheet->setCellValue(

        "B" . $row,

        $pair["feature_2"] ?? "-"

    );

    $strongSheet->setCellValue(

        "C" . $row,

        round(

            $pair["correlation"],

            4

        )

    );

    $row++;
}

/*
==========================================================
 Borders
==========================================================
*/

if ($row > 4) {

    $strongSheet->getStyle(

        "A3:C" . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

/*
==========================================================
 Auto Width
==========================================================
*/

foreach (range("A", "C") as $column) {

    $strongSheet

        ->getColumnDimension($column)

        ->setAutoSize(true);
}

/*
==========================================================
 Benchmark Results Worksheet
==========================================================
*/

$benchmarkSheet = $spreadsheet->createSheet();

$benchmarkSheet->setTitle("Benchmark");

/*
==========================================================
 Title
==========================================================
*/

$benchmarkSheet->mergeCells("A1:G1");

$benchmarkSheet->setCellValue(

    "A1",

    "Machine Learning Benchmark Results"

);

$benchmarkSheet->getStyle("A1")->applyFromArray([

    "font" => [
        "bold" => true,
        "size" => 16,
        "color" => [
            "rgb" => "FFFFFF"
        ]
    ],

    "alignment" => [
        "horizontal" => Alignment::HORIZONTAL_CENTER
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,
        "startColor" => [
            "rgb" => "198754"
        ]
    ]

]);

/*
==========================================================
 Determine Task
==========================================================
*/

$task = strtolower(

    $analysis["task_type"] ?? ""

);

$row = 3;

/*
==========================================================
 Classification Benchmark
==========================================================
*/

if ($task == "classification") {

    $headers = [

        "Algorithm",

        "Accuracy",

        "Precision",

        "Recall",

        "F1 Score",

        "CV Score",

        "Execution Time (s)"

    ];
}

/*
==========================================================
 Regression Benchmark
==========================================================
*/ elseif ($task == "regression") {

    $headers = [

        "Algorithm",

        "RMSE",

        "R² Score",

        "CV Score",

        "Execution Time (s)"

    ];
}

/*
==========================================================
 Clustering Benchmark
==========================================================
*/ else {

    $headers = [

        "Algorithm",

        "Silhouette",

        "Execution Time (s)"

    ];
}

/*
==========================================================
 Header Row
==========================================================
*/

$column = 1;

foreach ($headers as $header) {

    $benchmarkSheet->setCellValueByColumnAndRow(

        $column,

        $row,

        $header

    );

    $column++;
}

$lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(

    count($headers)

);

$benchmarkSheet->getStyle(

    "A3:" . $lastColumn . "3"

)->applyFromArray([

    "font" => [
        "bold" => true
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,
        "startColor" => [
            "rgb" => "F8F9FA"
        ]
    ]

]);

/*
==========================================================
 Benchmark Results
==========================================================
*/

$results = $analysis["benchmark_results"] ?? [];

$row = 4;

foreach ($results as $result) {

    $column = 1;

    foreach ($headers as $header) {

        switch ($header) {

            case "Algorithm":

                $value = $result["algorithm"] ?? "-";

                break;

            case "Accuracy":

                $value = $result["accuracy"] ?? "-";

                break;

            case "Precision":

                $value = $result["precision"] ?? "-";

                break;

            case "Recall":

                $value = $result["recall"] ?? "-";

                break;

            case "F1 Score":

                $value = $result["f1"] ?? "-";

                break;

            case "CV Score":

                $value = $result["cv_score"] ?? "-";

                break;

            case "Execution Time (s)":

                $value = $result["execution_time"] ?? "-";

                break;

            case "RMSE":

                $value = $result["rmse"] ?? "-";

                break;

            case "R² Score":

                $value = $result["r2"] ?? "-";

                break;

            case "Silhouette":

                $value = $result["silhouette"] ?? "-";

                break;

            default:

                $value = "-";
        }

        if (is_numeric($value)) {

            $value = round($value, 4);
        }

        $benchmarkSheet->setCellValueByColumnAndRow(

            $column,

            $row,

            $value

        );

        $column++;
    }

    $row++;
}

/*
==========================================================
 Styling
==========================================================
*/

if ($row > 4) {

    $benchmarkSheet->getStyle(

        "A3:" . $lastColumn . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

for (

    $i = 1;

    $i <= count($headers);

    $i++

) {

    $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);

    $benchmarkSheet

        ->getColumnDimension($letter)

        ->setAutoSize(true);
}

/*
==========================================================
 Recommendation Worksheet
==========================================================
*/

$recommendSheet = $spreadsheet->createSheet();

$recommendSheet->setTitle("Recommendation");

/*
==========================================================
 Recommendation Data
==========================================================
*/

$recommendation = $analysis["recommendation"] ?? [];

/*
==========================================================
 Title
==========================================================
*/

$recommendSheet->mergeCells("A1:B1");

$recommendSheet->setCellValue(

    "A1",

    "SimuRithm Recommendation"

);

$recommendSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [
            "rgb" => "FFFFFF"
        ]

    ],

    "alignment" => [
        "horizontal" => Alignment::HORIZONTAL_CENTER
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,

        "startColor" => [
            "rgb" => "20C997"
        ]
    ]

]);

/*
==========================================================
 Recommendation Summary
==========================================================
*/

$recommendSheet->setCellValue("A3", "Task Type");

$recommendSheet->setCellValue(

    "B3",

    $analysis["task_type"] ?? "-"

);

$recommendSheet->setCellValue("A4", "Recommended Algorithm");

$recommendSheet->setCellValue(

    "B4",

    $recommendation["best_algorithm"] ?? "-"

);

$recommendSheet->setCellValue("A5", "Primary Metric");

$recommendSheet->setCellValue(

    "B5",

    $recommendation["metric"] ?? "-"

);

$recommendSheet->setCellValue("A6", "Winning Score");

$recommendSheet->setCellValue(

    "B6",

    isset($recommendation["score"])

        ? round($recommendation["score"], 4)

        : "-"

);

$recommendSheet->setCellValue("A7", "Reason");

$recommendSheet->setCellValue(

    "B7",

    $recommendation["reason"] ?? "-"

);

/*
==========================================================
 Summary Style
==========================================================
*/

$recommendSheet->getStyle("A3:A7")->applyFromArray([

    "font" => [

        "bold" => true

    ]

]);

$recommendSheet->getStyle("A3:B7")->applyFromArray([

    "borders" => [

        "allBorders" => [

            "borderStyle" => Border::BORDER_THIN

        ]

    ]

]);

foreach (range("A", "B") as $column) {

    $recommendSheet

        ->getColumnDimension($column)

        ->setAutoSize(true);
}

/*
==========================================================
 Algorithm Ranking
==========================================================
*/

$recommendSheet->setCellValue("A10", "Algorithm");

$recommendSheet->setCellValue("B10", "Score");

$recommendSheet->getStyle("A10:B10")->applyFromArray([

    "font" => [
        "bold" => true
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,

        "startColor" => [
            "rgb" => "E9ECEF"
        ]
    ]

]);

$row = 11;

$ranking = $recommendation["ranking"] ?? [];

foreach ($ranking as $item) {

    $recommendSheet->setCellValue(

        "A" . $row,

        $item["algorithm"] ?? "-"

    );

    $recommendSheet->setCellValue(

        "B" . $row,

        isset($item["score"])

            ? round($item["score"], 4)

            : "-"

    );

    $row++;
}

/*
==========================================================
 Ranking Style
==========================================================
*/

if ($row > 11) {

    $recommendSheet->getStyle(

        "A10:B" . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

/*
==========================================================
 Feature Importance Worksheet
==========================================================
*/

$featureSheet = $spreadsheet->createSheet();

$featureSheet->setTitle("Feature Importance");

$importance = $analysis["feature_importance"] ?? [];

/*
==========================================================
 Title
==========================================================
*/

$featureSheet->mergeCells("A1:C1");

$featureSheet->setCellValue(

    "A1",

    "Feature Importance"

);

$featureSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [
            "rgb" => "FFFFFF"
        ]

    ],

    "alignment" => [
        "horizontal" => Alignment::HORIZONTAL_CENTER
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,

        "startColor" => [
            "rgb" => "6610F2"
        ]
    ]

]);

/*
==========================================================
 Sort by Importance
==========================================================
*/

usort(

    $importance,

    function ($a, $b) {

        return ($b["importance"] ?? 0)

            <=>

            ($a["importance"] ?? 0);
    }

);

/*
==========================================================
 Table Header
==========================================================
*/

$featureSheet->setCellValue("A3", "Rank");

$featureSheet->setCellValue("B3", "Feature");

$featureSheet->setCellValue("C3", "Importance");

$featureSheet->getStyle("A3:C3")->applyFromArray([

    "font" => [
        "bold" => true
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,

        "startColor" => [
            "rgb" => "E9ECEF"
        ]
    ]

]);

/*
==========================================================
 Feature Data
==========================================================
*/

$row = 4;

$rank = 1;

foreach ($importance as $feature) {

    $featureSheet->setCellValue(

        "A" . $row,

        $rank

    );

    $featureSheet->setCellValue(

        "B" . $row,

        $feature["feature"] ?? "-"

    );

    $featureSheet->setCellValue(

        "C" . $row,

        isset($feature["importance"])

            ? round($feature["importance"], 4)

            : "-"

    );

    $rank++;

    $row++;
}

/*
==========================================================
 Styling
==========================================================
*/

if ($row > 4) {

    $featureSheet->getStyle(

        "A3:C" . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

foreach (range("A", "C") as $column) {

    $featureSheet

        ->getColumnDimension($column)

        ->setAutoSize(true);
}

/*
==========================================================
 Hyperparameter Tuning Worksheet
==========================================================
*/

$tuningSheet = $spreadsheet->createSheet();

$tuningSheet->setTitle("Hyperparameter Tuning");

$tuning = $analysis["tuning"] ?? [];

/*
==========================================================
 Title
==========================================================
*/

$tuningSheet->mergeCells("A1:B1");

$tuningSheet->setCellValue(

    "A1",

    "Hyperparameter Tuning"

);

$tuningSheet->getStyle("A1")->applyFromArray([

    "font" => [

        "bold" => true,

        "size" => 16,

        "color" => [
            "rgb" => "FFFFFF"
        ]

    ],

    "alignment" => [
        "horizontal" => Alignment::HORIZONTAL_CENTER
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,

        "startColor" => [
            "rgb" => "6C757D"
        ]
    ]

]);

/*
==========================================================
 Algorithm Information
==========================================================
*/

$tuningSheet->setCellValue("A3", "Algorithm");

$tuningSheet->setCellValue(

    "B3",

    $tuning["algorithm"] ?? "-"

);

$tuningSheet->setCellValue("A4", "Best CV Score");

$tuningSheet->setCellValue(

    "B4",

    isset($tuning["best_score"])

        ? round($tuning["best_score"], 4)

        : "-"

);

/*
==========================================================
 Best Parameters
==========================================================
*/

$tuningSheet->setCellValue("A6", "Parameter");

$tuningSheet->setCellValue("B6", "Optimized Value");

$tuningSheet->getStyle("A6:B6")->applyFromArray([

    "font" => [
        "bold" => true
    ],

    "fill" => [
        "fillType" => Fill::FILL_SOLID,

        "startColor" => [
            "rgb" => "E9ECEF"
        ]
    ]

]);

/*
==========================================================
 Parameter Values
==========================================================
*/

$row = 7;

$params = $tuning["best_params"] ?? [];

foreach ($params as $parameter => $value) {

    if (is_array($value)) {

        $value = json_encode($value);
    }

    $tuningSheet->setCellValue(

        "A" . $row,

        $parameter

    );

    $tuningSheet->setCellValue(

        "B" . $row,

        (string)$value

    );

    $row++;
}

/*
==========================================================
 Styling
==========================================================
*/

$tuningSheet->getStyle("A3:A4")->applyFromArray([

    "font" => [
        "bold" => true
    ]

]);

if ($row > 7) {

    $tuningSheet->getStyle(

        "A6:B" . ($row - 1)

    )->applyFromArray([

        "borders" => [

            "allBorders" => [

                "borderStyle" => Border::BORDER_THIN

            ]

        ]

    ]);
}

foreach (range("A", "B") as $column) {

    $tuningSheet
        ->getColumnDimension($column)
        ->setAutoSize(true);
}

/*
==========================================================
 Workbook Properties
==========================================================
*/

$spreadsheet->getProperties()

    ->setCreator("SimuRithm")

    ->setLastModifiedBy("SimuRithm")

    ->setTitle("SimuRithm Machine Learning Analysis Report")

    ->setSubject("Machine Learning Benchmark")

    ->setDescription(

        "Generated automatically by SimuRithm."

    )

    ->setCategory("Machine Learning");

/*
==========================================================
 Professional Styling
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    /*
    ------------------------------------------
    Freeze Header
    ------------------------------------------
    */

    $sheet->freezePane("A3");

    /*
    ------------------------------------------
    Default Font
    ------------------------------------------
    */

    $sheet->getParent()

        ->getDefaultStyle()

        ->getFont()

        ->setName("Calibri")

        ->setSize(11);

    /*
    ------------------------------------------
    Vertical Alignment
    ------------------------------------------
    */

    $sheet->getStyle(

        $sheet->calculateWorksheetDimension()

    )->getAlignment()

        ->setVertical(

            Alignment::VERTICAL_CENTER

        );
}

/*
==========================================================
 Center Table Headers
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    $highestColumn =

        $sheet->getHighestColumn();

    $sheet->getStyle(

        "A3:" . $highestColumn . "3"

    )->getAlignment()

        ->setHorizontal(

            Alignment::HORIZONTAL_CENTER

        );
}

/*
==========================================================
 Wrap Text
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    $sheet->getStyle(

        $sheet->calculateWorksheetDimension()

    )->getAlignment()

        ->setWrapText(true);
}

/*
==========================================================
 Auto Row Height
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    foreach (

        $sheet->getRowDimensions()

        as

        $row

    ) {

        $row->setRowHeight(-1);
    }
}

/*
==========================================================
 Highlight Recommended Algorithm
==========================================================
*/

$recommended =

    $analysis["recommendation"]["best_algorithm"]

    ??

    "";

$benchmarkSheet =

    $spreadsheet->getSheetByName("Benchmark");

if (

    $benchmarkSheet !== null

) {

    $highestRow =

        $benchmarkSheet->getHighestRow();

    for (

        $i = 4;

        $i <= $highestRow;

        $i++

    ) {

        if (

            $benchmarkSheet

            ->getCell("A" . $i)

            ->getValue()

            ==

            $recommended

        ) {

            $benchmarkSheet

                ->getStyle(

                    "A" . $i . ":" . $benchmarkSheet->getHighestColumn() . $i

                )

                ->applyFromArray([

                    "fill" => [

                        "fillType" => Fill::FILL_SOLID,

                        "startColor" => [

                            "rgb" => "D1E7DD"

                        ]

                    ],

                    "font" => [

                        "bold" => true

                    ]

                ]);
        }
    }
}

/*
==========================================================
 Numeric Formatting
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    $highestColumn =

        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString(

            $sheet->getHighestColumn()

        );

    $highestRow =

        $sheet->getHighestRow();

    for (

        $col = 1;

        $col <= $highestColumn;

        $col++

    ) {

        $letter =

            \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);

        for (

            $row = 4;

            $row <= $highestRow;

            $row++

        ) {

            $cell =

                $sheet->getCell(

                    $letter . $row

                );

            if (

                is_numeric(

                    $cell->getValue()

                )

            ) {

                $sheet->getStyle(

                    $letter . $row

                )->getNumberFormat()

                    ->setFormatCode(

                        '0.0000'

                    );
            }
        }
    }
}

/*
==========================================================
 Set Active Worksheet
==========================================================
*/

$spreadsheet->setActiveSheetIndex(0);

/*
==========================================================
 Workbook Protection
==========================================================
*/

$spreadsheet->getSecurity()

    ->setLockWindows(false)

    ->setLockStructure(false);

/*
==========================================================
 Footer
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    $sheet->getHeaderFooter()

        ->setOddFooter(

            "&LSimuRithm Machine Learning Analysis Report"

                . "&RPage &P of &N"

        );
}

/*
==========================================================
 Print Settings
==========================================================
*/

foreach (

    $spreadsheet->getAllSheets()

    as

    $sheet

) {

    $sheet->getPageSetup()

        ->setOrientation(

            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE

        );

    $sheet->getPageSetup()

        ->setPaperSize(

            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4

        );

    $sheet->getPageMargins()

        ->setTop(0.5)

        ->setBottom(0.5)

        ->setLeft(0.3)

        ->setRight(0.3);
}

/*
==========================================================
 Dynamic Filename
==========================================================
*/

$fileName =

    "SimuRithm_Report_"

    . date("Ymd_His")

    . ".xlsx";

/*
==========================================================
 Clear Output Buffer
==========================================================
*/

if (ob_get_length()) {

    ob_end_clean();
}

/*
==========================================================
 PART 13A
 Native Excel Benchmark Chart
==========================================================
*/

$benchmarkSheet = $spreadsheet->getSheetByName("Benchmark");

if ($benchmarkSheet !== null) {

    $benchmarkResults = $analysis["benchmark_results"] ?? [];

    if (!empty($benchmarkResults)) {

        $taskType = strtolower(
            $analysis["task_type"] ?? ""
        );

        /*
        ------------------------------------------
        Determine metric
        ------------------------------------------
        */

        if ($taskType === "classification") {

            $metricKey = "accuracy";
            $metricTitle = "Accuracy";
            $metricColumn = "B";
        } elseif ($taskType === "regression") {

            $metricKey = "r2";
            $metricTitle = "R² Score";
            $metricColumn = "C";
        } else {

            $metricKey = "silhouette";
            $metricTitle = "Silhouette Score";
            $metricColumn = "B";
        }

        /*
        ------------------------------------------
        Find rows containing usable values
        ------------------------------------------
        */

        $chartStartRow = 4;
        $chartEndRow = 3;

        foreach ($benchmarkResults as $index => $result) {

            if (
                isset($result["algorithm"]) &&
                isset($result[$metricKey]) &&
                is_numeric($result[$metricKey])
            ) {

                $chartEndRow = $chartStartRow + $index;
            }
        }

        /*
        ------------------------------------------
        Only create chart if data exists
        ------------------------------------------
        */

        if ($chartEndRow >= $chartStartRow) {

            /*
            --------------------------------------
            Category labels
            --------------------------------------
            */

            $categories = [
                new DataSeriesValues(
                    "String",
                    "'Benchmark'!\$A\$" . $chartStartRow . ":\$A\$" . $chartEndRow,
                    null,
                    $chartEndRow - $chartStartRow + 1
                )
            ];

            /*
            --------------------------------------
            Metric values
            --------------------------------------
            */

            $values = [
                new DataSeriesValues(
                    "Number",
                    "'Benchmark'!\$" . $metricColumn . "\$" . $chartStartRow . ":\$" . $metricColumn . "\$" . $chartEndRow,
                    null,
                    $chartEndRow - $chartStartRow + 1
                )
            ];

            /*
            --------------------------------------
            Series
            --------------------------------------
            */

            $series = new DataSeries(
                DataSeries::TYPE_BARCHART,
                DataSeries::GROUPING_CLUSTERED,
                range(0, count($values) - 1),
                [
                    new DataSeriesValues(
                        "String",
                        "'Benchmark'!\$" . $metricColumn . "\$3",
                        null,
                        1
                    )
                ],
                $categories,
                $values
            );

            /*
            --------------------------------------
            Direction
            --------------------------------------
            */

            $series->setPlotDirection(
                DataSeries::DIRECTION_COL
            );

            /*
            --------------------------------------
            Plot area
            --------------------------------------
            */

            $plotArea = new PlotArea(
                null,
                [$series]
            );

            /*
            --------------------------------------
            Legend
            --------------------------------------
            */

            $legend = new Legend(
                Legend::POSITION_BOTTOM,
                null,
                false
            );

            /*
            --------------------------------------
            Chart title
            --------------------------------------
            */

            $title = new Title(
                "Algorithm " . $metricTitle . " Comparison"
            );

            /*
            --------------------------------------
            X-axis title
            --------------------------------------
            */

            $xAxisTitle = new Title(
                "Algorithm"
            );

            /*
            --------------------------------------
            Y-axis title
            --------------------------------------
            */

            $yAxisTitle = new Title(
                $metricTitle
            );

            /*
            --------------------------------------
            Create chart
            --------------------------------------
            */

            $benchmarkChart = new Chart(
                "benchmark_chart",
                $title,
                $legend,
                $plotArea,
                true,
                0,
                $xAxisTitle,
                $yAxisTitle
            );

            /*
            --------------------------------------
            Chart size / location
            --------------------------------------
            */

            $benchmarkChart->setTopLeftPosition("I3");

            $benchmarkChart->setBottomRightPosition("P20");

            /*
            --------------------------------------
            Add chart to worksheet
            --------------------------------------
            */

            $benchmarkSheet->addChart(
                $benchmarkChart
            );
        }
    }
}

/*
==========================================================
 PART 13B
 Native Excel Feature Importance Chart
==========================================================
*/

$featureSheet = $spreadsheet->getSheetByName(
    "Feature Importance"
);

if ($featureSheet !== null) {

    $importance = $analysis["feature_importance"] ?? [];

    if (!empty($importance)) {

        /*
        ------------------------------------------
        Determine final data row
        ------------------------------------------
        */

        $featureStartRow = 4;

        $featureEndRow =
            $featureStartRow +
            count($importance) -
            1;

        /*
        ------------------------------------------
        Feature names
        ------------------------------------------
        */

        $categories = [

            new DataSeriesValues(

                "String",

                "'Feature Importance'!\$B\$"
                    . $featureStartRow .
                    ":\$B\$"
                    . $featureEndRow,

                null,

                count($importance)

            )

        ];

        /*
        ------------------------------------------
        Importance values
        ------------------------------------------
        */

        $values = [

            new DataSeriesValues(

                "Number",

                "'Feature Importance'!\$C\$"
                    . $featureStartRow .
                    ":\$C\$"
                    . $featureEndRow,

                null,

                count($importance)

            )

        ];

        /*
        ------------------------------------------
        Data series
        ------------------------------------------
        */

        $series = new DataSeries(

            DataSeries::TYPE_BARCHART,

            DataSeries::GROUPING_CLUSTERED,

            range(
                0,
                count($values) - 1
            ),

            [

                new DataSeriesValues(

                    "String",

                    "'Feature Importance'!\$C\$3",

                    null,

                    1

                )

            ],

            $categories,

            $values

        );

        /*
        ------------------------------------------
        Horizontal bars
        ------------------------------------------
        */

        $series->setPlotDirection(

            DataSeries::DIRECTION_BAR

        );

        /*
        ------------------------------------------
        Plot area
        ------------------------------------------
        */

        $plotArea = new PlotArea(

            null,

            [$series]

        );

        /*
        ------------------------------------------
        Legend
        ------------------------------------------
        */

        $legend = new Legend(

            Legend::POSITION_BOTTOM,

            null,

            false

        );

        /*
        ------------------------------------------
        Title
        ------------------------------------------
        */

        $title = new Title(

            "Feature Importance"

        );

        /*
        ------------------------------------------
        Axis titles
        ------------------------------------------
        */

        $xAxisTitle = new Title(

            "Importance"

        );

        $yAxisTitle = new Title(

            "Feature"

        );

        /*
        ------------------------------------------
        Create chart
        ------------------------------------------
        */

        $featureChart = new Chart(

            "feature_importance_chart",

            $title,

            $legend,

            $plotArea,

            true,

            0,

            $yAxisTitle,

            $xAxisTitle

        );

        /*
        ------------------------------------------
        Chart position
        ------------------------------------------
        */

        $featureChart->setTopLeftPosition(

            "E3"

        );

        $featureChart->setBottomRightPosition(

            "L25"

        );

        /*
        ------------------------------------------
        Add chart
        ------------------------------------------
        */

        $featureSheet->addChart(

            $featureChart

        );
    }
}

/*
==========================================================
 Output Workbook
==========================================================
*/

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="' . $fileName . '"'
);

header(
    'Cache-Control: max-age=0'
);

header(
    'Pragma: public'
);

header(
    'Expires: 0'
);

if (ob_get_length()) {

    ob_end_clean();
}

$writer = new Xlsx(
    $spreadsheet
);

$writer->setIncludeCharts(true);

$writer->save(
    'php://output'
);

exit;
