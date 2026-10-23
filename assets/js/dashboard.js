/*
|--------------------------------------------------------------------------
| SimuRithm Dashboard
|--------------------------------------------------------------------------
| Frontend Release 1.1
|--------------------------------------------------------------------------
| Global Dashboard Controller
|--------------------------------------------------------------------------
*/

"use strict";

/*
|--------------------------------------------------------------------------
| Global Variables
|--------------------------------------------------------------------------
*/

let benchmarkChart = null;

let benchmarkResults = [];

/*
|--------------------------------------------------------------------------
| Latest Analysis
|--------------------------------------------------------------------------
*/

let latestAnalysis = null;


/*
|--------------------------------------------------------------------------
| Store Latest Analysis
|--------------------------------------------------------------------------
*/

function storeAnalysis(data) {

    if (!data) {

        console.warn(
            "No analysis data received."
        );

        return;

    }

    latestAnalysis = data;

    /*
    ----------------------------------------------------------
    Keep benchmark results synchronized
    ----------------------------------------------------------
    */

    if (
        Array.isArray(
            data.benchmark_results
        )
    ) {

        benchmarkResults =
            data.benchmark_results;

    }

    console.log(
        "Latest analysis stored:",
        latestAnalysis
    );

}

const Dashboard = {

    chart: null,

    apiOnline: false,

    datasetLoaded: false,

    datasetPath: "",

    previewRows: 10

};

/*
|--------------------------------------------------------------------------
| Initialize Dashboard
|--------------------------------------------------------------------------
*/

$(document).ready(function () {

    initializeDashboard();

});


function initializeDashboard() {

    initializeClock();

    initializeButtons();

    initializeTables();

    checkFlaskStatus();

    console.log("SimuRithm Dashboard Initialized");

}

/*
|--------------------------------------------------------------------------
| Live Clock
|--------------------------------------------------------------------------
*/

function initializeClock() {

    updateClock();

    setInterval(updateClock, 1000);

}

function updateClock() {

    const now = new Date();

    $("#currentDate").text(

        now.toLocaleDateString()

    );

    $("#currentTime").text(

        now.toLocaleTimeString()

    );

}

/*
|--------------------------------------------------------------------------
| Navbar Buttons
|--------------------------------------------------------------------------
*/

function initializeButtons() {

    $("#btnRefresh").on("click", function () {

        location.reload();

    });

    $("#btnFullscreen").on("click", function () {

        toggleFullscreen();

    });

}

/*
|--------------------------------------------------------------------------
| Fullscreen
|--------------------------------------------------------------------------
*/

function toggleFullscreen() {

    if (!document.fullscreenElement) {

        document.documentElement.requestFullscreen();

    }
    else {

        document.exitFullscreen();

    }

}

/*
|--------------------------------------------------------------------------
| DataTables
|--------------------------------------------------------------------------
*/

function initializeTables() {

    /*
    ----------------------------------------------------------
    Dynamic DataTables
    ----------------------------------------------------------
    Tables are initialized by their respective load functions
    after data has been received from Flask.

    This prevents DataTables from initializing empty/dynamic
    tables before their columns are available.
    ----------------------------------------------------------
    */

    console.log("Dynamic DataTables will initialize after analysis.");

}

function labelDataTableSearch(tableId, label) {

    const searchInput = $(`#${tableId}_wrapper .dataTables_filter input`);

    searchInput.attr({
        id: `${tableId}Search`,
        name: `${tableId}_search`,
        "aria-label": label
    });

    searchInput.closest("label").attr("for", `${tableId}Search`);

}

/*
|--------------------------------------------------------------------------
| Flask Health Check
|--------------------------------------------------------------------------
*/

function checkFlaskStatus() {

    $.ajax({

        url: "ajax/health.php",

        type: "GET",

        dataType: "json",

        success: function (response) {

            Dashboard.apiOnline = true;

            updateApiBadge(true);

        },

        error: function () {

            Dashboard.apiOnline = false;

            updateApiBadge(false);

        }

    });

}

/*
|--------------------------------------------------------------------------
| API Badge
|--------------------------------------------------------------------------
*/

function updateApiBadge(status) {

    if (status) {

        $("#flaskStatus")

            .removeClass()

            .addClass("badge bg-success")

            .text("API Online");

        $("#footerApiStatus")

            .removeClass()

            .addClass("badge bg-success")

            .text("API Online");

    }

    else {

        $("#flaskStatus")

            .removeClass()

            .addClass("badge bg-danger")

            .text("API Offline");

        $("#footerApiStatus")

            .removeClass()

            .addClass("badge bg-danger")

            .text("API Offline");

    }

}

/*
|--------------------------------------------------------------------------
| Loading Overlay
|--------------------------------------------------------------------------
*/

function showLoading(message = "Loading...") {

    $("#loadingText").text(message);

    $(".loading-overlay").fadeIn(200);

}

function hideLoading() {

    $(".loading-overlay").fadeOut(200);

}

