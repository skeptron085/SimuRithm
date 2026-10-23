/*
|--------------------------------------------------------------------------
| SimuRithm Analysis Configuration
|--------------------------------------------------------------------------
| Release 1.4
|--------------------------------------------------------------------------
| Handles:
|
| 1. X variable selection
| 2. Y variable selection
| 3. Algorithm selection
| 4. Analysis configuration validation
| 5. Sending configuration to ajax/run_analysis.php
| 6. Loading final analysis results
|--------------------------------------------------------------------------
*/

"use strict";


/*
|--------------------------------------------------------------------------
| Global Analysis Configuration
|--------------------------------------------------------------------------
*/

let analysisConfig = {

    dataset_id: null,

    file_path: "",

    columns: [],

    x_variables: [],

    y_variables: [],

    category: "classification",

    algorithms: [],

    task: "",

    selection_mode: "automatic"

};

const algorithmOptions = {
    classification: [
        "Logistic Regression", "Decision Tree", "Random Forest", "SVM", "KNN", "Naive Bayes"
    ],
    regression: [
        "Linear Regression", "Ridge", "Lasso", "Decision Tree Regressor",
        "Random Forest Regressor", "SVR"
    ],
    clustering: ["K-Means", "DBSCAN", "Hierarchical Clustering"],
    anomaly_detection: ["Isolation Forest", "One-Class SVM", "Local Outlier Factor (LOF)"],
    dimensionality_reduction: ["PCA", "t-SNE", "UMAP"],
    topic_modeling: ["LDA", "NMF"],
    text_similarity: ["TF-IDF + Cosine Similarity", "Jaccard Similarity"],
    sequence_modeling: ["LSTM", "GRU", "Transformer"]
};

const featureOnlyCategories = new Set([
    "clustering", "anomaly_detection", "dimensionality_reduction",
    "topic_modeling", "text_similarity"
]);

/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

$(document).ready(function(){

    initializeAnalysisConfig();

});


function initializeAnalysisConfig(){

    // Keep the variable controls synchronized with Automatic / Manual mode.
    $("input[name=\"selectionMode\"]").on("change", function(){
        updateSelectionModeUI();
    });
    updateSelectionModeUI();

    /*
    ----------------------------------------------------------
    Configuration button
    ----------------------------------------------------------
    */

    $("#btnRunAnalysis").on(
        "click",
        function(){

            collectAnalysisConfiguration();

        }
    );


    /*
    ----------------------------------------------------------
    Select All X
    ----------------------------------------------------------
    */

    $("#btnSelectAllX").on(
        "click",
        function(){

            $("#xVariables option").prop(
                "selected",
                true
            );

            updateSelectedVariableCount();

        }
    );


    /*
    ----------------------------------------------------------
    Clear X
    ----------------------------------------------------------
    */

    $("#btnClearX").on(
        "click",
        function(){

            $("#xVariables option").prop(
                "selected",
                false
            );

            updateSelectedVariableCount();

        }
    );


    /*
    ----------------------------------------------------------
    Select All Y
    ----------------------------------------------------------
    */

    $("#btnSelectAllY").on(
        "click",
        function(){

            $("#yVariables option").prop(
                "selected",
                true
            );

            updateSelectedVariableCount();

        }
    );


    /*
    ----------------------------------------------------------
    Clear Y
    ----------------------------------------------------------
    */

    $("#btnClearY").on(
        "click",
        function(){

            $("#yVariables option").prop(
                "selected",
                false
            );

            updateSelectedVariableCount();

        }
    );


    /*
    ----------------------------------------------------------
    Variable selection changes
    ----------------------------------------------------------
    */

    $("#xVariables, #yVariables").on(
        "change",
        function(){

            updateSelectedVariableCount();

        }
    );


    /*
    ----------------------------------------------------------
    Algorithm change
    ----------------------------------------------------------
    */

    $("#algorithmCategory").on(
        "change",
        function(){
            const category = $(this).val();
            analysisConfig.category = category;
            updateAlgorithmOptions();
            updateCategoryFields();
            updateAlgorithmStatus();
        }
    );

    $("#algorithm").on("change", updateAlgorithmStatus);

    $("#btnAddAlgorithms").on("click", addSelectedAlgorithms);

    $("#selectedAlgorithmModels").on("change", ".selected-model-category", function(){
        const index = Number($(this).closest("[data-model-index]").data("model-index"));
        const category = $(this).val();
        const firstAlgorithm = algorithmOptions[category]?.[0] || "";
        const duplicate = analysisConfig.algorithms.some(function(model, modelIndex){
            return modelIndex !== index && model.category === category && model.algorithm === firstAlgorithm;
        });

        if(duplicate){
            showWarning("That category and model are already selected.");
            renderSelectedAlgorithmModels();
            return;
        }

        analysisConfig.algorithms[index] = {
            category: category,
            algorithm: firstAlgorithm
        };
        renderSelectedAlgorithmModels();
        updateCategoryFields();
        updateAlgorithmStatus();
    });

    $("#selectedAlgorithmModels").on("change", ".selected-model-algorithm", function(){
        const index = Number($(this).closest("[data-model-index]").data("model-index"));
        const algorithm = $(this).val();
        const selected = analysisConfig.algorithms[index];
        const duplicate = analysisConfig.algorithms.some(function(model, modelIndex){
            return modelIndex !== index && model.category === selected.category && model.algorithm === algorithm;
        });

        if(duplicate){
            showWarning("That category and model are already selected.");
            renderSelectedAlgorithmModels();
            return;
        }

        selected.algorithm = algorithm;
    });

    $("#selectedAlgorithmModels").on("click", ".btn-remove-selected-model", function(){
        const index = Number($(this).closest("[data-model-index]").data("model-index"));
        analysisConfig.algorithms.splice(index, 1);
        renderSelectedAlgorithmModels();
        updateCategoryFields();
        updateAlgorithmStatus();
    });

}


