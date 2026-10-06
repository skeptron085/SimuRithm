/*
|--------------------------------------------------------------------------
| SimuRithm Upload Module
|--------------------------------------------------------------------------
| Release 1.3
|--------------------------------------------------------------------------
| Workflow:
|
| Upload
|   ↓
| Initial Analysis
|   ↓
| Statistics
|   ↓
| Dataset Preview
|   ↓
| NLP Header Analysis
|   ↓
| User Variable/Algorithm Selection
|   ↓
| run_analysis.php
|
|--------------------------------------------------------------------------
*/

"use strict";


/*
|--------------------------------------------------------------------------
| Global Variables
|--------------------------------------------------------------------------
*/

let selectedFile = null;

let analysisResult = null;

let selectedDatasetPath = "";

let selectedDatasetId = null;


/*
|--------------------------------------------------------------------------
| Initialize
|--------------------------------------------------------------------------
*/

$(document).ready(function(){

    initializeUpload();

});


function initializeUpload(){

    browseButton();

    fileSelector();

    dragAndDrop();

    uploadButton();

    clearButton();

    $("#btnContinueToConfigure").on("click", function(){

        if(!selectedDatasetId || !selectedDatasetPath){

            showWarning("Upload a dataset before configuring an analysis.");

            return;

        }

        if(typeof showAnalysisConfiguration !== "function"){

            showError("The analysis configuration interface could not be loaded.");

            return;

        }

        if(typeof window.showWorkflowPage === "function"){

            window.showWorkflowPage("configure");

        }

        showAnalysisConfiguration({
            dataset_id: selectedDatasetId,
            file_path: selectedDatasetPath,
            task: analysisResult?.task
                || analysisResult?.task_type
                || analysisResult?.detected_task
                || analysisResult?.inferred_task
                || analysisResult?.dataset?.task
                || analysisResult?.dataset?.task_type
                || analysisResult?.dataset?.detected_task
                || analysisResult?.dataset?.inferred_task
                || "",
            analysis: analysisResult || {}
        });

    });

    $("#simurithmSidebar [data-workflow-page='configure']").on("click", function(event){

        if(!selectedDatasetId || !selectedDatasetPath){

            event.preventDefault();

            showWarning("Upload and review a dataset before configuring an analysis.");

            window.showWorkflowPage("upload");

            return;

        }

        $("#btnContinueToConfigure").trigger("click");

    });

}


/*
|--------------------------------------------------------------------------
| Browse
|--------------------------------------------------------------------------
*/

function browseButton(){

    $("#btnBrowse").click(function(){

        $("#datasetFile").click();

    });

}


/*
|--------------------------------------------------------------------------
| File Selection
|--------------------------------------------------------------------------
*/

function fileSelector(){

    $("#datasetFile").change(function(){

        if(this.files.length === 0){

            return;

        }

        selectedFile = this.files[0];

        displayFileInformation(
            selectedFile
        );

    });

}


/*
|--------------------------------------------------------------------------
| Drag & Drop
|--------------------------------------------------------------------------
*/

function dragAndDrop(){

    $("#uploadArea").on(
        "dragover",
        function(e){

            e.preventDefault();

            $(this).addClass("dragover");

        }
    );


    $("#uploadArea").on(
        "dragleave",
        function(){

            $(this).removeClass(
                "dragover"
            );

        }
    );


    $("#uploadArea").on(
        "drop",
        function(e){

            e.preventDefault();

            $(this).removeClass(
                "dragover"
            );

            const files =
                e.originalEvent
                 .dataTransfer
                 .files;


            if(files.length === 0){

                return;

            }


            selectedFile = files[0];

            displayFileInformation(
                selectedFile
            );

        }
    );

}


/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
*/

function displayFileInformation(file){

    $("#fileInformation").slideDown();

    $("#fileName").text(
        file.name
    );


    const extension =
        file.name
            .split(".")
            .pop()
            .toUpperCase();


    $("#fileExtension").text(
        extension
    );


    $("#fileSize").text(
        formatBytes(file.size)
    );


    $("#uploadStatus")

        .removeClass()

        .addClass(
            "badge bg-info"
        )

        .text("Ready");


    $("#datasetStatus")

        .removeClass()

        .addClass(
            "badge bg-success"
        )

        .text(
            "Dataset Ready"
        );

}


