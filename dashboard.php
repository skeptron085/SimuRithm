<?php
require_once 'config/config.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= APP_NAME ?> Dashboard</title>

    <!-- Bootstrap -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <!-- Bootstrap Icons -->

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet">

    <!-- DataTables -->

    <link
        rel="stylesheet"
        href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

    <!-- SweetAlert2 -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Custom CSS -->

    <link
        rel="stylesheet"
        href="assets/css/dashboard.css?v=<?= filemtime(__DIR__ . '/assets/css/dashboard.css') ?>">

</head>

<body class="bg-light">

    <!-- ===========================================
     NAVBAR
=========================================== -->

    <?php include 'includes/navbar.php'; ?>

    <!-- ===========================================
         MAIN CONTAINER
    =========================================== -->

    <div class="simurithm-layout">

        <?php include 'includes/workflow_sidebar.php'; ?>

        <main class="simu-main-content">

            <div class="container-fluid py-4">

                <section class="workflow-page" id="upload" aria-labelledby="uploadTitle">
                    <header class="workflow-page-header">
                        <div class="workflow-step">STEP 01 <span>/ 04</span></div>
                        <h1 id="uploadTitle">Upload dataset</h1>
                        <p>Choose the data you want to inspect and analyze.</p>
                    </header>
                    <?php include 'includes/upload_card.php'; ?>
                </section>

                <section class="workflow-page" id="explore" aria-labelledby="exploreTitle" hidden>
                    <header class="workflow-page-header">
                        <div class="workflow-step">STEP 02 <span>/ 04</span></div>
                        <h1 id="exploreTitle">Explore dataset</h1>
                        <p>Review its profile, columns, and initial analysis.</p>
                    </header>
                    <?php include 'includes/statistics_cards.php'; ?>
                    <?php include 'includes/dataset_profile.php'; ?>
                    <?php include 'includes/preview_table.php'; ?>
                    <?php include 'includes/nlp_analysis.php'; ?>
                    <div class="d-flex justify-content-end mb-4">
                        <button type="button" class="btn btn-success" id="btnContinueToConfigure" disabled>
                            Continue to configure
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </section>

                <section class="workflow-page" id="configure" aria-labelledby="configureTitle" hidden>
                    <header class="workflow-page-header">
                        <div class="workflow-step">STEP 03 <span>/ 04</span></div>
                        <h1 id="configureTitle">Configure analysis</h1>
                        <p>Select input variables, a target, and an algorithm to run.</p>
                    </header>
                    <?php include 'includes/analysis_configuration.php'; ?>
                </section>

                <section class="workflow-page" id="results" aria-labelledby="resultsTitle" hidden>
                    <header class="workflow-page-header">
                        <div class="workflow-step">STEP 04 <span>/ 04</span></div>
                        <h1 id="resultsTitle">Results</h1>
                        <p>Compare model performance and inspect recommendations.</p>
                    </header>
                    <?php include 'includes/correlation_matrix.php'; ?>
                    <?php include 'includes/strongest_correlations.php'; ?>
                    <?php include 'includes/benchmark_table.php'; ?>
                    <?php include 'includes/benchmark_charts.php'; ?>
                    <?php include 'includes/recommendation_card.php'; ?>
                    <?php include 'includes/feature_importance.php'; ?>
                    <?php include 'includes/tuning_card.php'; ?>
                </section>

            </div>

            <?php include 'includes/footer.php'; ?>

        </main>

    </div>

    <!-- Loading Modal -->

    <?php include 'includes/loading_modal.php'; ?>

    <!-- ===========================================
         JAVASCRIPT
    =========================================== -->

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="assets/js/workflow.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

    <!-- Dashboard -->

    <script src="assets/js/dashboard.js?v=20261024-1"></script>

    <!-- Export -->

    <script src="assets/js/export.js?v=20260929-2"></script>

    <!-- Upload -->

    <script src="assets/js/upload.js"></script>

    <script src="assets/js/analysis_config.js?v=20261024-1"></script>

    <!-- Benchmark -->

    <script src="assets/js/benchmark.js"></script>

    <!-- Charts -->

    <script src="assets/js/charts.js"></script>

    <!-- Hyperparameter -->

    <script src="assets/js/tuning.js"></script>

</body>

</html>