/*
|--------------------------------------------------------------------------
| Open Analysis Configuration
|--------------------------------------------------------------------------
|
| This function should be called AFTER:
|
| - Dataset preview
| - NLP header analysis
|
| has already been displayed.
|
*/

function showAnalysisConfiguration(data){

    console.log(
        "Opening analysis configuration:",
        data
    );


    /*
    ----------------------------------------------------------
    Save dataset information
    ----------------------------------------------------------
    */
    const datasetId = data.dataset_id || null;
    const isSameDataset = analysisConfig.dataset_id !== null
        && datasetId !== null
        && String(analysisConfig.dataset_id) === String(datasetId);
    const taskValue = data.task
        || data.task_type
        || data.detected_task
        || data.inferred_task
        || data.analysis?.task
        || data.analysis?.task_type
        || data.analysis?.detected_task
        || data.analysis?.inferred_task
        || data.analysis?.dataset?.task
        || data.analysis?.dataset?.task_type
        || data.analysis?.dataset?.detected_task
        || data.analysis?.dataset?.inferred_task
        || "";
    const normalizedTask = String(taskValue).trim().toLowerCase().replace(/[_-]+/g, " ");
    const detectedTask = /(^|\s)regression($|\s)/.test(normalizedTask)
        ? "regression"
        : /(^|\s)classification($|\s)/.test(normalizedTask)
            ? "classification"
            : "";
    const taskCategory = detectedTask || (isSameDataset ? analysisConfig.task : "");
    const savedConfiguration = isSameDataset ? {
        x_variables: $("#xVariables").val() || analysisConfig.x_variables,
        y_variables: $("#yVariables").val() || analysisConfig.y_variables,
        category: analysisConfig.category,
        algorithms: analysisConfig.algorithms.slice()
    } : null;

    analysisConfig.dataset_id = datasetId;


    analysisConfig.file_path =
        data.file_path ||
        data.path ||
        "";


    analysisConfig.task = taskCategory;

    if(!isSameDataset){
        analysisConfig.category = taskCategory || "classification";
        analysisConfig.columns = [];
        analysisConfig.algorithms = taskCategory ? [{
            category: taskCategory,
            algorithm: algorithmOptions[taskCategory][0]
        }] : [];
        analysisConfig.x_variables = [];
        analysisConfig.y_variables = [];
    }


    /*
    ----------------------------------------------------------
    Get dataset columns
    ----------------------------------------------------------
    */

    let columns = [];


    if(
        Array.isArray(data.columns)
    ){

        columns = data.columns;

    }
    else if(
        data.analysis &&
        data.analysis.statistics &&
        Array.isArray(
            data.analysis.statistics.columns
        )
    ){

        columns =
            data.analysis.statistics.columns;

    }
    else if(
        data.analysis &&
        Array.isArray(data.analysis.header_analysis)
    ){

        columns = data.analysis.header_analysis.map(function(header){

            return header.column || header.name || header.column_name || "";

        }).filter(Boolean);

    }
    else if(
        data.analysis &&
        data.analysis.preview &&
        data.analysis.preview.length > 0
    ){

        columns =
            Object.keys(
                data.analysis.preview[0]
            );

    }

        if(columns.length > 0){
            analysisConfig.columns = columns;
        } else if(isSameDataset){
            columns = analysisConfig.columns;
        }


    /*
    ----------------------------------------------------------
    Populate variables
    ----------------------------------------------------------
    */

    populateVariableSelectors(
        columns
    );


    if(savedConfiguration){
        analysisConfig.x_variables = savedConfiguration.x_variables;
        analysisConfig.y_variables = savedConfiguration.y_variables;
        analysisConfig.algorithms = savedConfiguration.algorithms;
    }

    $("#xVariables").val(analysisConfig.x_variables);
    $("#yVariables").val(analysisConfig.y_variables);
    $("#algorithmCategory").val(analysisConfig.category);
    updateAlgorithmOptions();
    updateCategoryFields();
    renderSelectedAlgorithmModels();


    updateSelectedVariableCount();

    updateAlgorithmStatus();


    /*
    ----------------------------------------------------------
    Show configuration interface
    ----------------------------------------------------------
    */

    $("#analysisConfiguration")
        .slideDown();


    /*
    ----------------------------------------------------------
    Scroll to configuration
    ----------------------------------------------------------
    */

    $("html, body").animate(

        {

            scrollTop:
                $("#analysisConfiguration")
                    .offset()
                    .top - 80

        },

        500

    );

}


