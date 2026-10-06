<?php

/*
|--------------------------------------------------------------------------
| SimuRithm
|--------------------------------------------------------------------------
| Analysis Configuration
| Release 1.3
|--------------------------------------------------------------------------
| Allows the user to select:
| - Multiple X variables
| - One or multiple Y variables
| - One algorithm
|--------------------------------------------------------------------------
*/
?>

<div
    class="card shadow-sm mb-4"
    id="analysisConfiguration">

    <div class="card-header">

        <h5 class="mb-0">

            <i class="bi bi-sliders"></i>

            Analysis Configuration

        </h5>

        <small class="text-muted">

            Select the variables and algorithm to use
            before running the analysis.

        </small>

    </div>


    <div class="card-body">


        <!-- =========================================================
             X VARIABLES
        ========================================================== -->

        <div class="row g-4 mb-4">

            <div class="col-lg-6">

                <label
                    for="xVariables"
                    class="form-label fw-bold">

                    X Variables
                    <span class="text-danger">*</span>

                </label>

                <select
                    id="xVariables"
                    class="form-select"
                    multiple
                    size="7"
                    aria-describedby="xVariablesHelp">
                </select>

                <div class="d-flex align-items-center gap-2 mt-2">
                    <small class="text-muted" id="xVariablesHelp">
                        Choose one or more input variables.
                        <span class="ms-1">Selected: <span id="xVariableCount">0</span></span>
                    </small>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-auto" id="btnSelectAllX">All</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearX">Clear</button>
                </div>

            </div>

            <div class="col-lg-6">

                <label
                    for="yVariables"
                    class="form-label fw-bold">

                    Y Variables
                    <span class="text-danger">*</span>

                </label>

                <select
                    id="yVariables"
                    class="form-select"
                    multiple
                    size="7"
                    aria-describedby="yVariablesHelp">
                </select>

                <div class="d-flex align-items-center gap-2 mt-2">
                    <small class="text-muted" id="yVariablesHelp">
                        Choose one or more target variables.
                        <span class="ms-1">Selected: <span id="yVariableCount">0</span></span>
                    </small>
                    <button type="button" class="btn btn-outline-secondary btn-sm ms-auto" id="btnSelectAllY">All</button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearY">Clear</button>
                </div>

            </div>
        </div>


        <!-- =========================================================
             ALGORITHM
        ========================================================== -->

        <div class="row g-3 mb-4">
            <div class="col-lg-7">

                <label for="algorithmCategory" class="form-label fw-bold">
                    Analysis Category
                    <span class="text-danger">*</span>
                </label>

                <select id="algorithmCategory" class="form-select mb-3">
                    <option value="classification">Classification</option>
                    <option value="regression">Regression</option>
                    <option value="clustering">Clustering</option>
                    <option value="anomaly_detection">Anomaly Detection</option>
                    <option value="dimensionality_reduction">Dimensionality Reduction</option>
                    <option value="topic_modeling">Topic Modeling</option>
                    <option value="text_similarity">Text Similarity</option>
                    <option value="sequence_modeling">Sequence Modeling</option>
                </select>

                <label for="algorithm" class="form-label fw-bold">
                    Algorithms
                    <span class="text-danger">*</span>
                </label>

                <select id="algorithm" class="form-select" multiple size="7"
                    aria-describedby="algorithmHelp algorithmStatus"></select>

                <div class="d-flex justify-content-between align-items-center gap-2 mt-2">
                    <small class="text-muted" id="algorithmHelp">
                        Choose models from any categories; select 1–5 total.
                    </small>
                    <button type="button" class="btn btn-outline-primary btn-sm flex-shrink-0" id="btnAddAlgorithms">
                        <i class="bi bi-plus-lg me-1"></i>Add selected
                    </button>
                </div>
                <small class="text-muted d-block mt-1" id="algorithmStatus">
                    0 selected; choose 1-5 total
                </small>

            </div>
            <div class="col-lg-5">
                <div class="border rounded p-3 h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Selected models</h6>
                        <span class="badge bg-secondary" id="selectedAlgorithmCount">0</span>
                    </div>
                    <div id="selectedAlgorithmModels" class="d-grid gap-2" aria-live="polite">
                        <small class="text-muted">No models selected.</small>
                    </div>
                </div>
            </div>

        </div>


        <!-- =========================================================
             VALIDATION MESSAGE
        ========================================================== -->

        <div
            id="analysisConfigMessage"
            class="alert alert-warning"
            style="display:none;"></div>


        <!-- =========================================================
             RUN BUTTON
        ========================================================== -->

        <div class="d-flex justify-content-between align-items-center">

            <a href="#explore" class="btn btn-outline-secondary" data-workflow-page="explore">
                <i class="bi bi-arrow-left me-1"></i>Review headers
            </a>

            <button
                type="button"
                id="btnRunAnalysis"
                class="btn btn-primary">

                <i class="bi bi-play-fill"></i>

                Run Analysis

            </button>

        </div>

    </div>

</div>