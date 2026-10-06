<?php

/*
|--------------------------------------------------------------------------
| SimuRithm
|--------------------------------------------------------------------------
| Run Analysis AJAX Controller
| Release 1.3
|--------------------------------------------------------------------------
| Purpose:
|   Receives the user's X variables, Y variables, selected algorithm,
|   and dataset information from the frontend.
|
|   Then sends them to Flask:
|
|       POST http://127.0.0.1:5000/run_analyze
|
|   This file does NOT perform machine learning.
|--------------------------------------------------------------------------
*/

header("Content-Type: application/json");

require_once "../config/config.php";
require_once "../config/database.php";
require_once "../config/analysis_persistence.php";
require_once "../api/flask_connector.php";
require_once "../config/logger.php";


/*
|--------------------------------------------------------------------------
| Response Helper
|--------------------------------------------------------------------------
*/

function jsonResponse(
    bool $success,
    string $message = "",
    array $data = [],
    int $httpCode = 200
) {

    http_response_code($httpCode);

    echo json_encode(

        array_merge(

            [
                "success" => $success,
                "message" => $message
            ],

            $data

        ),

        JSON_UNESCAPED_UNICODE |
            JSON_UNESCAPED_SLASHES

    );

    exit;
}


/*
|--------------------------------------------------------------------------
| Validate Request Method
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    jsonResponse(

        false,

        "Invalid request method.",

        [],

        405

    );
}


/*
|--------------------------------------------------------------------------
| Read JSON Request
|--------------------------------------------------------------------------
|
| Expected request:
|
| {
|     "file_path": "...",
|     "x_variables": ["Age", "Gender", "StudyTime"],
|     "y_variables": ["Grade"],
|     "selected_algorithm": "Random Forest"
| }
|
|--------------------------------------------------------------------------
*/

$rawInput = file_get_contents("php://input");

$data = json_decode(

    $rawInput,

    true

);


if (!is_array($data)) {

    jsonResponse(

        false,

        "Invalid JSON request.",

        [],

        400

    );
}


/*
|--------------------------------------------------------------------------
| Get Dataset Path
|--------------------------------------------------------------------------
*/

$rawFilePath = $data["file_path"] ?? "";
if (!is_string($rawFilePath) && !is_numeric($rawFilePath)) {
    jsonResponse(false, "Dataset file path must be a string.", [], 400);
}
$filePath = trim((string) $rawFilePath);


/*
|--------------------------------------------------------------------------
| Get X Variables
|--------------------------------------------------------------------------
*/

$xVariables = $data["x_variables"] ?? [];


/*
|--------------------------------------------------------------------------
| Get Y Variables
|--------------------------------------------------------------------------
*/

$yVariables = $data["y_variables"] ?? [];


/*
|--------------------------------------------------------------------------
| Get Selected Algorithm
|--------------------------------------------------------------------------
*/

$rawCategory = $data["category"] ?? "";
if (!is_string($rawCategory)) {
    jsonResponse(false, "Analysis category must be a string.", [], 400);
}
$category = trim($rawCategory);
$selectedAlgorithms = $data["algorithms"] ?? [];
$rawDetectedTask = $data["detected_task"] ?? "";
if (!is_string($rawDetectedTask)) {
    jsonResponse(false, "Detected task must be a string.", [], 400);
}
$detectedTask = strtolower(trim($rawDetectedTask));
if (!in_array($detectedTask, ["classification", "regression"], true)) {
    $detectedTask = "";
}


/*
|--------------------------------------------------------------------------
| Validate Dataset Path
|--------------------------------------------------------------------------
*/

if ($filePath === "") {

    jsonResponse(

        false,

        "Dataset file path is required.",

        [],

        400

    );
}


/*
|--------------------------------------------------------------------------
| Validate X Variables
|--------------------------------------------------------------------------
*/

if (!is_array($xVariables)) {

    jsonResponse(

        false,

        "X variables must be an array.",

        [],

        400

    );
}


/*
|--------------------------------------------------------------------------
| Validate Y Variables
|--------------------------------------------------------------------------
*/

if (!is_array($yVariables)) {

    jsonResponse(

        false,

        "Y variables must be an array.",

        [],

        400

    );
}


/*
|--------------------------------------------------------------------------
| At Least One X Variable
|--------------------------------------------------------------------------
*/