/*
|--------------------------------------------------------------------------
| Populate Variable Selectors
|--------------------------------------------------------------------------
*/

function populateVariableSelectors(columns){

    const xSelect =
        $("#xVariables");

    const ySelect =
        $("#yVariables");


    /*
    ----------------------------------------------------------
    Clear previous options
    ----------------------------------------------------------
    */

    xSelect.empty();

    ySelect.empty();


    /*
    ----------------------------------------------------------
    No columns
    ----------------------------------------------------------
    */

    if(
        !Array.isArray(columns) ||
        columns.length === 0
    ){

        xSelect.append(

            $("<option>", {

                value: "",

                text:
                    "No variables available"

            })

        );


        ySelect.append(

            $("<option>", {

                value: "",

                text:
                    "No variables available"

            })

        );

        return;

    }


    /*
    ----------------------------------------------------------
    Add columns
    ----------------------------------------------------------
    */

    columns.forEach(function(column){

        if(
            typeof column === "object"
        ){

            column =
                column.name ||
                column.column ||
                column.column_name ||
                "";

        }


        if(!column){

            return;

        }


        /*
        X Variable
        */

        xSelect.append(

            $("<option>", {

                value: column,

                text: column

            })

        );


        /*
        Y Variable
        */

        ySelect.append(

            $("<option>", {

                value: column,

                text: column

            })

        );

    });

}


/*
|--------------------------------------------------------------------------
| Collect Configuration
|--------------------------------------------------------------------------
*/

function getAnalysisSelectionMode() {
    return $('input[name="selectionMode"]:checked').val() || "automatic";
}

function updateSelectionModeUI(){
    const mode = getAnalysisSelectionMode();
    analysisConfig.selection_mode = mode;

    const automatic = mode === "automatic";
    $("#variableSelectionSection").toggleClass("d-none", automatic);

    if($("#selectionModeHelp").length){
        $("#selectionModeHelp").text(automatic
            ? "SimuRithm will infer suitable input and target variables when required. Choose the algorithm models below."
            : "Select the input variables and target variables required by your selected models.");
    }
}


