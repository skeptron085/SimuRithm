<?php

/*
|--------------------------------------------------------------------------
| SimuRithm
|--------------------------------------------------------------------------
| Analysis Configuration
| Release 1.3
|--------------------------------------------------------------------------
| Selection modes:
| - Automatic: SimuRithm selects suitable X/Y variables.
| - Manual: User selects X/Y variables.
| - Both modes allow algorithm selection.
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
            Choose automatic or manual variable selection,
            then configure your algorithms.
        </small>
    </div>

    <div class="card-body">

        <!-- =====================================================
             SELECTION MODE
        ====================================================== -->

        <div class="mb-4">

            <label class="form-label fw-bold">
                Variable Selection Mode
                <span class="text-danger">*</span>
            </label>

            <div class="row g-3">

                <div class="col-md-6">
                    <label
                        for="selectionModeAutomatic"
                        class="border rounded p-3 d-flex gap-2 h-100 w-100"
                        style="cursor:pointer;">

                        <input
                            type="radio"
                            class="form-check-input mt-1"
                            name="selectionMode"
                            id="selectionModeAutomatic"
                            value="automatic"
                            checked>

                        <span>
                            <strong class="d-block">
                                <i class="bi bi-magic me-1"></i>
                                Automatic
                            </strong>

                            <small class="text-muted">
                                SimuRithm selects suitable X/Y variables
                                based on the dataset and analysis category.
                                Variable selectors are hidden.
                            </small>
                        </span>
                    </label>
                </div>

                <div class="col-md-6">
                    <label
                        for="selectionModeManual"
                        class="border rounded p-3 d-flex gap-2 h-100 w-100"
                        style="cursor:pointer;">

                        <input
                            type="radio"
                            class="form-check-input mt-1"
                            name="selectionMode"
                            id="selectionModeManual"
                            value="manual">

                        <span>
                            <strong class="d-block">
                                <i class="bi bi-hand-index-thumb me-1"></i>
                                Manual
                            </strong>

                            <small class="text-muted">
                                Select your own X input variables and
                                Y target variables where required.
                            </small>
                        </span>
                    </label>
                </div>

            </div>

            <div
                id="selectionModeHelp"
                class="alert alert-info py-2 mt-3 mb-0"
                role="status">

                <i class="bi bi-info-circle me-1"></i>

                Automatic mode is active. Choose an analysis category
                and configure the algorithms below.
            </div>

        </div>


        <!-- =====================================================
             X AND Y VARIABLES
             Hidden in Automatic mode
        ====================================================== -->

        <div
            id="variableSelectionSection"
            class="d-none">

            <hr>

            <div class="row g-4 mb-4">

                <!-- X VARIABLES -->

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

                    <div
                        class="d-flex align-items-center gap-2 mt-2">

                        <small
                            class="text-muted"
                            id="xVariablesHelp">

                            Choose one or more input variables.

                            <span class="ms-1">
                                Selected:
                                <span id="xVariableCount">0</span>
                            </span>

                        </small>

                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm ms-auto"
                            id="btnSelectAllX">

                            All

                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="btnClearX">

                            Clear

                        </button>

                    </div>

                </div>


                <!-- Y VARIABLES -->

                <div class="col-lg-6">

                    <label
                        for="yVariables"
                        class="form-label fw-bold">

                        Y Variables
                        <span
                            class="text-danger"
                            id="yRequiredMarker">*</span>

                    </label>

                    <select
                        id="yVariables"
                        class="form-select"
                        multiple
                        size="7"
                        aria-describedby="yVariablesHelp">
                    </select>

                    <div
                        class="d-flex align-items-center gap-2 mt-2">

                        <small
                            class="text-muted"
                            id="yVariablesHelp">

                            Choose target variables when required
                            by the selected category.

                            <span class="ms-1">
                                Selected:
                                <span id="yVariableCount">0</span>
                            </span>

                        </small>

                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm ms-auto"
                            id="btnSelectAllY">

                            All

                        </button>

                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="btnClearY">

                            Clear

                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             ANALYSIS CATEGORY AND ALGORITHMS
        ====================================================== -->

        <hr>

        <div class="row g-3 mb-4">

            <div class="col-lg-7">

                <label
                    for="algorithmCategory"
                    class="form-label fw-bold">

                    Analysis Category
                    <span class="text-danger">*</span>

                </label>

                <select
                    id="algorithmCategory"
                    class="form-select mb-3">

                    <option value="classification">
                        Classification
                    </option>

                    <option value="regression">
                        Regression
                    </option>

                    <option value="clustering">
                        Clustering
                    </option>

                    <option value="anomaly_detection">
                        Anomaly Detection
                    </option>

                    <option value="dimensionality_reduction">
                        Dimensionality Reduction
                    </option>

                    <option value="topic_modeling">
                        Topic Modeling
                    </option>

                    <option value="text_similarity">
                        Text Similarity
                    </option>

                    <option value="sequence_modeling">
                        Sequence Modeling
                    </option>

                </select>


                <label
                    for="algorithm"
                    class="form-label fw-bold">

                    Algorithms
                    <span class="text-danger">*</span>

                </label>

                <select
                    id="algorithm"
                    class="form-select"
                    multiple
                    size="7"
                    aria-describedby="algorithmHelp algorithmStatus">
                </select>

                <div
                    class="d-flex justify-content-between align-items-center gap-2 mt-2">

                    <small
                        class="text-muted"
                        id="algorithmHelp">

                        Select the algorithms to benchmark.

                    </small>

                    <button
                        type="button"
                        class="btn btn-outline-primary btn-sm flex-shrink-0"
                        id="btnAddAlgorithms">

                        <i class="bi bi-plus-lg me-1"></i>
                        Add selected

                    </button>

                </div>

                <small
                    class="text-muted d-block mt-1"
                    id="algorithmStatus"
                    aria-live="polite">

                    0 selected; choose 1–5 total.

                </small>

            </div>


            <!-- SELECTED MODELS -->

            <div class="col-lg-5">

                <div class="border rounded p-3 h-100">

                    <div
                        class="d-flex justify-content-between align-items-center mb-2">

                        <h6 class="mb-0">
                            Selected models
                        </h6>

                        <span
                            class="badge bg-secondary"
                            id="selectedAlgorithmCount">

                            0

                        </span>

                    </div>

                    <div
                        id="selectedAlgorithmModels"
                        class="d-grid gap-2"
                        aria-live="polite">

                        <small class="text-muted">
                            No models selected.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        <!-- =====================================================
             VALIDATION MESSAGE
        ====================================================== -->

        <div
            id="analysisConfigMessage"
            class="alert alert-warning"
            role="alert"
            style="display:none;">
        </div>


        <!-- =====================================================
             ACTION BUTTONS
        ====================================================== -->

        <div
            class="d-flex justify-content-between align-items-center">

            <a
                href="#explore"
                class="btn btn-outline-secondary"
                data-workflow-page="explore">

                <i class="bi bi-arrow-left me-1"></i>
                Review headers

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


<!-- =============================================================
     MODE VISIBILITY
============================================================== -->

<script>
    (function() {
        "use strict";

        function initializeSelectionMode() {
            const automaticRadio = document.getElementById(
                "selectionModeAutomatic"
            );

            const manualRadio = document.getElementById(
                "selectionModeManual"
            );

            const variableSection = document.getElementById(
                "variableSelectionSection"
            );

            const modeHelp = document.getElementById(
                "selectionModeHelp"
            );

            const algorithmHelp = document.getElementById(
                "algorithmHelp"
            );

            if (
                !automaticRadio ||
                !manualRadio ||
                !variableSection
            ) {
                return;
            }

            function updateSelectionMode() {
                const isAutomatic = automaticRadio.checked;

                variableSection.classList.toggle(
                    "d-none",
                    isAutomatic
                );

                if (modeHelp) {
                    if (isAutomatic) {
                        modeHelp.className =
                            "alert alert-info py-2 mt-3 mb-0";

                        modeHelp.innerHTML =
                            '<i class="bi bi-info-circle me-1"></i>' +
                            "Automatic mode is active. X/Y variables " +
                            "will be selected by SimuRithm. Choose a " +
                            "category and configure the algorithms below.";
                    } else {
                        modeHelp.className =
                            "alert alert-secondary py-2 mt-3 mb-0";

                        modeHelp.innerHTML =
                            '<i class="bi bi-hand-index-thumb me-1"></i>' +
                            "Manual mode is active. Select your X/Y " +
                            "variables and the algorithms to benchmark.";
                    }
                }

                if (algorithmHelp) {
                    algorithmHelp.textContent =
                        "Select the algorithms you want SimuRithm to benchmark.";
                }

                /*
                 * Keep manual selections in the controls when switching
                 * modes. The JavaScript request handler must decide whether
                 * to send them based on the active selection mode.
                 */
            }

            automaticRadio.addEventListener(
                "change",
                updateSelectionMode
            );

            manualRadio.addEventListener(
                "change",
                updateSelectionMode
            );

            updateSelectionMode();
        }

        if (document.readyState === "loading") {
            document.addEventListener(
                "DOMContentLoaded",
                initializeSelectionMode
            );
        } else {
            initializeSelectionMode();
        }
    })();
</script>