if (count($xVariables) === 0) {

    jsonResponse(

        false,

        "Please select at least one X variable.",

        [],

        400

    );
}

foreach (["X" => $xVariables, "Y" => $yVariables] as $variableType => $variables) {
    foreach ($variables as $variable) {
        if (!is_string($variable) && !is_numeric($variable)) {
            jsonResponse(false, "{$variableType} variable names must be strings.", [], 400);
        }
    }
}


/*
|--------------------------------------------------------------------------
| At Least One Y Variable
|--------------------------------------------------------------------------
*/

if (!is_array($selectedAlgorithms)) {
    jsonResponse(false, "Algorithms must be an array.", [], 400);
}

$categoryAlgorithms = [
    "classification" => ["Logistic Regression", "Decision Tree", "Random Forest", "SVM", "KNN", "Naive Bayes"],
    "regression" => ["Linear Regression", "Ridge", "Lasso", "Decision Tree Regressor", "Random Forest Regressor", "SVR"],
    "clustering" => ["K-Means", "DBSCAN", "Hierarchical Clustering"],
    "anomaly_detection" => ["Isolation Forest", "One-Class SVM", "Local Outlier Factor (LOF)"],
    "dimensionality_reduction" => ["PCA", "t-SNE", "UMAP"],
    "topic_modeling" => ["LDA", "NMF"],
    "text_similarity" => ["TF-IDF + Cosine Similarity", "Jaccard Similarity"],
    "sequence_modeling" => ["LSTM", "GRU", "Transformer"]
];

if (count($selectedAlgorithms) === 0) {
    jsonResponse(false, "Select between 1 and 5 models total.", [], 400);
}

$featureOnlyCategories = [
    "clustering",
    "anomaly_detection",
    "dimensionality_reduction",
    "topic_modeling",
    "text_similarity"
];

$algorithmsByCategory = [];
$selectedModels = [];
foreach ($selectedAlgorithms as $selectedModel) {
    if (is_array($selectedModel)) {
        $rawModelCategory = $selectedModel["category"] ?? null;
        $rawAlgorithm = $selectedModel["algorithm"] ?? null;
    } else {
        if (!isset($categoryAlgorithms[$category])) {
            jsonResponse(false, "Select a supported analysis category.", [], 400);
        }
        $rawModelCategory = $category;
        $rawAlgorithm = $selectedModel;
    }

    if (!is_string($rawModelCategory) || !is_string($rawAlgorithm)) {
        jsonResponse(false, "Each selected model must include a valid category and algorithm name.", [], 400);
    }
    $modelCategory = trim($rawModelCategory);
    $algorithm = trim($rawAlgorithm);

    if (!isset($categoryAlgorithms[$modelCategory]) || !in_array($algorithm, $categoryAlgorithms[$modelCategory], true)) {
        jsonResponse(false, "One or more selected models are not supported.", [], 400);
    }

    if (isset($algorithmsByCategory[$modelCategory]) && in_array($algorithm, $algorithmsByCategory[$modelCategory], true)) {
        continue;
    }

    $algorithmsByCategory[$modelCategory][] = $algorithm;
    $selectedModels[] = ["category" => $modelCategory, "algorithm" => $algorithm];
}

$categoryCount = count($selectedModels);
if ($categoryCount < 1 || $categoryCount > 5) {
    jsonResponse(false, "Select between 1 and 5 models total.", [], 400);
}

$requiresTarget = count(array_diff(array_keys($algorithmsByCategory), $featureOnlyCategories)) > 0;

if ($requiresTarget && count($yVariables) === 0) {
    jsonResponse(false, "Please select at least one target.", [], 400);
}


/*
|--------------------------------------------------------------------------
| Clean Variable Names
|--------------------------------------------------------------------------
*/

$xVariables = array_values(

    array_filter(

        array_map(

            function ($value) {

                return trim((string)$value);
            },

            $xVariables

        ),

        function ($value) {

            return $value !== "";
        }

    )

);


$yVariables = array_values(

    array_filter(

        array_map(

            function ($value) {

                return trim((string)$value);
            },

            $yVariables

        ),

        function ($value) {

            return $value !== "";
        }

    )

);