function collectAnalysisConfiguration() {
    const selectionMode = getAnalysisSelectionMode();
    const isAutomatic = selectionMode === "automatic";

    // Automatic mode lets SimuRithm infer the variables.
    const xVariables = isAutomatic
        ? []
        : ($("#xVariables").val() || []);

    const yVariables = isAutomatic
        ? []
        : ($("#yVariables").val() || []);

    const category = $("#algorithmCategory").val() || "";

    // Keep the selected models from all chosen categories.
    const algorithms = analysisConfig.algorithms || [];

    // At least one model must be selected.
    const minimumAlgorithms = 1;
    const maximumAlgorithms = 5;

    if (
        algorithms.length < minimumAlgorithms ||
        algorithms.length > maximumAlgorithms
    ) {
        showWarning(
            "Select between " +
            minimumAlgorithms +
            " and " +
            maximumAlgorithms +
            " algorithms."
        );
        return;
    }

    const invalidModels = algorithms.filter(function (model) {
    return (
        !model ||
        !algorithmOptions[model.category] ||
        !algorithmOptions[model.category].includes(model.algorithm)
        );
    });

    if (invalidModels.length > 0) {
        showWarning(
        "One or more selected models have an invalid category or algorithm. " +
        "Please review your selected models."
    );
        return;
    }

    // Determine whether any selected model needs a target variable.
    const requiresTarget = algorithms.some(function (model) {
        return !featureOnlyCategories.has(model.category);
    });

    // Validate X variables only in Manual mode.
    if (!isAutomatic && xVariables.length === 0) {
        showWarning("Please select at least one X variable.");
        return;
    }

    // Validate Y variables only in Manual mode and when needed.
    if (
        !isAutomatic &&
        requiresTarget &&
        yVariables.length === 0
    ) {
        showWarning("Please select at least one Y variable.");
        return;
    }

    // A variable cannot be both X and Y in Manual mode.
    if (!isAutomatic && requiresTarget) {
        const overlap = xVariables.filter(function (variable) {
            return yVariables.includes(variable);
        });

        if (overlap.length > 0) {
            showWarning(
                "The same variable cannot be used as both X and Y: " +
                overlap.join(", ")
            );
            return;
        }
    }

    // Save the configuration before sending it to PHP.
    analysisConfig.selection_mode = selectionMode;
    analysisConfig.x_variables = xVariables;
    analysisConfig.y_variables = yVariables;
    analysisConfig.category = category;
    analysisConfig.algorithms = algorithms;

    console.log(
        "Analysis configuration:",
        JSON.stringify(analysisConfig, null, 2)
    );

    // Continue using the existing AJAX workflow.
    runConfiguredAnalysis();
}


/*
|--------------------------------------------------------------------------
| Run Configured Analysis
|--------------------------------------------------------------------------
*/

