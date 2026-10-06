<?php

header("Content-Type: application/json");

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/analysis_persistence.php";
require_once "../api/flask_connector.php";
require_once "../config/logger.php";


$response = [

    "success" => false,

    "message" => ""

];


/*
|--------------------------------------------------------------------------
| Validate Request
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    $response["message"] = "Invalid request.";

    echo json_encode($response);

    exit;
}


if (!isset($_FILES["dataset"])) {

    $response["message"] = "No file uploaded.";

    echo json_encode($response);

    exit;
}


$file = $_FILES["dataset"];

/*
|--------------------------------------------------------------------------
| Save Benchmark Results
|--------------------------------------------------------------------------
| Saves the benchmark output returned by Flask into:
|
| 1. benchmark_results
| 2. benchmark_history
|
|--------------------------------------------------------------------------
*/

function saveBenchmarkResults(
    mysqli $db,
    int $datasetId,
    string $datasetName,
    string $taskType,
    array $benchmarkResults
) {

    if (empty($benchmarkResults)) {

        return [
            "success" => true,
            "saved" => 0,
            "message" => "No benchmark results to save."
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare benchmark_results INSERT
    |--------------------------------------------------------------------------
    */

    $resultStatement = $db->prepare("
        INSERT INTO benchmark_results
        (
            dataset_id,
            algorithm_name,
            accuracy,
            precision_score,
            recall_score,
            f1_score,
            execution_time
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    /*
    |--------------------------------------------------------------------------
    | Prepare benchmark_history INSERT
    |--------------------------------------------------------------------------
    */

    $historyStatement = $db->prepare("
        INSERT INTO benchmark_history
        (
            dataset_id,
            dataset_name,
            task_type,
            algorithm_name,
            score
        )
        VALUES (?, ?, ?, ?, ?)
    ");

    $saved = 0;

    try {

        /*
        |--------------------------------------------------------------------------
        | Start Transaction
        |--------------------------------------------------------------------------
        */

        $db->begin_transaction();

        foreach ($benchmarkResults as $benchmark) {

            /*
            |--------------------------------------------------------------------------
            | Validate Algorithm
            |--------------------------------------------------------------------------
            */

            if (!isset($benchmark["algorithm"])) {

                continue;
            }

            $algorithmName = (string) $benchmark["algorithm"];

            /*
            |--------------------------------------------------------------------------
            | Get Metrics
            |--------------------------------------------------------------------------
            */

            $accuracy = isset($benchmark["accuracy"])
                ? (float) $benchmark["accuracy"]
                : 0.0;

            $precision = isset($benchmark["precision"])
                ? (float) $benchmark["precision"]
                : 0.0;

            $recall = isset($benchmark["recall"])
                ? (float) $benchmark["recall"]
                : 0.0;

            $f1 = isset($benchmark["f1"])
                ? (float) $benchmark["f1"]
                : 0.0;

            $executionTime = isset($benchmark["execution_time"])
                ? (float) $benchmark["execution_time"]
                : 0.0;

            /*
            |--------------------------------------------------------------------------
            | Save Detailed Result
            |--------------------------------------------------------------------------
            */

            $resultStatement->bind_param(
                "isddddd",
                $datasetId,
                $algorithmName,
                $accuracy,
                $precision,
                $recall,
                $f1,
                $executionTime
            );

            $resultStatement->execute();

            /*
            |--------------------------------------------------------------------------
            | Save History
            |--------------------------------------------------------------------------
            |
            | For classification:
            | accuracy is the benchmark score.
            |
            */

            $score = $accuracy;

            $historyStatement->bind_param(
                "isssd",
                $datasetId,
                $datasetName,
                $taskType,
                $algorithmName,
                $score
            );

            $historyStatement->execute();

            $saved++;
        }

        /*
        |--------------------------------------------------------------------------
        | Commit
        |--------------------------------------------------------------------------
        */

        $db->commit();

        $resultStatement->close();
        $historyStatement->close();

        return [
            "success" => true,
            "saved" => $saved,
            "message" => "Benchmark results saved successfully."
        ];
    } catch (Throwable $e) {

        /*
        |--------------------------------------------------------------------------
        | Rollback
        |--------------------------------------------------------------------------
        */

        $db->rollback();

        if ($resultStatement) {
            $resultStatement->close();
        }

        if ($historyStatement) {
            $historyStatement->close();
        }

        error_log(
            "Benchmark Save Error: " . $e->getMessage(),
            3,
            LOG_DIR . "database.log"
        );

        return [
            "success" => false,
            "saved" => 0,
            "message" => "Unable to save benchmark results.",
            "error" => $e->getMessage()
        ];
    }
}


/*
|--------------------------------------------------------------------------
| Upload Errors
|--------------------------------------------------------------------------
*/

if ($file["error"] !== UPLOAD_ERR_OK) {

    $response["message"] = "Upload failed. Error code: " . $file["error"];

    echo json_encode($response);

    exit;
}


/*
|--------------------------------------------------------------------------
| File Validation
|--------------------------------------------------------------------------
*/

$allowed = [

    "csv",

    "xls",

    "xlsx"

];


$extension = strtolower(

    pathinfo(

        $file["name"],

        PATHINFO_EXTENSION

    )

);


if (!in_array($extension, $allowed, true)) {

    $response["message"] =

        "Only CSV, XLS and XLSX files are allowed.";

    echo json_encode($response);

    exit;
}


/*
|--------------------------------------------------------------------------
| Maximum File Size
|--------------------------------------------------------------------------
*/

$maxSize = 1024 * 1024 * 1024;


if ($file["size"] > $maxSize) {

    $response["message"] =

        "Dataset exceeds 1GB.";

    echo json_encode($response);

    exit;
}


/*
|--------------------------------------------------------------------------
| Upload Directory
|--------------------------------------------------------------------------
*/

$uploadDirectory = "../uploads/";


if (!file_exists($uploadDirectory)) {

    if (!mkdir($uploadDirectory, 0777, true)) {

        $response["message"] =

            "Unable to create upload directory.";

        echo json_encode($response);

        exit;
    }
}


/*
|--------------------------------------------------------------------------
| Generate Unique Filename
|--------------------------------------------------------------------------
*/

$newFilename =

    date("YmdHis")

    . "_"

    . uniqid()

    . "."

    . $extension;


$destination =

    $uploadDirectory

    . $newFilename;


/*
|--------------------------------------------------------------------------
| Move Uploaded File
|--------------------------------------------------------------------------
*/

if (!move_uploaded_file(

    $file["tmp_name"],

    $destination

)) {

    $response["message"] =

        "Unable to save uploaded file.";

    logError(
        "Unable to save uploaded dataset.",
        [
            "filename" => $file["name"],
            "error_code" => $file["error"]
        ]
    );

    echo json_encode($response);

    exit;
}


$realPath = realpath($destination);


/*
|--------------------------------------------------------------------------
| System Log - Dataset Uploaded
|--------------------------------------------------------------------------
*/

logSuccess(

    "Dataset uploaded successfully.",

    [

        "filename" => $file["name"],

        "stored_name" => $newFilename,

        "file_size" => $file["size"],

        "extension" => $extension

    ]

);

/*
|--------------------------------------------------------------------------
| Database Connection
|--------------------------------------------------------------------------
*/

$database = new Database();

$conn = $database->connect();


if ($conn === null) {

    /*
    |--------------------------------------------------------------------------
    | Delete Uploaded File if Database Fails
    |--------------------------------------------------------------------------
    */

    if (file_exists($realPath)) {

        unlink($realPath);
    }

    $response["message"] =

        "Unable to connect to SimuRithm database.";

    echo json_encode($response);

    exit;
}




/*
|--------------------------------------------------------------------------
| Register Dataset
|--------------------------------------------------------------------------
| Initially rows and columns are NULL.
| They will be updated after Flask analysis.
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare("

        INSERT INTO datasets

        (

            filename,

            total_columns,

            total_rows

        )

        VALUES

        (

            ?,

            NULL,

            NULL

        )

    ");


    $stmt->bind_param(

        "s",

        $file["name"]

    );


    $stmt->execute();


    $datasetId = $stmt->insert_id;


    $stmt->close();


    /*
|--------------------------------------------------------------------------
| System Log - Dataset Registered
|--------------------------------------------------------------------------
*/

    logInfo(

        "Dataset registered in database.",

        [

            "dataset_id" => $datasetId,

            "filename" => $file["name"]

        ]

    );
} catch (Exception $e) {

    if (file_exists($realPath)) {

        unlink($realPath);
    }


    logError(

        "Unable to register dataset in database.",

        [

            "filename" => $file["name"],

            "error" => $e->getMessage()

        ]

    );


    $response["message"] =

        "Unable to register dataset in database.";

    $response["error"] = $e->getMessage();

    echo json_encode($response);

    exit;
}

/*
|--------------------------------------------------------------------------
| Send Dataset to Flask
|--------------------------------------------------------------------------
*/

$flask = new FlaskConnector();

/*
|--------------------------------------------------------------------------
| System Log - Analysis Started
|--------------------------------------------------------------------------
*/

logInfo(

    "Dataset analysis started.",

    [

        "dataset_id" => $datasetId,

        "filename" => $file["name"]

    ]

);


$flask = new FlaskConnector();


$result = $flask->post(

    "/analyze",

    [

        "file_path" => $realPath

    ]

);


/*
|--------------------------------------------------------------------------
| Flask Error
|--------------------------------------------------------------------------
*/

if (

    !isset($result)

    ||

    !is_array($result)

) {

    logError(

        "Invalid response received from Flask API.",

        [

            "dataset_id" => $datasetId,

            "filename" => $file["name"]

        ]

    );


    $response["message"] =

        "Invalid response from Flask API.";

    $response["dataset_id"] = $datasetId;

    echo json_encode($response);

    exit;
}

if (

    isset($result["success"])

    &&

    $result["success"] === false

) {

    /*
    |--------------------------------------------------------------------------
    | System Log - Flask Analysis Failed
    |--------------------------------------------------------------------------
    */

    logError(

        "Dataset analysis failed in Flask API.",

        [

            "dataset_id" => $datasetId,

            "filename" => $file["name"],

            "error" => $result

        ]

    );


    /*
    |--------------------------------------------------------------------------
    | Keep Dataset Record
    |--------------------------------------------------------------------------
    | The file was successfully uploaded and registered.
    | We keep the record for debugging/history.
    |--------------------------------------------------------------------------
    */

    $response["message"] =

        "Dataset uploaded, but Flask analysis failed.";

    $response["dataset_id"] = $datasetId;

    $response["filename"] = $file["name"];

    $response["stored_name"] = $newFilename;

    $response["error"] = $result;

    echo json_encode($response);

    exit;
}


/*
|--------------------------------------------------------------------------
| Extract Statistics
|--------------------------------------------------------------------------
*/

$totalRows = null;

$totalColumns = null;


if (

    isset($result["statistics"])

    &&

    is_array($result["statistics"])

) {

    $totalRows =

        $result["statistics"]["rows"]

        ?? null;

    $totalColumns =

        $result["statistics"]["columns"]

        ?? null;
}

/*
|--------------------------------------------------------------------------
| System Log - Analysis Completed
|--------------------------------------------------------------------------
*/

logSuccess(

    "Dataset analysis completed successfully.",

    [

        "dataset_id" => $datasetId,

        "filename" => $file["name"],

        "rows" => $totalRows,

        "columns" => $totalColumns

    ]

);


/*
|--------------------------------------------------------------------------
| Update Dataset Record
|--------------------------------------------------------------------------
*/

try {

    $stmt = $conn->prepare("

        UPDATE datasets

        SET

            total_columns = ?,

            total_rows = ?

        WHERE dataset_id = ?

    ");



    $stmt->bind_param(

        "iii",

        $totalColumns,

        $totalRows,

        $datasetId

    );


    $stmt->execute();


    $stmt->close();
} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Analysis succeeded even if database update failed
    |--------------------------------------------------------------------------
    */

    logError(

        "Dataset statistics update failed.",

        [

            "dataset_id" => $datasetId,

            "filename" => $file["name"],

            "error" => $e->getMessage()

        ]

    );


    error_log(

        "Dataset statistics update failed: "

            . $e->getMessage()

    );
}
/*
|--------------------------------------------------------------------------
| Save Benchmark Results
|--------------------------------------------------------------------------
*/

$persistence = saveAnalysisPersistence(
    $conn,
    (int) $datasetId,
    $file["name"],
    $result
);


/*
|--------------------------------------------------------------------------
| Close Database
|--------------------------------------------------------------------------
*/

$database->close();

/*
|--------------------------------------------------------------------------
| System Log - Upload and Analysis Complete
|--------------------------------------------------------------------------
*/

logSuccess(

    "Upload and analysis workflow completed.",

    [

        "dataset_id" => $datasetId,

        "filename" => $file["name"],

        "rows" => $totalRows,

        "columns" => $totalColumns

    ]

);


/*
|--------------------------------------------------------------------------
| Success
|--------------------------------------------------------------------------
*/

echo json_encode([

    "success" => true,

    "message" => "Dataset uploaded and analyzed successfully.",

    "dataset_id" => $datasetId,

    "filename" => $file["name"],

    "stored_name" => $newFilename,

    "size" => $file["size"],

    "path" => $realPath,

    "persistence" => $persistence,

    "analysis" => $result

]);