/*
|--------------------------------------------------------------------------
| Prevent Duplicate Variables
|--------------------------------------------------------------------------
*/

$xVariables = array_values(

    array_unique($xVariables)

);


$yVariables = array_values(

    array_unique($yVariables)

);


/*
|--------------------------------------------------------------------------
| Validate Again After Cleaning
|--------------------------------------------------------------------------
*/

if (count($xVariables) === 0) {

    jsonResponse(

        false,

        "No valid X variables were selected.",

        [],

        400

    );
}


if ($requiresTarget && count($yVariables) === 0) {

    jsonResponse(

        false,

        "No valid Y variables were selected.",

        [],

        400

    );
}


/*
|--------------------------------------------------------------------------
| Prevent Same Variable Being Used as X and Y
|--------------------------------------------------------------------------
*/

$overlap = $requiresTarget ? array_intersect(

    $xVariables,

    $yVariables

) : [];


if (count($overlap) > 0) {

    jsonResponse(

        false,

        "A variable cannot be used as both X and Y: " .
            implode(", ", $overlap),

        [],

        400

    );
}


/*
|--------------------------------------------------------------------------
| Verify Dataset Exists
|--------------------------------------------------------------------------
|
| The frontend may send either:
|
|   1. Absolute path
|   2. Stored dataset path
|
|--------------------------------------------------------------------------
*/

if (!file_exists($filePath)) {

    /*
    --------------------------------------------------------------
    Try resolving relative upload path
    --------------------------------------------------------------
    */

    $possiblePath = realpath(

        dirname(__DIR__) .
            "/uploads/" .
            basename($filePath)

    );


    if ($possiblePath !== false) {

        $filePath = $possiblePath;
    }
}


/*
|--------------------------------------------------------------------------
| Final Dataset Validation
|--------------------------------------------------------------------------
*/

if (!file_exists($filePath)) {

    jsonResponse(

        false,

        "Dataset file could not be found.",

        [

            "file_path" => $filePath

        ],

        404

    );
}


/*
|--------------------------------------------------------------------------
| Prepare Flask Request
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Log Analysis Request
|--------------------------------------------------------------------------
*/

if (function_exists("logInfo")) {

    logInfo(

        "SimuRithm analysis requested.",

        [

            "file_path" =>
            $filePath,

            "x_variables" =>
            $xVariables,

            "y_variables" =>
            $yVariables,

            "models" => $selectedModels

        ]

    );
}


/*
|--------------------------------------------------------------------------
| Send Request To Flask
|--------------------------------------------------------------------------
*/