function runConfiguredAnalysis(){

    /*
    ----------------------------------------------------------
    Validate dataset
    ----------------------------------------------------------
    */

    if(
        !analysisConfig.dataset_id
    ){

        showError(
            "Dataset ID is missing."
        );

        return;

    }


    /*
    ----------------------------------------------------------
    Show loading modal
    ----------------------------------------------------------
    */

    showLoadingModal(
        "Running configured analysis..."
    );


    updateLoadingProgress(
        10,
        analysisConfig.selection_mode === "automatic"
            ? "Inferring suitable variables..."
            : "Preparing selected variables..."
    );


    /*
    ----------------------------------------------------------
    Request
    ----------------------------------------------------------
    */

    $.ajax({

        url:
            "ajax/run_analysis.php",

        type:
            "POST",

        dataType:
            "json",

        contentType:
            "application/json",

        data:
            JSON.stringify({

                dataset_id:
                    analysisConfig.dataset_id,

                file_path:
                    analysisConfig.file_path,

                selection_mode:
                    analysisConfig.selection_mode || getAnalysisSelectionMode(),

                x_variables:
                    analysisConfig.x_variables,

                y_variables:
                    analysisConfig.y_variables,

                category:
                    analysisConfig.category,

                detected_task:
                    analysisConfig.task,

                algorithms:
                    analysisConfig.algorithms

            }),


        /*
        ------------------------------------------------------
        Success
        ------------------------------------------------------
        */

        success:function(response){

            console.log(
                "Configured analysis response:",
                response
            );


            /*
            --------------------------------------------------
            Check application success
            --------------------------------------------------
            */

            if(
                !response ||
                response.success !== true
            ){
                showAnalysisError(
                    response?.message ||
                    "Analysis failed."
                );


                return;

            }


            /*
            --------------------------------------------------
            Progress
            --------------------------------------------------
            */

            updateLoadingProgress(

                30,

                "Computing correlation analysis..."

            );


            /*
            --------------------------------------------------
            Correlation
            --------------------------------------------------
            */

            if(
                response.analysis &&
                response.analysis.correlation
            ){

                if(
                    typeof loadCorrelation ===
                    "function"
                ){

                    loadCorrelation(

                        response.analysis.correlation

                    );

                }

            }


            /*
            --------------------------------------------------
            Strongest Correlations
            --------------------------------------------------
            */

            if(
                response.analysis &&
                Array.isArray(
                    response.analysis
                        .strongest_correlations
                )
            ){

                if(
                    typeof loadStrongCorrelations ===
                    "function"
                ){

                    loadStrongCorrelations(

                        response.analysis
                            .strongest_correlations

                    );

                }

            }


            /*
            --------------------------------------------------
            Benchmark
            --------------------------------------------------
            */

            updateLoadingProgress(

                55,

                "Running selected algorithm..."

            );


            if(
                response.analysis &&
                Array.isArray(
                    response.analysis
                        .benchmark_results
                )
            ){

                if(
                    typeof loadBenchmark ===
                    "function"
                ){

                    loadBenchmark(

                        response.analysis
                            .benchmark_results

                    );

                }


                /*
                ----------------------------------------------
                Benchmark charts
                ----------------------------------------------
                */

                if(
                    typeof loadBenchmarkCharts ===
                    "function"
                ){

                    loadBenchmarkCharts(

                        response.analysis
                            .benchmark_results

                    );

                }

            }


            /*
            --------------------------------------------------
            Feature Importance
            --------------------------------------------------
            */

            if(
                response.analysis &&
                Array.isArray(
                    response.analysis
                        .feature_importance
                )
            ){

                if(
                    typeof loadFeatureImportance ===
                    "function"
                ){

                    loadFeatureImportance(

                        response.analysis
                            .feature_importance

                    );

                }

            }


            /*
            --------------------------------------------------
            Hyperparameter tuning
            --------------------------------------------------
            */

            if(typeof loadTuning === "function"){
                loadTuning(response.analysis?.tuning || null);
            }


            /*
            --------------------------------------------------
            Recommendation
            --------------------------------------------------
            */

            updateLoadingProgress(

                80,

                "Generating recommendation..."

            );


            if(
                response.analysis &&
                response.analysis.recommendation
            ){

                if(
                    typeof loadRecommendation ===
                    "function"
                ){

                    loadRecommendation(

                        response.analysis
                            .recommendation

                    );

                }

            }


            /*
            --------------------------------------------------
            Store result
            --------------------------------------------------
            */

            if(
                response.analysis
            ){

                analysisResult =
                    response.analysis;


                if(
                    typeof Dashboard !==
                    "undefined"
                ){

                    Dashboard.analysis =
                        response.analysis;

                }


                if(
                    typeof storeAnalysis ===
                    "function"
                ){

                    storeAnalysis(
                        response.analysis
                    );

                }

            }


            /*
            --------------------------------------------------
            Save selected configuration
            --------------------------------------------------
            */

            if(
                response.analysis &&
                response.analysis.configuration
            ){

                analysisConfig =
                    Object.assign(

                        analysisConfig,

                        response.analysis
                            .configuration

                    );

            }


            /*
            --------------------------------------------------
            Complete
            --------------------------------------------------
            */

            updateLoadingProgress(

                100,

                response.message || "Analysis finished."

            );


            /*
            --------------------------------------------------
            Hide configuration
            --------------------------------------------------
            */

            $("#analysisConfiguration")
                .slideUp();


            /*
            --------------------------------------------------
            Update status
            --------------------------------------------------
            */

            const benchmarkResults = response.analysis?.benchmark_results || [];
            const failedBenchmarks = benchmarkResults
                .filter(function(benchmark){ return Boolean(benchmark.error); }).length;
            const successfulBenchmarks = benchmarkResults.length - failedBenchmarks;
            const datasetStatus = $("#datasetStatus").removeClass();
            if(failedBenchmarks > 0 && successfulBenchmarks === 0){
                datasetStatus.addClass("badge bg-danger").text("No Compatible Models");
            } else if(failedBenchmarks > 0){
                datasetStatus.addClass("badge bg-warning text-dark").text("Analysis Complete with Errors");
            } else if(successfulBenchmarks === 0){
                datasetStatus.addClass("badge bg-warning text-dark").text("No Benchmark Results");
            } else {
                datasetStatus.addClass("badge bg-success").text("Analysis Complete");
            }


            /*
            --------------------------------------------------
            Close loading modal
            --------------------------------------------------
            */

            setTimeout(function(){

                hideLoadingModal();

                if(typeof window.showWorkflowPage === "function"){

                    window.showWorkflowPage("results");

                }

            }, 600);


            console.log(
                "Configured analysis completed."
            );

        },


        /*
        ------------------------------------------------------
        Error
        ------------------------------------------------------
        */

        error:function(xhr){
            console.error(
                "Configured analysis error:",
                xhr.responseText
            );


            let message =
                "Unable to run analysis.";


            /*
            --------------------------------------------------
            Try to read PHP JSON response
            --------------------------------------------------
            */

            try{

                const response =
                    JSON.parse(
                        xhr.responseText
                    );


                if(
                    response.message
                ){

                    message =
                        response.message;

                }

            }
            catch(e){

                console.error(
                    "Unable to parse error response:",
                    e
                );

            }


            showAnalysisError(
                message
            );

        }

    });

}