/*
|--------------------------------------------------------------------------
| SweetAlert Helpers
|--------------------------------------------------------------------------
*/

function showSuccess(message) {

    Swal.fire({

        icon: "success",

        title: "Success",

        text: message,

        timer: 1800,

        showConfirmButton: false

    });

}

function showError(message) {

    Swal.fire({

        icon: "error",

        title: "Error",

        text: message

    });

}

function showWarning(message) {

    Swal.fire({

        icon: "warning",

        title: "Warning",

        text: message

    });

}

function showInfo(message) {

    Swal.fire({

        icon: "info",

        title: "Information",

        text: message

    });

}

/*
|--------------------------------------------------------------------------
| Dataset Status
|--------------------------------------------------------------------------
*/

function datasetLoaded(path) {

    Dashboard.datasetLoaded = true;

    Dashboard.datasetPath = path;

}

function clearDashboard() {

    Dashboard.datasetLoaded = false;

    Dashboard.datasetPath = "";

    /*
    ----------------------------------------------------------
    Clear Preview DataTable
    ----------------------------------------------------------
    */

    if($.fn.DataTable.isDataTable("#previewTable")){

        $("#previewTable")
            .DataTable()
            .clear()
            .destroy();

    }

    $("#previewHead").empty();

    $("#previewBody").empty();

    $("#previewRowsBadge").text("0 Rows");

    $("#previewEmptyMessage").show();

}

/*
|--------------------------------------------------------------------------
| Last Analysis
|--------------------------------------------------------------------------
*/

function updateLastAnalysis() {

    const now = new Date();

    $("#lastAnalysis").text(

        now.toLocaleString()

    );

}

/*
|--------------------------------------------------------------------------
| Number Formatter
|--------------------------------------------------------------------------
*/

function numberFormat(value) {

    return Number(value).toLocaleString();

}

/*
|--------------------------------------------------------------------------
| File Size Formatter
|--------------------------------------------------------------------------
*/

function formatBytes(bytes) {

    if (bytes === 0) {

        return "0 Bytes";

    }

    const k = 1024;

    const sizes = [

        "Bytes",

        "KB",

        "MB",

        "GB",

        "TB"

    ];

    const i = Math.floor(

        Math.log(bytes) / Math.log(k)

    );

    return parseFloat(

        (bytes / Math.pow(k, i)).toFixed(2)

    ) + " " + sizes[i];

}

/*
|--------------------------------------------------------------------------
| Export (Placeholder)
|--------------------------------------------------------------------------
*/

function exportPDF() {

    showInfo("Export PDF module will be added.");

}

function exportExcel() {

    showInfo("Export Excel module will be added.");

}

/*
|--------------------------------------------------------------------------
| Loading Modal
|--------------------------------------------------------------------------
*/

let loadingModal = null;

function showLoadingModal(message = "Preparing analysis...") {

    if (!loadingModal) {

        loadingModal = new bootstrap.Modal(

            document.getElementById("loadingModal")

        );

    }

    resetLoadingProgress();

    $("#loadingText").text(message);

    loadingModal.show();

}

function hideLoadingModal() {

    if (loadingModal) {

        loadingModal.hide();

    }

}

/*
|--------------------------------------------------------------------------
| Progress Bar
|--------------------------------------------------------------------------
*/

function updateLoadingProgress(percent, message = "") {

    $("#loadingProgress")

        .css("width", percent + "%")

        .text(percent + "%");

    if (message !== "") {

        $("#loadingText").text(message);

    }

}

/*
|--------------------------------------------------------------------------
| Update Step Status
|--------------------------------------------------------------------------
*/

function completeLoadingStep(stepId) {

    $("#" + stepId)

        .removeClass("text-secondary")

        .addClass("text-success")

        .html("✔ " + $("#" + stepId).text().replace("⏳ ", ""));

}

function resetLoadingProgress() {

    $("#loadingProgress")

        .css("width", "0%")

        .text("0%");

    $("#loadingText").text("Preparing analysis...");

    const steps = [

        "stepUpload",

        "stepStatistics",

        "stepNLP",

        "stepCorrelation",

        "stepBenchmark",

        "stepRecommendation"

    ];

    steps.forEach(function(step){

        $("#" + step)

            .removeClass("text-success")

            .addClass("text-secondary");

        $("#" + step).html(

            "⏳ " +

            $("#" + step).text()

                .replace("✔ ","")

                .replace("⏳ ","")

        );

    });

}

/*
|--------------------------------------------------------------------------
| Statistics Cards
|--------------------------------------------------------------------------
*/

function loadStatistics(stats){

    if(!stats){

        return;

    }

    $("#statRows").text(

        numberFormat(stats.rows)

    );

    $("#statColumns").text(

        numberFormat(stats.columns)

    );

    $("#statMissing").text(

        numberFormat(stats.missing_values)

    );

    $("#statDuplicate").text(

        numberFormat(stats.duplicate_rows)

    );

    $("#statNumeric").text(

        numberFormat(stats.numeric_columns)

    );

    $("#statCategorical").text(

        numberFormat(stats.categorical_columns)

    );

    $("#statDatasetSize").text(

        stats.dataset_size

    );

    $("#statMemory").text(

        stats.memory_usage

    );

}