$flaskUrl = "http://127.0.0.1:5000/run_benchmark";
$categoryResults = [];
$categoryErrors = [];
$usedCompatibilityModels = false;
foreach ($algorithmsByCategory as $modelCategory => $categoryModels) {
    $flaskAlgorithms = $categoryModels;
    $compatibilityModels = [];
    foreach ($categoryAlgorithms[$modelCategory] as $availableAlgorithm) {
        if (count($flaskAlgorithms) >= 3) {
            break;
        }
        if (!in_array($availableAlgorithm, $flaskAlgorithms, true)) {
            $flaskAlgorithms[] = $availableAlgorithm;
            $compatibilityModels[] = $availableAlgorithm;
        }
    }

    $usedCompatibilityModels = $usedCompatibilityModels || count($compatibilityModels) > 0;
    $flaskPayload = [
        "file_path" => $filePath,
        "category" => $modelCategory,
        "x_columns" => $xVariables,
        "y_columns" => $yVariables,
        "algorithms" => $flaskAlgorithms,
        "selected_algorithms" => $categoryModels,
        "enable_tuning" => ENABLE_AUTO_TUNING
    ];
    $flaskResponse = false;
    $curlError = "Unknown cURL error.";
    $curlErrno = 0;
    $httpCode = 0;
    for ($attempt = 0; $attempt < 3; $attempt++) {
        $ch = curl_init($flaskUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
            CURLOPT_POSTFIELDS => json_encode($flaskPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 300
        ]);
        $flaskResponse = curl_exec($ch);
        $curlError = curl_error($ch);
        $curlErrno = curl_errno($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($flaskResponse !== false) {
            break;
        }

        $retryableCurlErrors = [CURLE_COULDNT_CONNECT, CURLE_RECV_ERROR];
        if ($attempt < 2 && in_array($curlErrno, $retryableCurlErrors, true)) {
            usleep(250000 * ($attempt + 1));
            continue;
        }
        break;
    }

    if ($flaskResponse === false) {
        $timedOut = $curlErrno === CURLE_OPERATION_TIMEDOUT;
        $errorMessage = $timedOut
            ? "The SimuRithm Flask API timed out while running {$modelCategory}."
            : "Unable to connect to the SimuRithm Flask API while running {$modelCategory} (cURL {$curlErrno}: {$curlError}).";

        if (function_exists("logError")) {
            logError("Flask benchmark request failed.", [
                "category" => $modelCategory,
                "curl_errno" => $curlErrno,
                "curl_error" => $curlError,
                "attempts" => $attempt + 1
            ]);
        }

        jsonResponse(
            false,
            $errorMessage,
            ["category" => $modelCategory, "curl_errno" => $curlErrno, "error" => $curlError],
            $timedOut ? 504 : 502
        );
    }

    $result = json_decode($flaskResponse, true);
    if (!is_array($result)) {
        jsonResponse(
            false,
            "The analysis service returned an invalid response for {$modelCategory}.",
            ["category" => $modelCategory, "http_code" => $httpCode, "raw_response" => $flaskResponse],
            502
        );
    }

    if ($httpCode < 200 || $httpCode >= 300 || !($result["success"] ?? false)) {
        $serviceMessage = $result["message"] ?? "Analysis failed for {$modelCategory}.";
        $categoryErrors[] = [
            "category" => $modelCategory,
            "message" => $serviceMessage,
            "http_code" => $httpCode
        ];
        continue;
    }

    $categoryAnalysis = $result["analysis"] ?? $result;
    if (!is_array($categoryAnalysis)) {
        jsonResponse(
            false,
            "The analysis service returned an invalid result for {$modelCategory}.",
            ["category" => $modelCategory],
            502
        );
    }

    $categoryBenchmarks = $categoryAnalysis["benchmark_results"] ?? null;
    if (!is_array($categoryBenchmarks)) {
        jsonResponse(
            false,
            "The analysis service did not return benchmark results for {$modelCategory}.",
            ["category" => $modelCategory],
            502
        );
    }

    $categoryAnalysis["benchmark_results"] = array_values(array_filter(
        $categoryBenchmarks,
        function ($benchmark) use ($flaskAlgorithms, $modelCategory) {
            if (!is_array($benchmark) || !in_array($benchmark["algorithm"] ?? "", $flaskAlgorithms, true)) {
                return false;
            }
            return true;
        }
    ));
    foreach ($categoryAnalysis["benchmark_results"] as &$benchmark) {
        $benchmark["category"] = $benchmark["category"] ?? $modelCategory;
        $cvScore = $benchmark["cv_score"]
            ?? $benchmark["cross_validation_score"]
            ?? $benchmark["cross_val_score"]
            ?? $benchmark["mean_cv_score"]
            ?? null;
        if (is_numeric($cvScore)) {
            $benchmark["cv_score"] = (float) $cvScore;
        }
    }
    unset($benchmark);

    if (count($compatibilityModels) > 0) {
        $serviceRecommendation = $categoryAnalysis["recommendation"] ?? [];
        $recommendationAlgorithm = is_array($serviceRecommendation)
            ? ($serviceRecommendation["best_algorithm"] ?? $serviceRecommendation["recommended_algorithm"] ?? "")
            : "";
        if (!in_array($recommendationAlgorithm, $categoryModels, true)) {
            unset($categoryAnalysis["recommendation"]);
        }
        unset($categoryAnalysis["feature_importance"]);
    }
    $categoryResults[] = $categoryAnalysis;
}

$failedBenchmarkRows = [];
foreach ($categoryErrors as $categoryError) {
    $failedCategory = (string) ($categoryError["category"] ?? "");
    $failureReason = (string) ($categoryError["message"] ?? "Analysis failed.");
    foreach ($algorithmsByCategory[$failedCategory] ?? [] as $failedAlgorithm) {
        $failedBenchmarkRows[] = [
            "category" => $failedCategory,
            "algorithm" => $failedAlgorithm,
            "target" => implode(", ", $yVariables),
            "status" => "Not run",
            "error" => $failureReason,
            "execution_time" => null
        ];
    }
}

if ($categoryResults === []) {
    $analysisTask = count($algorithmsByCategory) === 1
        ? array_key_first($algorithmsByCategory)
        : "Selected categories";
    $analysisResult = [
        "task" => $analysisTask,
        "dataset" => ["task" => $analysisTask],
        "benchmark_results" => $failedBenchmarkRows,
        "feature_importance" => [],
        "category_errors" => $categoryErrors,
        "recommendation" => [
            "best_algorithm" => "No successful model",
            "task" => $analysisTask,
            "reason" => "No selected model could run for the selected target. See benchmark status for details."
        ]
    ];

    jsonResponse(
        true,
        "Analysis finished, but no selected models were compatible with the target.",
        [
            "analysis" => $analysisResult,
            "detected_task" => $detectedTask,
            "category" => count($algorithmsByCategory) === 1 ? array_key_first($algorithmsByCategory) : "multiple",
            "algorithms" => $selectedModels,
            "x_variables" => $xVariables,
            "y_variables" => $yVariables
        ]
    );
}


/*
|--------------------------------------------------------------------------
| Analysis Successful
|--------------------------------------------------------------------------
*/

$analysisResult = $categoryResults[0];
$analysisResult["category_errors"] = $categoryErrors;
$analysisTask = count($algorithmsByCategory) === 1
    ? array_key_first($algorithmsByCategory)
    : "Selected categories";
$analysisResult["task"] = $analysisTask;
if (!isset($analysisResult["dataset"]) || !is_array($analysisResult["dataset"])) {
    $analysisResult["dataset"] = [];
}
$analysisResult["dataset"]["task"] = $analysisTask;
$analysisResult["benchmark_results"] = [];
$analysisResult["feature_importance"] = [];
$tuningCandidates = [];
$serviceRecommendations = [];
foreach ($categoryResults as $categoryIndex => $categoryResult) {
    $analysisResult["benchmark_results"] = array_merge(
        $analysisResult["benchmark_results"],
        $categoryResult["benchmark_results"] ?? []
    );

    if (!$usedCompatibilityModels && isset($categoryResult["feature_importance"]) && is_array($categoryResult["feature_importance"])) {
        $analysisResult["feature_importance"] = array_merge(
            $analysisResult["feature_importance"],
            $categoryResult["feature_importance"]
        );
    }

    if (isset($categoryResult["tuning"]) && is_array($categoryResult["tuning"])) {
        $categoryTunings = $categoryResult["tuning"]["by_target"] ?? [$categoryResult["tuning"]];
        foreach ($categoryTunings as $categoryTuning) {
            if (is_array($categoryTuning)) {
                $categoryTuning["category"] = $categoryTuning["category"]
                    ?? $categoryResult["dataset"]["task"]
                    ?? "";
                $tuningCandidates[] = $categoryTuning;
            }
        }
    }

    $resultCategory = $categoryResult["dataset"]["task"]
        ?? $categoryResult["benchmark_results"][0]["category"]
        ?? null;
    if ($resultCategory !== null && isset($categoryResult["recommendation"]) && is_array($categoryResult["recommendation"])) {
        $serviceRecommendations[$resultCategory] = $categoryResult["recommendation"];
    }
}
$analysisResult["benchmark_results"] = array_merge(
    $analysisResult["benchmark_results"],
    $failedBenchmarkRows
);

$categoryPerformance = [];
foreach ($analysisResult["benchmark_results"] as $benchmark) {
    if (!is_array($benchmark) || !empty($benchmark["error"])) {
        continue;
    }

    $modelCategory = (string) ($benchmark["category"] ?? "");
    $algorithm = (string) ($benchmark["algorithm"] ?? "");
    $metric = (string) ($benchmark["primary_metric"] ?? "primary score");
    $score = $benchmark["primary_score"] ?? null;
    if (!is_numeric($score)) {
        $metricKey = strtolower(str_replace([" ", "-"], "_", $metric));
        $score = $benchmark[$metricKey] ?? null;
    }
    if ($modelCategory === "" || $algorithm === "" || !is_numeric($score)) {
        continue;
    }

    $metricIsLowerBetter = (bool) preg_match("/(rmse|mse|mae|loss|error|execution.?time)/i", $metric);
    $higherIsBetter = isset($benchmark["higher_is_better"])
        ? filter_var($benchmark["higher_is_better"], FILTER_VALIDATE_BOOLEAN)
        : !$metricIsLowerBetter;
    $categoryPerformance[$modelCategory][$algorithm]["scores"][] = (float) $score;
    $categoryPerformance[$modelCategory][$algorithm]["metric"] = $metric;
    $categoryPerformance[$modelCategory][$algorithm]["higher_is_better"] = $higherIsBetter;
}

$categoryRecommendations = [];
$categoryRanks = [];
foreach ($categoryPerformance as $modelCategory => $algorithms) {
    $candidates = [];
    foreach ($algorithms as $algorithm => $performance) {
        $averageScore = array_sum($performance["scores"]) / count($performance["scores"]);
        $candidates[] = [
            "category" => $modelCategory,
            "algorithm" => $algorithm,
            "metric" => $performance["metric"],
            "score" => $averageScore,
            "higher_is_better" => $performance["higher_is_better"]
        ];
    }

    usort($candidates, function ($left, $right) {
        $difference = $left["score"] <=> $right["score"];
        return $left["higher_is_better"] ? -$difference : $difference;
    });
    foreach ($candidates as $candidateIndex => $candidate) {
        $categoryRanks[$modelCategory][$candidate["algorithm"]] = $candidateIndex + 1;
    }

    $serviceRecommendation = $serviceRecommendations[$modelCategory] ?? [];
    $recommendedAlgorithm = $serviceRecommendation["best_algorithm"] ?? "";
    $selectedForCategory = $algorithmsByCategory[$modelCategory] ?? [];
    $serviceCandidate = null;
    if (in_array($recommendedAlgorithm, $selectedForCategory, true)) {
        foreach ($candidates as $candidate) {
            if ($candidate["algorithm"] === $recommendedAlgorithm) {
                $serviceCandidate = $candidate;
                break;
            }
        }
    }

    if ($serviceCandidate !== null) {
        $serviceCandidate["score"] = is_numeric($serviceRecommendation["score"] ?? null)
            ? (float) $serviceRecommendation["score"]
            : $serviceCandidate["score"];
        $serviceCandidate["metric"] = $serviceRecommendation["metric"]
            ?? $serviceCandidate["metric"];
        $serviceCandidate["recommendation_score"] = $serviceRecommendation["recommendation_score"] ?? null;
        $serviceCandidate["reason"] = $serviceRecommendation["reason"] ?? "Recommended from category benchmark metrics.";
        $categoryRecommendations[] = $serviceCandidate;
    } elseif ($candidates !== []) {
        $categoryRecommendations[] = $candidates[0];
    }
}

$qualityScore = function ($candidate) {
    $score = (float) ($candidate["score"] ?? 0);
    $metric = strtolower((string) ($candidate["metric"] ?? ""));

    if (preg_match("/silhouette/", $metric)) {
        return max(0.0, min(1.0, ($score + 1.0) / 2.0));
    }
    if (preg_match("/(rmse|mse|mae|loss|error)/", $metric)) {
        return 1.0 / (1.0 + max(0.0, $score));
    }
    if (preg_match("/calinski.?harabasz/", $metric)) {
        return max(0.0, $score) / (1.0 + max(0.0, $score));
    }
    if (preg_match("/(accuracy|precision|recall|f1|r.?2|cv|trustworthiness|diversity|similarity)/", $metric)) {
        return max(0.0, min(1.0, $score));
    }

    return max(0.0, min(1.0, (float) ($candidate["recommendation_score"] ?? $score)));
};

foreach ($categoryRecommendations as &$recommendation) {
    $recommendation["overall_quality"] = $qualityScore($recommendation);
}
unset($recommendation);

usort($categoryRecommendations, function ($left, $right) {
    $qualityDifference = $right["overall_quality"] <=> $left["overall_quality"];
    if ($qualityDifference !== 0) {
        return $qualityDifference;
    }

    return ($right["recommendation_score"] ?? 0) <=> ($left["recommendation_score"] ?? 0);
});

$bestRecommendation = $categoryRecommendations[0] ?? null;
foreach ($analysisResult["benchmark_results"] as &$benchmark) {
    $modelCategory = (string) ($benchmark["category"] ?? "");
    $algorithm = (string) ($benchmark["algorithm"] ?? "");
    $benchmark["category_rank"] = $categoryRanks[$modelCategory][$algorithm] ?? null;
    $benchmark["is_recommended"] = $bestRecommendation !== null
        && $modelCategory === $bestRecommendation["category"]
        && $algorithm === $bestRecommendation["algorithm"];
}
unset($benchmark);

if ($bestRecommendation === null) {
    $analysisResult["recommendation"] = [
        "best_algorithm" => "No successful model",
        "task" => $analysisTask,
        "reason" => "No selected model returned a usable primary benchmark score."
    ];
} else {
    $analysisResult["recommendation"] = array_merge($bestRecommendation, [
        "best_algorithm" => $bestRecommendation["algorithm"],
        "task" => $bestRecommendation["category"],
        "reason" => "Best selected algorithm across the dataset based on its normalized category quality score ("
            . $bestRecommendation["metric"] . ")."
    ]);
}

$matchedTuning = [];
if ($bestRecommendation !== null) {
    foreach ($tuningCandidates as $tuningCandidate) {
        $tunedAlgorithm = $tuningCandidate["algorithm"] ?? $tuningCandidate["best_algorithm"] ?? "";
        $tunedCategory = $tuningCandidate["category"] ?? "";
        if (
            $tunedAlgorithm !== $bestRecommendation["algorithm"]
            || $tunedCategory !== $bestRecommendation["category"]
        ) {
            continue;
        }
        $tuningCandidate["category"] = $bestRecommendation["category"];
        $matchedTuning[] = $tuningCandidate;
    }
}

if (count($matchedTuning) === 1) {
    $analysisResult["tuning"] = $matchedTuning[0];
} elseif ($matchedTuning !== []) {
    $analysisResult["tuning"] = ["by_category" => $matchedTuning];
} else {
    unset($analysisResult["tuning"]);
}

if (count($algorithmsByCategory) > 1) {
    $analysisResult["feature_importance"] = [];
}
$persistence = [
    "benchmark" => ["saved" => 0, "success" => true],
    "recommendation" => ["saved" => false, "success" => true]
];

try {
    $database = new Database();
    $connection = $database->connect();
    if ($connection === null) {
        throw new RuntimeException("Unable to connect to SimuRithm database.");
    }

    $datasetId = (int) ($data["dataset_id"] ?? 0);
    $datasetStatement = $connection->prepare(
        "SELECT filename FROM datasets WHERE dataset_id = ?"
    );
    $datasetStatement->bind_param("i", $datasetId);
    $datasetStatement->execute();
    $datasetRecord = $datasetStatement->get_result()->fetch_assoc();
    $datasetStatement->close();

    if (!$datasetRecord) {
        throw new RuntimeException("Dataset record was not found.");
    }

    $persistence = saveAnalysisPersistence(
        $connection,
        $datasetId,
        $datasetRecord["filename"],
        $analysisResult
    );
    $database->close();
} catch (Throwable $error) {
    if (isset($database)) {
        $database->close();
    }
    $persistence["database"]["success"] = false;
    $persistence["database"]["error"] = $error->getMessage();
    error_log("Analysis persistence failed: " . $error->getMessage(), 3, LOG_DIR . "database.log");
}

if (function_exists("logSuccess")) {

    logSuccess(

        "SimuRithm analysis completed successfully.",

        [

            "file_path" =>
            $filePath,

            "x_variables" =>
            $xVariables,

            "y_variables" =>
            $yVariables,

            "models" => $selectedModels

        ]

    );
}


/*
|--------------------------------------------------------------------------
| Return Result To JavaScript
|--------------------------------------------------------------------------
*/

jsonResponse(

    true,

    "Dataset analysis completed successfully.",

    [

        "analysis" => $analysisResult,

        "detected_task" => $detectedTask,

        "persistence" => $persistence,

        "file_path" =>
        $filePath,

        "x_variables" =>
        $xVariables,

        "y_variables" =>
        $yVariables,

        "category" => count($algorithmsByCategory) === 1 ? array_key_first($algorithmsByCategory) : "multiple",

        "algorithms" => $selectedModels

    ]

);