function showAnalysisError(message){
    const modalElement = document.getElementById("loadingModal");

    if(modalElement && modalElement.classList.contains("show")){
        modalElement.addEventListener("hidden.bs.modal", function(){
            showError(message);
        }, {once: true});
        hideLoadingModal();
        return;
    }

    hideLoadingModal();
    showError(message);
}


/*
|--------------------------------------------------------------------------
| Update Variable Count
|--------------------------------------------------------------------------
*/

function updateSelectedVariableCount(){

    const xCount =
        ($("#xVariables").val() || [])
            .length;


    const yCount =
        ($("#yVariables").val() || [])
            .length;


    $("#xVariableCount")
        .text(
            xCount
        );


    $("#yVariableCount")
        .text(
            yCount
        );


    /*
    ----------------------------------------------------------
    Optional labels
    ----------------------------------------------------------
    */

    $("#selectedXCount")
        .text(
            xCount
        );


    $("#selectedYCount")
        .text(
            yCount
        );

}


/*
|--------------------------------------------------------------------------
| Update Algorithm Status
|--------------------------------------------------------------------------
*/

function updateAlgorithmOptions(){
    const category = $("#algorithmCategory").val();
    const algorithms = algorithmOptions[category] || [];
    const selector = $("#algorithm").empty();

    algorithms.forEach(function(algorithm){
        selector.append($("<option>", {value: algorithm, text: algorithm}));
    });

    selector.val([]);
}


function updateCategoryFields(){
    const activeCategoryNeedsTarget = !featureOnlyCategories.has($("#algorithmCategory").val());
    const selectedModelsNeedTarget = analysisConfig.algorithms.some(function(model){
        return !featureOnlyCategories.has(model.category);
    });
    const requiresTarget = activeCategoryNeedsTarget || selectedModelsNeedTarget;
    $("#yVariables").closest(".col-lg-6").toggle(requiresTarget);
}


function updateAlgorithmStatus(){
    const selectedCount = analysisConfig.algorithms.length;
    $("#algorithmStatus").text(
        selectedCount + " selected; choose 1-5 total"
    );
    $("#selectedAlgorithmCount").text(selectedCount);
}