/*
|-------------------------------------------------------------------------- 
| Dataset Profile
|-------------------------------------------------------------------------- 
*/

function loadDatasetProfile(response){

    if(!response){

        console.warn(
            "No dataset profile response received."
        );

        return;

    }

    /*
    ----------------------------------------------------------
    File Information
    ----------------------------------------------------------
    */

    $("#profileDatasetName").text(

        response.filename || "-"

    );

    /*
    ----------------------------------------------------------
    Dataset Type
    ----------------------------------------------------------
    */

    if(response.filename){

        const extension =
            response.filename
                .split(".")
                .pop()
                .toUpperCase();

        $("#profileDatasetType").text(

            extension

        );

    }
    else{

        $("#profileDatasetType").text("-");

    }

    /*
    ----------------------------------------------------------
    Dataset File Size
    ----------------------------------------------------------
    */

    if(
        response.size !== undefined &&
        response.size !== null
    ){

        $("#profileDatasetSize").text(

            formatBytes(
                Number(response.size)
            )

        );

    }
    else{

        $("#profileDatasetSize").text("-");

    }

    /*
    ----------------------------------------------------------
    Analysis
    ----------------------------------------------------------
    */

    const analysis =
        response.analysis || {};

    /*
    ----------------------------------------------------------
    Statistics
    ----------------------------------------------------------
    */

    const statistics =
        analysis.statistics || {};

    $("#profileRows").text(

        numberFormat(
            statistics.rows ?? 0
        )

    );

    $("#profileColumns").text(

        numberFormat(
            statistics.columns ?? 0
        )

    );

    /*
    ----------------------------------------------------------
    Task
    ----------------------------------------------------------
    */

    const dataset =
        analysis.dataset || {};

    const task =
        dataset.task || "-";

    if(task !== "-"){

        $("#profileTask")

            .removeClass()

            .addClass(
                "badge bg-success"
            )

            .text(

                task.charAt(0).toUpperCase()
                +
                task.slice(1)

            );

    }
    else{

        $("#profileTask")

            .removeClass()

            .addClass(
                "badge bg-secondary"
            )

            .text("-");

    }

    /*
    ----------------------------------------------------------
    Suggested Target
    ----------------------------------------------------------
    */

    $("#profileTarget").text(

        dataset.target || "-"

    );

    /*
    ----------------------------------------------------------
    Recommended Algorithm
    ----------------------------------------------------------
    */

    const recommendation =
        analysis.recommendation || {};

    $("#profileAlgorithm").text(

        recommendation.best_algorithm || "-"

    );

    /*
    ----------------------------------------------------------
    Upload Time
    ----------------------------------------------------------
    */

    $("#profileUploadTime").text(

        new Date().toLocaleString()

    );

    /*
    ----------------------------------------------------------
    Status
    ----------------------------------------------------------
    */

    $("#profileStatus")

        .removeClass()

        .addClass(
            "badge bg-success"
        )

        .text("Analyzed");

}

/*
|--------------------------------------------------------------------------
| Dataset Preview
|--------------------------------------------------------------------------
*/

function loadPreview(preview){

    const table=$("#previewTable");

    const head=$("#previewHead");

    const body=$("#previewBody");

    const emptyMessage=$("#previewEmptyMessage");

    /*
    ----------------------------------------------------------
    Destroy Existing DataTable
    ----------------------------------------------------------
    */

    if($.fn.DataTable.isDataTable("#previewTable")){

        table.DataTable().clear().destroy();

    }

    /*
    ----------------------------------------------------------
    Clear Existing Table
    ----------------------------------------------------------
    */

    head.empty();

    body.empty();

    /*
    ----------------------------------------------------------
    Validate Preview
    ----------------------------------------------------------
    */

    if(!preview || preview.length===0){

        emptyMessage.show();

        $("#previewRowsBadge").text("0 Rows");

        return;

    }

    emptyMessage.hide();

    /*
    ----------------------------------------------------------
    Get Columns
    ----------------------------------------------------------
    */

    const columns=Object.keys(preview[0]);

    /*
    ----------------------------------------------------------
    Build Header
    ----------------------------------------------------------
    */

    let headerRow="<tr>";

    columns.forEach(function(column){

        headerRow+="<th>"+escapeHtml(column)+"</th>";

    });

    headerRow+="</tr>";

    head.html(headerRow);

    /*
    ----------------------------------------------------------
    Build Body
    ----------------------------------------------------------
    */

    preview.forEach(function(row){

        let tableRow="<tr>";

        columns.forEach(function(column){

            let value=row[column];

            if(value===null || value===undefined){

                value="";

            }

            tableRow+="<td>"+escapeHtml(String(value))+"</td>";

        });

        tableRow+="</tr>";

        body.append(tableRow);

    });

    /*
    ----------------------------------------------------------
    Rows Badge
    ----------------------------------------------------------
    */

    $("#previewRowsBadge").text(

        preview.length+" Rows"

    );

    /*
    ----------------------------------------------------------
    Initialize DataTables
    ----------------------------------------------------------
    */

    table.DataTable({

        pageLength:10,

        lengthMenu:[

            [10,25,50,100],

            [10,25,50,100]

        ],

        responsive:false,

        autoWidth:false,

        ordering:true,

        searching:true,

        info:true,

        language:{

            emptyTable:"No dataset preview available."

        }

    });

    labelDataTableSearch("previewTable", "Search dataset preview");

}

