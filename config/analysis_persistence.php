<?php

function saveAnalysisPersistence(
    mysqli $db,
    int $datasetId,
    string $datasetName,
    array $analysis
) {
    $status = [
        "benchmark" => ["saved" => 0, "success" => true],
        "recommendation" => ["saved" => false, "success" => true]
    ];

    $benchmarkResults = $analysis["benchmark_results"] ?? [];
    if (is_array($benchmarkResults) && $benchmarkResults !== []) {
        try {
            $db->begin_transaction();

            $resultStatement = $db->prepare(
                "INSERT INTO benchmark_results
                    (dataset_id, algorithm_name, accuracy, precision_score, recall_score, f1_score, execution_time)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $historyStatement = $db->prepare(
                "INSERT INTO benchmark_history
                    (dataset_id, dataset_name, task_type, algorithm_name, score)
                 VALUES (?, ?, ?, ?, ?)"
            );

            $taskType = (string) ($analysis["dataset"]["task"] ?? $analysis["task"] ?? "unknown");
            foreach ($benchmarkResults as $benchmark) {
                if (!is_array($benchmark) || empty($benchmark["algorithm"]) || !empty($benchmark["error"])) {
                    continue;
                }

                $algorithm = (string) $benchmark["algorithm"];
                $accuracy = isset($benchmark["accuracy"]) ? (float) $benchmark["accuracy"] : null;
                $precision = isset($benchmark["precision"])
                    ? (float) $benchmark["precision"]
                    : (isset($benchmark["precision_score"]) ? (float) $benchmark["precision_score"] : null);
                $recall = isset($benchmark["recall"])
                    ? (float) $benchmark["recall"]
                    : (isset($benchmark["recall_score"]) ? (float) $benchmark["recall_score"] : null);
                $f1 = isset($benchmark["f1"])
                    ? (float) $benchmark["f1"]
                    : (isset($benchmark["f1_score"]) ? (float) $benchmark["f1_score"] : null);
                $executionTime = (float) ($benchmark["execution_time"] ?? 0);
                $score = (float) ($benchmark["primary_score"]
                    ?? $benchmark["accuracy"]
                    ?? $benchmark["r2"]
                    ?? $benchmark["silhouette"]
                    ?? 0);

                $resultStatement->bind_param(
                    "isddddd",
                    $datasetId,
                    $algorithm,
                    $accuracy,
                    $precision,
                    $recall,
                    $f1,
                    $executionTime
                );
                $resultStatement->execute();

                $historyStatement->bind_param(
                    "isssd",
                    $datasetId,
                    $datasetName,
                    $taskType,
                    $algorithm,
                    $score
                );
                $historyStatement->execute();
                $status["benchmark"]["saved"]++;
            }

            $resultStatement->close();
            $historyStatement->close();
            $db->commit();
        } catch (Throwable $error) {
            $db->rollback();
            $status["benchmark"]["success"] = false;
            $status["benchmark"]["error"] = $error->getMessage();
            error_log("Benchmark persistence failed: " . $error->getMessage(), 3, LOG_DIR . "database.log");
        }
    }

    $recommendation = $analysis["recommendation"] ?? null;
    if (is_array($recommendation)) {
        $algorithm = (string) ($recommendation["best_algorithm"] ?? $recommendation["recommended_algorithm"] ?? "");
        if (
            $algorithm !== "" && $algorithm !== "No successful model"
            && $algorithm !== "No single recommendation"
            && $algorithm !== "Category-specific recommendations"
        ) {
            $reason = (string) ($recommendation["reason"] ?? $recommendation["explanation"] ?? $recommendation["recommendation_reason"] ?? "");
            $score = (float) ($recommendation["score"] ?? 0);

            try {
                $db->begin_transaction();
                $currentStatement = $db->prepare(
                    "INSERT INTO recommendations (dataset_id, best_algorithm, explanation) VALUES (?, ?, ?)"
                );
                $currentStatement->bind_param("iss", $datasetId, $algorithm, $reason);
                $currentStatement->execute();

                $historyStatement = $db->prepare(
                    "INSERT INTO recommendation_history
                        (dataset_name, recommended_algorithm, recommendation_reason, score)
                     VALUES (?, ?, ?, ?)"
                );
                $historyStatement->bind_param("sssd", $datasetName, $algorithm, $reason, $score);
                $historyStatement->execute();

                $currentStatement->close();
                $historyStatement->close();
                $db->commit();
                $status["recommendation"]["saved"] = true;
            } catch (Throwable $error) {
                $db->rollback();
                $status["recommendation"]["success"] = false;
                $status["recommendation"]["error"] = $error->getMessage();
                error_log("Recommendation persistence failed: " . $error->getMessage(), 3, LOG_DIR . "database.log");
            }
        }
    }

    return $status;
}