/*
|--------------------------------------------------------------------------
| Upload Button
|--------------------------------------------------------------------------
*/

function uploadButton(){

    $("#btnUpload").click(function(){

        if(selectedFile === null){

            showWarning("Please choose a dataset first.");

            return;

        }

        uploadDataset();

    });

}


function uploadDataset(){

    let formData = new FormData();

    formData.append("dataset", selectedFile);

    showLoadingModal("Uploading dataset...");

    $.ajax({

        url: "ajax/upload.php",
        type:
            "POST",

        data:
            formData,

        processData:
            false,

        contentType:
            false,


        xhr:function(){

            let xhr =
                new window.XMLHttpRequest();


            xhr.upload.addEventListener(

                "progress",

                function(e){

                    if(
                        e.lengthComputable
                    ){

                        let percent =
                            Math.round(
                                (
                                    e.loaded /
                                    e.total
                                ) * 100
                            );


                        $("#uploadProgressContainer")
                            .show();


                        $("#uploadProgress")

                            .css(
                                "width",
                                percent + "%"
                            )

                            .text(
                                percent + "%"
                            );


                        updateLoadingProgress(

                            percent,

                            "Uploading dataset..."

                        );

                    }

                }

            );

            xhr.upload.addEventListener(

                "load",

                function(){

                    updateLoadingProgress(

                        100,

                        "Upload complete. Analyzing dataset and detecting headers; large datasets may take longer."

                    );

                }

            );


            return xhr;

        },


        success:function(response){

            console.log(
                "Upload response:",
                response
            );


            /*
            |--------------------------------------------------------------------------
            | Check Upload Success
            |--------------------------------------------------------------------------
            */

            if(
                !response ||
                response.success !== true
            ){

                hideLoadingModal();


                $("#uploadStatus")

                    .removeClass()

                    .addClass(
                        "badge bg-danger"
                    )

                    .text(
                        "Upload Failed"
                    );


                showError(

                    response?.message ||

                    "Dataset upload failed."

                );


                console.error(
                    "Upload Error:",
                    response
                );


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | STEP 1
            | Upload Complete
            |--------------------------------------------------------------------------
            */

            completeLoadingStep(
                "stepUpload"
            );


            updateLoadingProgress(

                20,

                "Computing statistics..."

            );


            /*
            |--------------------------------------------------------------------------
            | STEP 2
            | Statistics
            |--------------------------------------------------------------------------
            */

            if(

                response.analysis &&

                response.analysis.statistics

            ){

                loadStatistics(

                    response.analysis.statistics

                );

            }


            completeLoadingStep(
                "stepStatistics"
            );


            updateLoadingProgress(

                40,

                "Preparing dataset preview..."

            );


            /*
            |--------------------------------------------------------------------------
            | STEP 3
            | Dataset Preview
            |--------------------------------------------------------------------------
            */

            if(

                response.analysis &&

                Array.isArray(
                    response.analysis.preview
                )

            ){

                loadPreview(

                    response.analysis.preview

                );

            }


            /*
            |--------------------------------------------------------------------------
            | STEP 4
            | NLP Header Analysis
            |--------------------------------------------------------------------------
            */

            updateLoadingProgress(

                60,

                "Analyzing dataset headers..."

            );


            if(

                response.analysis &&

                response.analysis.header_analysis

            ){

                loadHeaderAnalysis(

                    response.analysis.header_analysis

                );

            }


            completeLoadingStep(
                "stepNLP"
            );


            /*
            |--------------------------------------------------------------------------
            | Dataset Profile
            |--------------------------------------------------------------------------
            */

            if(
                typeof loadDatasetProfile ===
                "function"
            ){

                const profileResponse = {

                    filename:
                        selectedFile
                        ?
                        selectedFile.name
                        :
                        response.filename || "-",


                    size:
                        selectedFile
                        ?
                        selectedFile.size
                        :
                        Number(
                            response.size || 0
                        ),


                    analysis:
                        response.analysis || {}

                };


                loadDatasetProfile(
                    profileResponse
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Store Initial Analysis
            |--------------------------------------------------------------------------
            */

            analysisResult =
                response.analysis || {};


            if(
                typeof storeAnalysis ===
                "function"
            ){

                storeAnalysis(
                    response.analysis
                );

            }


            if(
                typeof Dashboard !==
                "undefined"
            ){

                Dashboard.analysis =
                    response.analysis;

            }


            /*
            |--------------------------------------------------------------------------
            | Store Dataset Path
            |--------------------------------------------------------------------------
            */

            selectedDatasetPath =

                response.path ||

                response.file_path ||

                response.stored_path ||

                response.stored_name ||

                "";


            /*
            |--------------------------------------------------------------------------
            | Store Dataset ID
            |--------------------------------------------------------------------------
            */

            selectedDatasetId =

                response.dataset_id ||

                response.analysis?.dataset_id ||

                null;


            /*
            |--------------------------------------------------------------------------
            | Dataset Status
            |--------------------------------------------------------------------------
            */

            if(
                typeof datasetLoaded ===
                "function"
            ){

                datasetLoaded(
                    selectedDatasetPath
                );

            }


            /*
            |--------------------------------------------------------------------------
            | Mark Initial Analysis Complete
            |--------------------------------------------------------------------------
            */

            updateLoadingProgress(

                100,

                "Dataset ready for analysis selection."

            );


            $("#uploadStatus")

                .removeClass()

                .addClass(
                    "badge bg-success"
                )

                .text(
                    "Dataset Ready"
                );


            $("#datasetStatus")

                .removeClass()

                .addClass(
                    "badge bg-success"
                )

                .text(
                    "Ready for Analysis"
                );


            /*
            |--------------------------------------------------------------------------
            | DO NOT:
            |
            | - run correlation
            | - run benchmark
            | - generate recommendation
            | - run feature importance
            | - run tuning
            |
            | Those processes now happen AFTER the user selects
            | X variables, Y variables, and algorithm.
            |--------------------------------------------------------------------------
            */


            /*
            |--------------------------------------------------------------------------
            | Show Variable Selection Interface
            |--------------------------------------------------------------------------
            */

            setTimeout(function(){

                hideLoadingModal();


                showAnalysisSelection();


            }, 700);


            /*
            |--------------------------------------------------------------------------
            | Update Last Analysis
            |--------------------------------------------------------------------------
            */

            if(
                typeof updateLastAnalysis ===
                "function"
            ){

                updateLastAnalysis();

            }


            console.log(
                "Initial dataset analysis completed."
            );


            console.log(
                "Waiting for user variable and algorithm selection."
            );

        },


        error:function(xhr){

            hideLoadingModal();


            $("#uploadStatus")

                .removeClass()

                .addClass(
                    "badge bg-danger"
                )

                .text(
                    "Failed"
                );


            showError(

                "Unable to upload dataset."

            );


            console.error(
                xhr.responseText
            );

        }

    });

}


/*
|--------------------------------------------------------------------------
| Show Analysis Selection Interface
|--------------------------------------------------------------------------
|
| This interface should contain:
|
|   X Variables
|   Y Variables
|   Algorithm
|   Run Analysis button
|
|--------------------------------------------------------------------------
*/

function showAnalysisSelection(){

    /*
    |--------------------------------------------------------------------------
    | If your HTML selection card exists
    |--------------------------------------------------------------------------
    */

    $("#btnContinueToConfigure").prop("disabled", false);

    if(typeof window.showWorkflowPage === "function"){

        window.showWorkflowPage("explore");

    }


    /*
    |--------------------------------------------------------------------------
    | Populate variable selectors
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Scroll user to selection area
    |--------------------------------------------------------------------------
    */

    if(
        $("#analysisSelectionCard").length
    ){

        $("html, body").animate({

            scrollTop:
                $("#analysisSelectionCard")
                    .offset()
                    .top - 80

        }, 500);

    }


    showInfo(

        "Dataset analyzed. Review the NLP header roles, then continue to choose variables and an algorithm."

    );

}


/*
|--------------------------------------------------------------------------
| Populate Variable Selectors
|--------------------------------------------------------------------------
*/

function populateVariableSelectors(){

    $("#btnContinueToConfigure").prop("disabled", false);

    if(typeof window.showWorkflowPage === "function"){

        window.showWorkflowPage("explore");

    }

    const xSelect = $("#xVariables");
    const ySelect = $("#yVariables");
    const headers = Array.isArray(analysisResult?.header_analysis)
        ? analysisResult.header_analysis
        : [];

    xSelect.empty();
    ySelect.empty();

    headers.forEach(function(item){
        const column = item.column || item.name || item.column_name || "";

        if(!column){
            return;
        }

        const optionX = $("<option></option>").val(column).text(column);
        const optionY = $("<option></option>")

        optionY.val(column)

                .text(column);


        xSelect.append(
            optionX
        );


        ySelect.append(
            optionY
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Initialize / Refresh Select2
    |--------------------------------------------------------------------------
    */

    if(
        $.fn.select2
    ){

        if(
            xSelect.hasClass(
                "select2-hidden-accessible"
            )
        ){

            xSelect.select2(
                "destroy"
            );

        }


        if(
            ySelect.hasClass(
                "select2-hidden-accessible"
            )
        ){

            ySelect.select2(
                "destroy"
            );

        }


        xSelect.select2({

            placeholder:
                "Select X variables",

            width:
                "100%"

        });

        ySelect.select2({

            placeholder:
                "Select Y variables",

            width:
                "100%"

        });

    }

}


function runSelectedAnalysis(){

    const requestData = {

        dataset_id: selectedDatasetId,

        file_path: selectedDatasetPath,

        x_variables: $("#xVariables").val() || [],

        y_variables: $("#yVariables").val() || [],

        selected_algorithm: $("#algorithm").val() || ""

    };

    showLoadingModal("Running selected analysis...");

    $.ajax({

        url: "ajax/run_analysis.php",

        type: "POST",

        contentType: "application/json",

        dataType: "json",

        data:
            JSON.stringify(
                requestData
            ),


        beforeSend:function(){

            updateLoadingProgress(

                25,

                "Sending selections to SimuRithm..."

            );

        },


        success:function(response){

            console.log(
                "Run analysis response:",
                response
            );


            if(
                !response ||
                response.success !== true
            ){

                hideLoadingModal();


                showError(

                    response?.message ||

                    "Selected analysis failed."

                );


                console.error(
                    "Run Analysis Error:",
                    response
                );


                return;

            }


            updateLoadingProgress(

                50,

                "Processing correlation and benchmark..."

            );


            /*
            |--------------------------------------------------------------------------
            | Get Analysis Result
            |--------------------------------------------------------------------------
            */

            const analysis =

                response.analysis ||

                response;


            analysisResult =
                analysis;


            /*
            |--------------------------------------------------------------------------
            | Correlation
            |--------------------------------------------------------------------------
            */

            if(
                analysis.correlation
            ){

                loadCorrelation(

                    analysis.correlation

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Strongest Correlations
            |--------------------------------------------------------------------------
            */

            if(
                Array.isArray(
                    analysis.strongest_correlations
                )
            ){

                loadStrongCorrelations(

                    analysis.strongest_correlations

                );

            }


            completeLoadingStep(
                "stepCorrelation"
            );


            updateLoadingProgress(

                65,

                "Displaying benchmark results..."

            );


            /*
            |--------------------------------------------------------------------------
            | Benchmark
            |--------------------------------------------------------------------------
            */

            if(
                Array.isArray(
                    analysis.benchmark_results
                )
            ){

                loadBenchmark(

                    analysis.benchmark_results

                );


                loadBenchmarkCharts(

                    analysis.benchmark_results

                );

            }


            completeLoadingStep(
                "stepBenchmark"
            );


            updateLoadingProgress(

                80,

                "Generating recommendation..."

            );


            /*
            |--------------------------------------------------------------------------
            | Recommendation
            |--------------------------------------------------------------------------
            */

            if(
                analysis.recommendation
            ){

                loadRecommendation(

                    analysis.recommendation

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Feature Importance
            |--------------------------------------------------------------------------
            */

            if(
                Array.isArray(
                    analysis.feature_importance
                )
            ){

                loadFeatureImportance(

                    analysis.feature_importance

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Hyperparameter Tuning
            |--------------------------------------------------------------------------
            */

            if(
                analysis.tuning
            ){

                loadTuning(

                    analysis.tuning

                );

            }


            /*
            |--------------------------------------------------------------------------
            | Store Result
            |--------------------------------------------------------------------------
            */

            if(
                typeof storeAnalysis ===
                "function"
            ){

                storeAnalysis(
                    analysis
                );

            }


            if(
                typeof Dashboard !==
                "undefined"
            ){

                Dashboard.analysis =
                    analysis;

            }


            /*
            |--------------------------------------------------------------------------
            | Final Progress
            |--------------------------------------------------------------------------
            */

            completeLoadingStep(
                "stepRecommendation"
            );


            updateLoadingProgress(

                100,

                "Selected analysis completed successfully."

            );


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            $("#uploadStatus")

                .removeClass()

                .addClass(
                    "badge bg-success"
                )

                .text(
                    "Analysis Complete"
                );


            $("#datasetStatus")

                .removeClass()

                .addClass(
                    "badge bg-success"
                )

                .text(
                    "Analysis Complete"
                );


            /*
            |--------------------------------------------------------------------------
            | Hide Selection Card
            |--------------------------------------------------------------------------
            */

            /*
            | Keep the card visible so the user can see what
            | variables and algorithm were selected.
            */

            $("#analysisSelectionCard").show();


            /*
            |--------------------------------------------------------------------------
            | Close Loading
            |--------------------------------------------------------------------------
            */

            setTimeout(function(){

                hideLoadingModal();

            }, 700);


            /*
            |--------------------------------------------------------------------------
            | Scroll To Results
            |--------------------------------------------------------------------------
            */

            setTimeout(function(){

                const benchmark =
                    $("#benchmarkTable");


                if(
                    benchmark.length
                ){

                    $("html, body").animate({

                        scrollTop:
                            benchmark.offset().top - 80

                    }, 600);

                }

            }, 800);


            console.log(
                "Selected analysis completed successfully."
            );

        },


        error:function(xhr){

            hideLoadingModal();


            let message =
                "Unable to run selected analysis.";


            try{

                const errorResponse =
                    JSON.parse(
                        xhr.responseText
                    );


                if(
                    errorResponse.message
                ){

                    message =
                        errorResponse.message;

                }

            }
            catch(e){

                console.error(e);

            }


            showError(
                message
            );


            console.error(
                "run_analysis.php error:",
                xhr.responseText
            );

        }

    });

}


/*
|--------------------------------------------------------------------------
| Clear
|--------------------------------------------------------------------------
*/

function clearButton(){

    $("#btnClear").click(function(){

        selectedFile = null;

        analysisResult = null;

        selectedDatasetPath = "";

        selectedDatasetId = null;


        $("#datasetFile").val("");


        $("#fileInformation").hide();


        $("#uploadProgressContainer")
            .hide();


        $("#uploadProgress")

            .css(
                "width",
                "0%"
            )

            .text(
                "0%"
            );


        $("#uploadStatus")

            .removeClass()

            .addClass(
                "badge bg-warning"
            )

            .text(
                "Waiting"
            );


        $("#datasetStatus")

            .removeClass()

            .addClass(
                "badge bg-secondary"
            )

            .text(
                "No Dataset"
            );


        /*
        |--------------------------------------------------------------------------
        | Hide Analysis Selection
        |--------------------------------------------------------------------------
        */

        $("#analysisSelectionCard")
            .hide();


        /*
        |--------------------------------------------------------------------------
        | Clear Selectors
        |--------------------------------------------------------------------------
        */

        $("#xVariables").empty();

        $("#yVariables").empty();

        $("#selectedAlgorithm")
            .val("");


        /*
        |--------------------------------------------------------------------------
        | Clear Dashboard
        |--------------------------------------------------------------------------
        */

        clearDashboard();

    });

}