function escapeHtml(value){

    return value

        .replace(/&/g,"&amp;")

        .replace(/</g,"&lt;")

        .replace(/>/g,"&gt;")

        .replace(/"/g,"&quot;")

        .replace(/'/g,"&#039;");

}

/*
|-------------------------------------------------------------------------- 
| NLP Header Analysis
|-------------------------------------------------------------------------- 
*/

function loadHeaderAnalysis(headers){

    if(!headers){

        return;

    }

    if(
        $.fn.DataTable.isDataTable(
            "#nlpTable"
        )
    ){

        $("#nlpTable")
            .DataTable()
            .destroy();

    }

    let html = "";

    headers.forEach(function(item,index){

        /*
        ------------------------------------------------------
        Data Type Badge
        ------------------------------------------------------
        */

        let typeBadge = "secondary";

        switch(item.data_type){

            case "Numeric":

                typeBadge = "primary";

                break;

            case "Alphabetic":

                typeBadge = "success";

                break;

            case "Alphanumeric":

                typeBadge = "warning";

                break;

            default:

                typeBadge = "secondary";

        }

        /*
        ------------------------------------------------------
        Semantic Role Badge
        ------------------------------------------------------
        */

        let roleBadge = "secondary";

        switch(item.role){

            case "Identifier":

                roleBadge = "dark";

                break;

            case "Numeric":

                roleBadge = "primary";

                break;

            case "Text":

                roleBadge = "success";

                break;

            case "Categorical":

                roleBadge = "info";

                break;

            case "Target Candidate":

                roleBadge = "danger";

                break;

            default:

                roleBadge = "secondary";

        }

        /*
        ------------------------------------------------------
        Tokens
        ------------------------------------------------------
        */

        let tokens = "";

        if(
            Array.isArray(item.tokens)
        ){

            tokens = item.tokens.join(", ");

        }
        else{

            tokens = "-";

        }

        /*
        ------------------------------------------------------
        Table Row
        ------------------------------------------------------
        */

        html += `

        <tr>

            <td>
                ${index + 1}
            </td>

            <td>

                <strong>

                    ${escapeHtml(
                        String(
                            item.column || "-"
                        )
                    )}

                </strong>

            </td>

            <td>

                ${escapeHtml(tokens)}

            </td>

            <td>

                <span class="badge bg-${typeBadge}">

                    ${escapeHtml(
                        String(
                            item.data_type || "Unknown"
                        )
                    )}

                </span>

            </td>

            <td>

                <span class="badge bg-${roleBadge}">

                    ${escapeHtml(
                        String(
                            item.role || "Unknown"
                        )
                    )}

                </span>

            </td>

        </tr>

        `;

    });

    /*
    ------------------------------------------------------
    Update Table
    ------------------------------------------------------
    */

    $("#nlpBody").html(html);

    /*
    ------------------------------------------------------
    Badge
    ------------------------------------------------------
    */

    $("#nlpBadge").text(

        headers.length +
        " Columns"

    );

    /*
    ------------------------------------------------------
    DataTable
    ------------------------------------------------------
    */

    $("#nlpTable").DataTable({

        responsive: true,

        pageLength: 10,

        destroy: true,

        searching: true,

        ordering: true,

        autoWidth: false

    });

    labelDataTableSearch("nlpTable", "Search NLP header analysis");

}

/*
|-------------------------------------------------------------------------- 
| Correlation Matrix
|-------------------------------------------------------------------------- 
*/

function loadCorrelation(correlationData){

    if(!correlationData){

        console.warn(
            "No correlation data received."
        );

        return;

    }

    /*
    ----------------------------------------------------------
    Python correlation_engine.py returns:
    
    {
        columns: [...],
        matrix: [
            [...],
            [...]
        ]
    }
    ----------------------------------------------------------
    */

    const columns = correlationData.columns || [];

    const matrix = correlationData.matrix || [];

    if(
        columns.length === 0 ||
        matrix.length === 0
    ){

        console.warn(
            "Correlation matrix is empty."
        );

        $("#correlationHead").html("");

        $("#correlationBody").html("");

        $("#correlationBadge").text(
            "0 Variables"
        );

        return;

    }

    /*
    ----------------------------------------------------------
    Destroy Existing DataTable
    ----------------------------------------------------------
    */

    if(
        $.fn.DataTable.isDataTable(
            "#correlationTable"
        )
    ){

        $("#correlationTable")
            .DataTable()
            .destroy();

    }

    /*
    ----------------------------------------------------------
    Build Header
    ----------------------------------------------------------
    */

    let head = "<tr>";

    head += "<th>Variable</th>";

    columns.forEach(function(column){

        head += `
            <th>
                ${escapeHtml(String(column))}
            </th>
        `;

    });

    head += "</tr>";

    $("#correlationHead").html(head);

    /*
    ----------------------------------------------------------
    Build Body
    ----------------------------------------------------------
    */

    let body = "";

    columns.forEach(function(rowName, rowIndex){

        body += "<tr>";

        body += `
            <th>
                ${escapeHtml(String(rowName))}
            </th>
        `;

        columns.forEach(function(columnName, columnIndex){

            let value =
                matrix[rowIndex]?.[columnIndex];

            /*
            --------------------------------------------------
            Handle null / undefined / NaN
            --------------------------------------------------
            */

            if(
                value === null ||
                value === undefined ||
                Number.isNaN(Number(value))
            ){

                body += `
                    <td>
                        -
                    </td>
                `;

                return;

            }

            value = Number(value);

            /*
            --------------------------------------------------
            Correlation Cell Color
            --------------------------------------------------
            */

            let cellClass = "";

            if(value >= 0.80){

                cellClass = "table-success";

            }
            else if(value <= -0.80){

                cellClass = "table-danger";

            }
            else if(Math.abs(value) >= 0.50){

                cellClass = "table-warning";

            }

            body += `
                <td class="${cellClass}">
                    ${value.toFixed(3)}
                </td>
            `;

        });

        body += "</tr>";

    });

    $("#correlationBody").html(body);

    /*
    ----------------------------------------------------------
    Badge
    ----------------------------------------------------------
    */

    $("#correlationBadge").text(
        columns.length + " Variables"
    );

    /*
    ----------------------------------------------------------
    Initialize DataTable
    ----------------------------------------------------------
    */

    $("#correlationTable").DataTable({

        responsive: true,

        searching: false,

        paging: false,

        ordering: false,

        info: false,

        destroy: true,

        autoWidth: false

    });

}

/*
|-------------------------------------------------------------------------- 
| Strongest Correlations
|-------------------------------------------------------------------------- 
*/

function loadStrongCorrelations(results){

    if(!Array.isArray(results)){

        console.warn(
            "Strongest correlations is not an array:",
            results
        );

        return;

    }

    /*
    ----------------------------------------------------------
    Destroy Existing DataTable
    ----------------------------------------------------------
    */

    if(
        $.fn.DataTable.isDataTable(
            "#strongCorrelationTable"
        )
    ){

        $("#strongCorrelationTable")
            .DataTable()
            .destroy();

    }

    /*
    ----------------------------------------------------------
    Empty Result
    ----------------------------------------------------------
    */

    if(results.length === 0){

        $("#strongCorrelationBody").html("");

        $("#strongCorrelationBadge").text(
            "0 Relationships"
        );

        return;

    }

    /*
    ----------------------------------------------------------
    Build Table
    ----------------------------------------------------------
    */

    let html = "";

    const limit = Math.min(
        10,
        results.length
    );

    for(let i = 0; i < limit; i++){

        const item = results[i];

        const variable1 =
            item.feature_1 ?? "-";

        const variable2 =
            item.feature_2 ?? "-";

        let correlation =
            Number(item.correlation);

        /*
        ------------------------------------------------------
        Handle NaN
        ------------------------------------------------------
        */

        if(Number.isNaN(correlation)){

            correlation = null;

        }

        /*
        ------------------------------------------------------
        Interpretation
        ------------------------------------------------------
        */

        let interpretation = "Weak";

        let badge = "secondary";

        if(correlation !== null){

            if(correlation >= 0.90){

                interpretation =
                    "Very Strong Positive";

                badge = "success";

            }

            else if(correlation >= 0.70){

                interpretation =
                    "Strong Positive";

                badge = "primary";

            }

            else if(correlation >= 0.50){

                interpretation =
                    "Moderate Positive";

                badge = "info";

            }

            else if(correlation <= -0.90){

                interpretation =
                    "Very Strong Negative";

                badge = "danger";

            }

            else if(correlation <= -0.70){

                interpretation =
                    "Strong Negative";

                badge = "warning";

            }

            else if(correlation <= -0.50){

                interpretation =
                    "Moderate Negative";

                badge = "dark";

            }

        }

        html += `

            <tr>

                <td>
                    ${i + 1}
                </td>

                <td>
                    <strong>
                        ${escapeHtml(
                            String(variable1)
                        )}
                    </strong>
                </td>

                <td>
                    <strong>
                        ${escapeHtml(
                            String(variable2)
                        )}
                    </strong>
                </td>

                <td>
                    ${
                        correlation === null
                        ? "-"
                        : correlation.toFixed(3)
                    }
                </td>

                <td>

                    <span class="badge bg-${badge}">

                        ${interpretation}

                    </span>

                </td>

            </tr>

        `;

    }

    /*
    ----------------------------------------------------------
    Insert HTML
    ----------------------------------------------------------
    */

    $("#strongCorrelationBody").html(html);

    /*
    ----------------------------------------------------------
    Badge
    ----------------------------------------------------------
    */

    $("#strongCorrelationBadge").text(

        limit + " Relationships"

    );

    /*
    ----------------------------------------------------------
    Initialize DataTable
    ----------------------------------------------------------
    */

    $("#strongCorrelationTable").DataTable({

        responsive: true,

        pageLength: 10,

        destroy: true,

        searching: false,

        ordering: false,

        paging: false,

        info: false,

        autoWidth: false

    });

}

/*
|--------------------------------------------------------------------------
| Benchmark Table
|--------------------------------------------------------------------------
*/

function loadBenchmark(results){

    results = Array.isArray(results) ? results : [];
    const groupedResults = new Map();
    results.forEach(function(result){
        const category = result.category ?? result.task ?? "Other";
        if(!groupedResults.has(category)){
            groupedResults.set(category, []);
        }
        groupedResults.get(category).push(result);
    });

    results = [];
    groupedResults.forEach(function(categoryResults){
        categoryResults.sort(function(a, b){
            if(Boolean(a.error) !== Boolean(b.error)){
                return a.error ? 1 : -1;
            }
            if(Boolean(a.is_recommended) !== Boolean(b.is_recommended)){
                return a.is_recommended ? -1 : 1;
            }
            const rankA = Number(a.category_rank);
            const rankB = Number(b.category_rank);
            if(Number.isFinite(rankA) && Number.isFinite(rankB) && rankA !== rankB){
                return rankA - rankB;
            }
            const scoreA = Number(a.primary_score ?? a.accuracy ?? a.r2 ?? a.silhouette ?? -Infinity);
            const scoreB = Number(b.primary_score ?? b.accuracy ?? b.r2 ?? b.silhouette ?? -Infinity);
            const lowerIsBetter = a.higher_is_better === false
                || /rmse|mse|mae|loss|error/i.test(a.primary_metric || "");
            return lowerIsBetter ? scoreA - scoreB : scoreB - scoreA;
        });
        categoryResults.forEach(function(result, index){
            const rank = Number(result.category_rank);
            if(result.error){
                result.category_rank = null;
            } else if(result.category_rank === null || result.category_rank === undefined
                || result.category_rank === "" || !Number.isFinite(rank) || rank < 1){
                result.category_rank = index + 1;
            }
            results.push(result);
        });
    });

    if($.fn.DataTable.isDataTable("#benchmarkTable")){

        $("#benchmarkTable").DataTable().destroy();

    }

    let html="";

    results.forEach(function(result,index){
        const statusMessage = result.error
            ? "Not run: " + String(result.error)
            : (result.status ?? (result.is_recommended ? "Recommended" : "Completed"));

        html+=`

        <tr>

            <td>

                ${result.error
                    ?'-'
                    :result.category_rank==1

                    ?'<span class="badge bg-success">1</span>'

                    :result.category_rank}

            </td>

            <td>

                ${result.category ?? result.task ?? "-"}

            </td>

            <td>

                <strong>

                    ${result.algorithm}

                </strong>

                ${result.is_recommended ? '<span class="badge bg-success ms-1">Recommended</span>' : ''}

            </td>

            <td>

                ${result.target ?? "-"}

            </td>

            <td>

                ${result.primary_metric ?? (result.accuracy !== undefined ? "Accuracy" : result.r2 !== undefined ? "R²" : result.silhouette !== undefined ? "Silhouette" : "-")}

            </td>

            <td>

                ${result.primary_score ?? result.accuracy ?? result.r2 ?? result.silhouette ?? "-"}

            </td>

            <td>

                ${result.accuracy ?? "-"}

            </td>

            <td>

                ${result.cv_score ?? result.cross_validation_score ?? result.cross_val_score ?? (result.cv_error ? "Unavailable: " + result.cv_error : "-")}

            </td>

            <td>

                ${result.precision ?? "-"}

            </td>

            <td>

                ${result.recall ?? "-"}

            </td>

            <td>

                ${result.f1_score ?? result.f1 ?? "-"}

            </td>

            <td>

                ${result.r2 ?? result.rmse ?? "-"}

            </td>

            <td>

                ${result.silhouette ?? "-"}

            </td>

            <td>

                ${result.execution_time}

            </td>

            <td>

                ${escapeHtml(String(statusMessage))}

            </td>

        </tr>

        `;

    });

    $("#benchmarkBody").html(html);

    $("#benchmarkBadge").text(

        results.length+" Algorithms"

    );

    $("#benchmarkTable").DataTable({

        responsive:true,

        pageLength:10,

        ordering:true,

        searching:true,

        autoWidth:false,

        destroy:true

    });

    labelDataTableSearch("benchmarkTable", "Search benchmark results");

}

/*
|--------------------------------------------------------------------------
| Recommendation Card
|--------------------------------------------------------------------------
*/

function loadRecommendation(recommendation){

    if(!recommendation){

        return;

    }

    const categoryRecommendations = Array.isArray(recommendation.by_category)
        ? recommendation.by_category
        : [];
    const recommendationRows = $("#recommendationByCategory").empty();

    if(categoryRecommendations.length > 0){
        $("#recommendedAlgorithm").text("Category-specific recommendations");
        $("#recommendedTask").text("Ranked separately by category");
        $("#recommendedScore").text("Not comparable across categories");
        $("#recommendationByCategoryWrap").show();
        categoryRecommendations.forEach(function(item){
            const row = $("<tr>");
            const score = item.score !== undefined && Number.isFinite(Number(item.score))
                ? Number(item.score).toFixed(4)
                : "-";
            [item.category, item.algorithm, item.metric, score].forEach(function(value){
                row.append($("<td>").text(value ?? "-"));
            });
            recommendationRows.append(row);
        });
    }else{
        $("#recommendedAlgorithm").text(recommendation.best_algorithm || "-");
        $("#recommendedTask").text(recommendation.task || "-");
        $("#recommendedScore").text(
            recommendation.score !== undefined && Number.isFinite(Number(recommendation.score))
                ? Number(recommendation.score).toFixed(4)
                : "-"
        );
        $("#recommendationByCategoryWrap").hide();
    }

    $("#recommendedReason").text(

        recommendation.reason ||

        "No explanation available."

    );

}

/*
|--------------------------------------------------------------------------
| Feature Importance
|--------------------------------------------------------------------------
*/

let featureChart = null;

function loadFeatureImportance(features){

    if(!features){

        return;

    }

    if(!Array.isArray(features)){

        console.warn(
            "Feature importance is not an array:",
            features
        );

        return;

    }

    if(features.length===0){

        return;

    }

    /*
    ----------------------------------------------------------
    Sort Features
    ----------------------------------------------------------
    */

    features.sort(function(a,b){

        return Number(b.importance || 0)
             - Number(a.importance || 0);

    });

    let html="";

    let labels=[];

    let values=[];

    /*
    ----------------------------------------------------------
    Build Table
    ----------------------------------------------------------
    */

    features.forEach(function(item,index){

        const importance = Number(
            item.importance || 0
        );

        html+=`

        <tr>

            <td>${index+1}</td>

            <td>${escapeHtml(
                String(item.feature || "-")
            )}</td>

            <td>${importance.toFixed(4)}</td>

        </tr>

        `;

        labels.push(
            String(item.feature || "-")
        );

        values.push(importance);

    });

    $("#featureBody").html(html);

    $("#featureBadge").text(

        features.length+" Features"

    );

    /*
    ----------------------------------------------------------
    DataTable
    ----------------------------------------------------------
    */

    if(
        $.fn.DataTable.isDataTable(
            "#featureTable"
        )
    ){

        $("#featureTable")
            .DataTable()
            .destroy();

    }

    $("#featureTable").DataTable({

        paging:false,

        searching:false,

        info:false,

        ordering:false,

        responsive:true,

        destroy:true

    });

    /*
    ----------------------------------------------------------
    Feature Chart
    ----------------------------------------------------------
    */

    const canvas = document.getElementById(
        "featureChart"
    );

    /*
    IMPORTANT:
    If the canvas doesn't exist, don't crash
    the entire analysis process.
    */

    if(!canvas){

        console.warn(
            "Feature Importance chart canvas #featureChart was not found."
        );

        return;

    }

    /*
    ----------------------------------------------------------
    Destroy Previous Chart
    ----------------------------------------------------------
    */

    if(featureChart !== null){

        featureChart.destroy();

        featureChart = null;

    }

    const ctx = canvas.getContext("2d");

    if(!ctx){

        console.warn(
            "Unable to get 2D context from #featureChart."
        );

        return;

    }

    /*
    ----------------------------------------------------------
    Create Chart
    ----------------------------------------------------------
    */

    featureChart = new Chart(ctx,{

        type:"bar",

        data:{

            labels:labels,

            datasets:[{

                label:"Importance",

                data:values,

                borderWidth:1

            }]

        },

        options:{

            responsive:true,

            maintainAspectRatio:false,

            indexAxis:"y",

            plugins:{

                legend:{

                    display:false

                }

            },

            scales:{

                x:{

                    beginAtZero:true,

                    max:1

                }

            }

        }

    });

}

/*
|--------------------------------------------------------------------------
| Hyperparameter Tuning
|--------------------------------------------------------------------------
*/

function loadTuning(tuning){

    if(!tuning){
        $("#bestCVScore").text("-");
        $("#tuningAlgorithm").text("");
        $("#tuningBody").html('<tr><td colspan="2" class="text-center text-muted">No tuned result was returned by the analysis service.</td></tr>');
        $("#tuningBadge").removeClass().addClass("badge bg-secondary").text("Unavailable");
        return;

    }

    const tuningResults = Array.isArray(tuning.by_category) ? tuning.by_category : [tuning];
    const rows = $("#tuningBody").empty();
    const singleTuning = tuningResults.length === 1 ? tuningResults[0] : null;
    const score = singleTuning?.best_score ?? singleTuning?.best_cv_score ?? singleTuning?.cv_score;
    const hasScore = score !== undefined && score !== null && score !== "" && Number.isFinite(Number(score));
    const tunedAlgorithms = Array.from(new Set(tuningResults.map(function(result){
        return result.algorithm;
    }).filter(Boolean)));
    let hasSuccessfulResult = false;
    let hasFailedResult = false;
    $("#bestCVScore").text(
        hasScore ? Number(score).toFixed(4) : (tuningResults.length > 1 ? "Per target" : "-")
    );
    $("#tuningAlgorithm").text(tunedAlgorithms.length === 1
        ? "Recommended model: " + tunedAlgorithms[0]
        : ""
    );

    tuningResults.forEach(function(result){
        if(tuningResults.length > 1){
            rows.append($("<tr>", {class: "table-secondary"}).append(
                $("<th>", {colspan: 2}).text(
                    (result.category || "Category") +
                    (result.target ? " / " + result.target : "") +
                    " | " + (result.algorithm || "Recommended model")
                )
            ));
            const categoryScore = result.best_score ?? result.best_cv_score ?? result.cv_score;
            if(categoryScore !== undefined && categoryScore !== null && categoryScore !== "" && Number.isFinite(Number(categoryScore))){
                rows.append($("<tr>").append(
                    $("<td>").text("Best CV Score"),
                    $("<td>").text(Number(categoryScore).toFixed(4))
                ));
            }
        }
            if(result.success === false || result.error){
                hasFailedResult = true;
                rows.append($("<tr>").append(
                    $("<td>", {colspan: 2, class: "text-warning"}).text(result.error || "Optimization did not complete.")
                ));
                return;
            }


        const parameters = result.best_parameters ?? result.best_params ?? result.parameters ?? {};
        const safeParameters = parameters && typeof parameters === "object" && !Array.isArray(parameters)
            ? parameters
            : {};
        const resultScore = result.best_score ?? result.best_cv_score ?? result.cv_score;
        if(Object.keys(safeParameters).length > 0 || (resultScore !== undefined && resultScore !== null)){
            hasSuccessfulResult = true;
        }
        Object.keys(safeParameters).forEach(function(parameter){
            rows.append($("<tr>").append(
                $("<td>").text(parameter),
                $("<td>").text(typeof safeParameters[parameter] === "object"
                    ? JSON.stringify(safeParameters[parameter])
                    : safeParameters[parameter])
            ));
        });
    });

    if(rows.children().length === 0){
        rows.append($("<tr>").append(
            $("<td>", {colspan: 2, class: "text-center text-muted"}).text("The service returned no optimized parameters for the recommended model.")
        ));
    }

    const badgeClass = hasSuccessfulResult
        ? (hasFailedResult ? "badge bg-warning text-dark" : "badge bg-success")
        : "badge bg-secondary";
    const badgeText = hasSuccessfulResult
        ? (hasFailedResult ? "Partial" : "Completed")
        : "Unavailable";
    $("#tuningBadge").removeClass().addClass(badgeClass).text(badgeText);

}

/*
----------------------------------------
Benchmark Charts
----------------------------------------
*/

function loadBenchmarkCharts(results){

    benchmarkResults = Array.isArray(results)
        ? results.filter(function(result){ return !result.error; })
        : [];

    drawBenchmarkChart();

}

function drawBenchmarkChart(){

    if(benchmarkResults.length===0){
        if(benchmarkChart!==null){
            benchmarkChart.destroy();
            benchmarkChart = null;
        }
        return;

    }

    if(benchmarkChart!==null){

        benchmarkChart.destroy();

    }

    let metric = $("#benchmarkMetric").val();

    let chartType = $("#benchmarkChartType").val();

    let labels = [];

    let values = [];

    benchmarkResults.forEach(function(item){

        labels.push(item.algorithm);

        values.push(item[metric] ?? 0);

    });

    let ctx = document
        .getElementById("benchmarkChart")
        .getContext("2d");

    let dataset = {

        label: metric,

        data: values,

        borderWidth: 2

    };

    let config = {

        type: chartType,

        data: {

            labels: labels,

            datasets: [dataset]

        },

        options: {

            responsive: true,

            maintainAspectRatio: false

        }

    };

    if(chartType==="scatter"){

        config.data.datasets = [{

            label: metric,

            data: values.map(function(value,index){

                return {

                    x:index+1,

                    y:value

                };

            })

        }];

    }

    benchmarkChart = new Chart(ctx, config);

}

$("#benchmarkMetric").on("change",function(){

    drawBenchmarkChart();

});

$("#benchmarkChartType").on("change",function(){

    drawBenchmarkChart();

});