function addSelectedAlgorithms(){
    const category = $("#algorithmCategory").val();
    const selected = $("#algorithm").val() || [];
    const existing = new Set(analysisConfig.algorithms.map(function(model){
        return model.category + "\u0000" + model.algorithm;
    }));
    const additions = selected.filter(function(algorithm){
        return !existing.has(category + "\u0000" + algorithm);
    });

    if(analysisConfig.algorithms.length + additions.length > 5){
        showWarning("You can select up to 5 models total.");
        return;
    }

    additions.forEach(function(algorithm){
        analysisConfig.algorithms.push({category: category, algorithm: algorithm});
    });
    renderSelectedAlgorithmModels();
    updateCategoryFields();
    updateAlgorithmStatus();
}


function renderSelectedAlgorithmModels(){
    const container = $("#selectedAlgorithmModels").empty();
    const categories = $("#algorithmCategory option").map(function(){
        return {value: this.value, label: $(this).text()};
    }).get();

    if(analysisConfig.algorithms.length === 0){
        container.append($("<small>", {class: "text-muted", text: "No models selected."}));
        return;
    }

    analysisConfig.algorithms.forEach(function(model, index){
        const categoryId = "selectedModelCategory-" + index;
        const algorithmId = "selectedModelAlgorithm-" + index;
        const row = $("<div>", {
            class: "d-flex align-items-center gap-2",
            "data-model-index": index
        });
        const categorySelect = $("<select>", {
            class: "form-select form-select-sm selected-model-category",
            id: categoryId,
            name: "selected_models[" + index + "][category]",
            "aria-label": "Model category"
        });
        const algorithmSelect = $("<select>", {
            class: "form-select form-select-sm selected-model-algorithm",
            id: algorithmId,
            name: "selected_models[" + index + "][algorithm]",
            "aria-label": "Algorithm model"
        });
        const categoryLabel = $("<label>", {
            class: "visually-hidden",
            for: categoryId,
            text: "Category for " + model.algorithm
        });
        const algorithmLabel = $("<label>", {
            class: "visually-hidden",
            for: algorithmId,
            text: "Algorithm for " + model.category
        });
        const removeButton = $("<button>", {
            type: "button",
            class: "btn btn-outline-danger btn-sm btn-remove-selected-model",
            title: "Remove model",
            "aria-label": "Remove " + model.algorithm
        }).append($("<i>", {class: "bi bi-trash"}));

        categories.forEach(function(category){
            categorySelect.append($("<option>", {
                value: category.value,
                text: category.label
            }));
        });
        (algorithmOptions[model.category] || []).forEach(function(algorithm){
            algorithmSelect.append($("<option>", {
                value: algorithm,
                text: algorithm
            }));
        });

        categorySelect.val(model.category);
        algorithmSelect.val(model.algorithm);
        row.append(categoryLabel, categorySelect, algorithmLabel, algorithmSelect, removeButton);
        container.append(row);
    });
}


/*
|--------------------------------------------------------------------------
| Reset Analysis Configuration
|--------------------------------------------------------------------------
*/

function resetAnalysisConfiguration(){

    analysisConfig = {

        dataset_id: null,

        file_path: "",

        columns: [],

        x_variables: [],

        y_variables: [],

        category: "classification",

        algorithms: [],

        task: "",

        selection_mode: "automatic"

    };


    $("input[name=\"selectionMode\"][value=\"automatic\"]").prop("checked", true);
    updateSelectionModeUI();

    $("#xVariables").val([]);

    $("#yVariables").val([]);

    $("#algorithm").val("");

    $("#algorithmCategory").val("classification");
    updateAlgorithmOptions();
    updateCategoryFields();


    updateSelectedVariableCount();

    updateAlgorithmStatus();

}


/*
|--------------------------------------------------------------------------
| Export configuration for other modules
|--------------------------------------------------------------------------
*/

Object.defineProperty(window, "SimuRithmAnalysisConfig", {
    configurable: true,
    get: function(){ return analysisConfig; }
});