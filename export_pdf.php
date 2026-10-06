<?php

/*
==========================================================
 SimuRithm
 PDF Analysis Report
 Release 1.3
==========================================================
*/

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| Prevent PHP Errors From Corrupting PDF Output
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);

ini_set('display_errors', '0');

ini_set('log_errors', '1');


/*
|--------------------------------------------------------------------------
| Start Output Buffer
|--------------------------------------------------------------------------
*/

ob_start();


/*
|--------------------------------------------------------------------------
| Load Composer
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/vendor/autoload.php';


use Dompdf\Dompdf;
use Dompdf\Options;

/*
==========================================================
 1. LOAD ANALYSIS DATA
==========================================================
*/

$analysis = null;


/*
|--------------------------------------------------------------------------
| Load Analysis From POST
|--------------------------------------------------------------------------
*/

if (

    isset($_POST["analysis"])

    &&

    !empty($_POST["analysis"])

) {

    $decoded = json_decode(

        $_POST["analysis"],

        true

    );


    if (

        is_array($decoded)

    ) {

        $analysis = $decoded;
    }
}


/*
|--------------------------------------------------------------------------
| 2. Validate Analysis
|--------------------------------------------------------------------------
*/

if (

    $analysis === null

    ||

    !is_array($analysis)

) {

    if (ob_get_level()) {

        ob_end_clean();
    }

    http_response_code(400);

    header(

        "Content-Type: text/plain; charset=UTF-8"

    );

    exit("Unable to generate PDF: no valid analysis data received.");
}


/*
==========================================================
 3. HELPER FUNCTIONS
==========================================================
*/

/**
 * Safely return a value from an array.
 */
function value(
    $array,
    $key,
    $default = 'N/A'
) {

    if (
        is_array($array) &&
        array_key_exists($key, $array)
    ) {

        return $array[$key];
    }

    return $default;
}


/**
 * Escape HTML output.
 */
function e($value)
{

    if (
        is_array($value) ||
        is_object($value)
    ) {

        return htmlspecialchars(
            json_encode(
                $value,
                JSON_UNESCAPED_UNICODE
            ),
            ENT_QUOTES,
            'UTF-8'
        );
    }

    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}


/**
 * Format numbers.
 */
function numberValue(
    $value,
    $decimals = 4
) {

    if (!is_numeric($value)) {

        return 'N/A';
    }

    return number_format(
        (float)$value,
        $decimals
    );
}


/**
 * Convert arrays into readable text.
 */
function readableValue($value)
{

    if (is_array($value)) {

        return implode(
            ', ',
            array_map(
                function ($item) {

                    if (
                        is_array($item) ||
                        is_object($item)
                    ) {

                        return json_encode(
                            $item,
                            JSON_UNESCAPED_UNICODE
                        );
                    }

                    return (string)$item;
                },
                $value
            )
        );
    }

    if (is_object($value)) {

        return json_encode(
            $value,
            JSON_UNESCAPED_UNICODE
        );
    }

    return (string)$value;
}


/*
==========================================================
 4. EXTRACT COMMON DATA
==========================================================
*/

$datasetName = value(
    $analysis,
    'dataset_name',
    value(
        $analysis,
        'filename',
        'Uploaded Dataset'
    )
);

$taskType = value(
    $analysis,
    'task_type',
    value(
        $analysis,
        'task',
        'N/A'
    )
);

$rowCount = value(
    $analysis,
    'row_count',
    value(
        $analysis,
        'rows',
        'N/A'
    )
);

$columnCount = value(
    $analysis,
    'column_count',
    value(
        $analysis,
        'columns_count',
        'N/A'
    )
);

$xVariables = value(
    $analysis,
    'x_variables',
    []
);

$yVariable = value(
    $analysis,
    'y_variable',
    'N/A'
);

$statistics = value(
    $analysis,
    'statistics_table',
    []
);

$profile = value(
    $analysis,
    'dataset_profile',
    []
);

$preview = value(
    $analysis,
    'preview',
    []
);

$nlp = value(
    $analysis,
    'nlp_analysis',
    []
);

$correlation = value(
    $analysis,
    'correlation_matrix',
    []
);

$strongest = value(
    $analysis,
    'strongest_correlations',
    []
);

$benchmark = value(
    $analysis,
    'benchmark_results',
    []
);

$recommendation = value(
    $analysis,
    'recommendation',
    []
);

$featureImportance = value(
    $analysis,
    'feature_importance',
    []
);

$tuning = value(
    $analysis,
    'tuning',
    []
);


/*
==========================================================
 5. NORMALIZE DATA STRUCTURES
==========================================================
*/

if (!is_array($xVariables)) {

    $xVariables = [];
}

if (!is_array($statistics)) {

    $statistics = [];
}

if (!is_array($profile)) {

    $profile = [];
}

if (!is_array($preview)) {

    $preview = [];
}

if (!is_array($nlp)) {

    $nlp = [];
}

if (!is_array($correlation)) {

    $correlation = [];
}

if (!is_array($strongest)) {

    $strongest = [];
}

if (!is_array($benchmark)) {

    $benchmark = [];
}

if (!is_array($featureImportance)) {

    $featureImportance = [];
}

if (!is_array($tuning)) {

    $tuning = [];
}


/*
==========================================================
 6. RECOMMENDATION NORMALIZATION
==========================================================
*/

$recommendedAlgorithm = 'N/A';

$recommendationReason = '';

if (is_array($recommendation)) {

    $recommendedAlgorithm = value(
        $recommendation,
        'recommended_algorithm',
        value(
            $recommendation,
            'algorithm',
            value(
                $recommendation,
                'model',
                'N/A'
            )
        )
    );

    $recommendationReason = value(
        $recommendation,
        'reason',
        value(
            $recommendation,
            'explanation',
            value(
                $recommendation,
                'message',
                ''
            )
        )
    );
} elseif (
    is_string($recommendation)
) {

    $recommendedAlgorithm =
        $recommendation;
}


/*
==========================================================
 7. CREATE HTML
==========================================================
*/

$html = '';

$html .= '
<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<style>

@page {

    margin: 35px 35px 45px 35px;

}

body {

    font-family: DejaVu Sans, sans-serif;

    font-size: 10px;

    color: #222;

}

h1 {

    font-size: 22px;

    margin-bottom: 5px;

}

h2 {

    font-size: 15px;

    margin-top: 20px;

    margin-bottom: 8px;

    border-bottom: 1px solid #444;

    padding-bottom: 4px;

}

h3 {

    font-size: 12px;

    margin-top: 12px;

}

p {

    margin: 4px 0;

}

.header {

    text-align: center;

    margin-bottom: 20px;

}

.subtitle {

    font-size: 11px;

}

.meta {

    width: 100%;

    border-collapse: collapse;

    margin-bottom: 15px;

}

.meta td {

    border: 1px solid #bbb;

    padding: 6px;

}

.meta .label {

    font-weight: bold;

    width: 30%;

}

table {

    width: 100%;

    border-collapse: collapse;

    margin-top: 8px;

}

th {

    border: 1px solid #888;

    padding: 5px;

    font-weight: bold;

    background: #eeeeee;

}

td {

    border: 1px solid #aaa;

    padding: 5px;

}

.section {

    page-break-inside: avoid;

}

.page-break {

    page-break-before: always;

}

.recommendation {

    border: 1px solid #777;

    padding: 10px;

    margin-top: 8px;

}

.recommended {

    font-size: 15px;

    font-weight: bold;

}

.small {

    font-size: 8px;

}

</style>

</head>

<body>
';


/*
==========================================================
 8. REPORT HEADER
==========================================================
*/

$html .= '

<div class="header">

    <h1>SimuRithm</h1>

    <div class="subtitle">
        Intelligent Decision Support System
        for Automated ML Model Selection
    </div>

    <p>
        Machine Learning Analysis Report
    </p>

</div>

';


/*
==========================================================
 9. DATASET INFORMATION
==========================================================
*/

$html .= '

<div class="section">

<h2>1. Dataset Information</h2>

<table class="meta">

<tr>

<td class="label">
Dataset
</td>

<td>
' . e($datasetName) . '
</td>

</tr>

<tr>

<td class="label">
Task Type
</td>

<td>
' . e(ucfirst(
    strtolower(
        (string)$taskType
    )
)) . '
</td>

</tr>

<tr>

<td class="label">
Rows
</td>

<td>
' . e(
    readableValue($rowCount)
) . '
</td>

</tr>

<tr>

<td class="label">
Columns
</td>

<td>
' . e(
    readableValue($columnCount)
) . '
</td>

</tr>

<tr>

<td class="label">
Target Variable
</td>

<td>
' . e(
    readableValue($yVariable)
) . '
</td>

</tr>

<tr>

<td class="label">
Input Variables
</td>

<td>
' . e(
    readableValue($xVariables)
) . '
</td>

</tr>

</table>

</div>

';


/*
==========================================================
 10. STATISTICS
==========================================================
*/

$html .= '

<div class="section">

<h2>2. Statistics Summary</h2>
';


if (!empty($statistics)) {

    $html .= '

    <table>

    <thead>

    <tr>

        <th>Variable</th>

        <th>Mean</th>

        <th>Std</th>

        <th>Min</th>

        <th>Max</th>

    </tr>

    </thead>

    <tbody>
    ';


    foreach ($statistics as $variable => $stat) {

        if (!is_array($stat)) {

            continue;
        }

        $variable = value(
            $stat,
            'column',
            'N/A'
        );

        $html .= '

        <tr>

        <td>
        ' . e($variable) . '
        </td>

        <td>
        ' . numberValue(
            value($stat, 'mean', null)
        ) . '
        </td>

        <td>
        ' . numberValue(
            value($stat, 'std', null)
        ) . '
        </td>

        <td>
        ' . numberValue(
            value($stat, 'min', null)
        ) . '
        </td>

        <td>
        ' . numberValue(
            value($stat, 'max', null)
        ) . '
        </td>

        </tr>
        ';
    }

    $html .= '

    </tbody>

    </table>
    ';
} else {

    $html .= '<p>No statistics data available.</p>';
}

$html .= '

</div>
';


/*
==========================================================
 11. DATASET PROFILE
==========================================================
*/

$html .= '

<div class="section page-break">

<h2>3. Dataset Profile</h2>

';


if (!empty($profile)) {

    $html .= '

    <table>

    <tbody>
    ';

    foreach ($profile as $key => $profileValue) {

        if (
            is_array($profileValue) ||
            is_object($profileValue)
        ) {

            $profileValue =
                readableValue($profileValue);
        }

        $html .= '

        <tr>

        <td class="label">
        ' . e(
            ucwords(
                str_replace(
                    '_',
                    ' ',
                    $key
                )
            )
        ) . '
        </td>

        <td>
        ' . e($profileValue) . '
        </td>

        </tr>

        ';
    }

    $html .= '

    </tbody>

    </table>

    ';
} else {

    $html .= '

    <p>
        No dataset profile information available.
    </p>

    ';
}

$html .= '

</div>
';


/*
==========================================================
 12. NLP HEADER ANALYSIS
==========================================================
*/

$html .= '

<div class="section">

<h2>4. NLP Header Analysis</h2>

';

if (!empty($nlp)) {

    $html .= '

    <table>

    <tbody>

    ';

    foreach ($nlp as $key => $nlpValue) {

        $html .= '

        <tr>

        <td class="label">
        ' . e(
            ucwords(
                str_replace(
                    '_',
                    ' ',
                    (string)$key
                )
            )
        ) . '
        </td>

        <td>
        ' . e(
            readableValue($nlpValue)
        ) . '
        </td>

        </tr>

        ';
    }

    $html .= '

    </tbody>

    </table>

    ';
} else {

    $html .= '

    <p>
        No NLP analysis available.
    </p>

    ';
}

$html .= '

</div>
';


/*
==========================================================
 13. CORRELATION MATRIX
==========================================================
*/

$html .= '

<div class="section page-break">

<h2>5. Correlation Matrix</h2>

';

if (!empty($correlation)) {

    $html .= '

    <table>

    <thead>

    <tr>

    ';

    /*
     * Detect matrix headers.
     */

    $firstRow = reset(
        $correlation
    );

    if (is_array($firstRow)) {

        $html .= '<th>Variable</th>';

        foreach (
            array_keys($firstRow)
            as $column
        ) {

            $html .= '<th>' . e(
                $column
            ) . '</th>';
        }

        $html .= '</tr>';

        foreach (
            $correlation as $rowName => $row
        ) {

            $html .= '<tr>';

            $html .= '<td><strong>'
                . e($rowName)
                . '</strong></td>';

            foreach (
                $row as $cell
            ) {

                $html .= '<td>'
                    . numberValue(
                        $cell
                    )
                    . '</td>';
            }

            $html .= '</tr>';
        }
    }

    $html .= '

    </table>

    ';
} else {

    $html .= '

    <p>
        No correlation matrix available.
    </p>

    ';
}

$html .= '

</div>
';


/*
==========================================================
 14. STRONGEST CORRELATIONS
==========================================================
*/

$html .= '

<div class="section">

<h2>6. Strongest Correlations</h2>

';

if (!empty($strongest)) {

    $html .= '

    <table>

    <thead>

    <tr>

        <th>Variable 1</th>

        <th>Variable 2</th>

        <th>Correlation</th>

    </tr>

    </thead>

    <tbody>

    ';

    foreach (
        $strongest as $item
    ) {

        if (!is_array($item)) {

            continue;
        }

        $variable1 = value(
            $item,
            'feature_1',
            value(
                $item,
                'feature1',
                value(
                    $item,
                    'variable1',
                    value(
                        $item,
                        'x',
                        'N/A'
                    )
                )
            )
        );

        $variable2 = value(
            $item,
            'feature_2',
            value(
                $item,
                'feature2',
                value(
                    $item,
                    'variable2',
                    value(
                        $item,
                        'y',
                        'N/A'
                    )
                )
            )
        );

        $corr = value(
            $item,
            'correlation',
            value(
                $item,
                'value',
                null
            )
        );

        $html .= '

        <tr>

        <td>
        ' . e($variable1) . '
        </td>

        <td>
        ' . e($variable2) . '
        </td>

        <td>
        ' . numberValue($corr) . '
        </td>

        </tr>

        ';
    }

    $html .= '

    </tbody>

    </table>

    ';
} else {

    $html .= '

    <p>
        No strongest correlation data available.
    </p>

    ';
}

$html .= '

</div>
';


/*
==========================================================
 15. BENCHMARK RESULTS
==========================================================
*/

$html .= '

<div class="section page-break">

<h2>7. Benchmark Results</h2>

';

if (!empty($benchmark)) {

    $html .= '

    <table>

    <thead>

    <tr>

        <th>Algorithm</th>

        <th>Accuracy</th>

        <th>Precision</th>

        <th>Recall</th>

        <th>F1</th>

        <th>R²</th>

        <th>MSE</th>

    </tr>

    </thead>

    <tbody>

    ';

    foreach (
        $benchmark as $result
    ) {

        if (!is_array($result)) {

            continue;
        }

        $algorithm = value(
            $result,
            'algorithm',
            value(
                $result,
                'model',
                'N/A'
            )
        );

        $html .= '

        <tr>

        <td>
        ' . e($algorithm) . '
        </td>

        <td>
        ' . numberValue(
            value(
                $result,
                'accuracy',
                null
            )
        ) . '
        </td>

        <td>
        ' . numberValue(
            value(
                $result,
                'precision',
                null
            )
        ) . '
        </td>

        <td>
        ' . numberValue(
            value(
                $result,
                'recall',
                null
            )
        ) . '
        </td>

        <td>
        ' . numberValue(
            value(
                $result,
                'f1',
                value(
                    $result,
                    'f1_score',
                    null
                )
            )
        ) . '
        </td>

        <td>
        ' . numberValue(
            value(
                $result,
                'r2',
                value(
                    $result,
                    'r2_score',
                    null
                )
            )
        ) . '
        </td>

        <td>
        ' . numberValue(
            value(
                $result,
                'mse',
                value(
                    $result,
                    'mean_squared_error',
                    null
                )
            )
        ) . '
        </td>

        </tr>

        ';
    }

    $html .= '

    </tbody>

    </table>

    ';
} else {

    $html .= '

    <p>
        No benchmark results available.
    </p>

    ';
}

$html .= '

</div>
';


/*
==========================================================
 16. RECOMMENDATION
==========================================================
*/

$html .= '

<div class="section">

<h2>8. Model Recommendation</h2>

<div class="recommendation">

<p>
<strong>Recommended Algorithm:</strong>
</p>

<p class="recommended">
' . e(
    $recommendedAlgorithm
) . '
</p>

';

if (!empty($recommendationReason)) {

    $html .= '

    <p>
    <strong>Reason:</strong>
    </p>

    <p>
    ' . e(
        readableValue(
            $recommendationReason
        )
    ) . '
    </p>

    ';
}

$html .= '

</div>

</div>
';


/*
==========================================================
 17. FEATURE IMPORTANCE
==========================================================
*/

$html .= '

<div class="section page-break">

<h2>9. Feature Importance</h2>

';

if (!empty($featureImportance)) {

    $html .= '

    <table>

    <thead>

    <tr>

        <th>Rank</th>

        <th>Feature</th>

        <th>Importance</th>

    </tr>

    </thead>

    <tbody>

    ';

    $rank = 1;

    foreach (
        $featureImportance as $feature
    ) {

        if (!is_array($feature)) {

            continue;
        }

        $featureName = value(
            $feature,
            'feature',
            value(
                $feature,
                'name',
                'N/A'
            )
        );

        $importanceValue = value(
            $feature,
            'importance',
            value(
                $feature,
                'value',
                null
            )
        );

        $featureRank = value(
            $feature,
            'rank',
            $rank
        );

        $html .= '

        <tr>

        <td>
        ' . e($featureRank) . '
        </td>

        <td>
        ' . e($featureName) . '
        </td>

        <td>
        ' . numberValue(
            $importanceValue
        ) . '
        </td>

        </tr>

        ';

        $rank++;
    }

    $html .= '

    </tbody>

    </table>

    ';
} else {

    $html .= '

    <p>
        No feature importance data available.
    </p>

    ';
}

$html .= '

</div>
';


/*
==========================================================
 18. HYPERPARAMETER TUNING
==========================================================
*/

$html .= '

<div class="section">

<h2>10. Hyperparameter Tuning</h2>

';


if (!empty($tuning)) {

    $html .= '

    <table>

    <tbody>

    ';


    foreach (
        $tuning as $key => $tuningValue
    ) {

        /*
        |--------------------------------------------------------------------------
        | Safely convert the array key to string
        |--------------------------------------------------------------------------
        */

        $keyLabel = ucwords(
            str_replace(
                '_',
                ' ',
                (string)$key
            )
        );


        /*
        |--------------------------------------------------------------------------
        | Convert tuning value to readable text
        |--------------------------------------------------------------------------
        */

        $displayValue = readableValue(
            $tuningValue
        );


        $html .= '

        <tr>

        <td class="label">
        ' . e($keyLabel) . '
        </td>

        <td>
        ' . e($displayValue) . '
        </td>

        </tr>

        ';
    }


    $html .= '

    </tbody>

    </table>

    ';
} else {

    $html .= '

    <p>
        No hyperparameter tuning results available.
    </p>

    ';
}


$html .= '

</div>

';

/*
==========================================================
 19. REPORT FOOTER
==========================================================
*/

$html .= '

<hr>

<p class="small">

Generated by SimuRithm Intelligent Decision
Support System.

</p>

</body>

</html>
';


/*
==========================================================
 20. DOMPDF CONFIGURATION
==========================================================
*/

$options = new Options();

$options->set(
    'isHtml5ParserEnabled',
    true
);

$options->set(
    'isRemoteEnabled',
    true
);

$options->set(
    'defaultFont',
    'DejaVu Sans'
);


/*
==========================================================
 21. CREATE PDF
==========================================================
*/

try {

    $dompdf = new Dompdf(

        $options

    );


    $dompdf->loadHtml(

        $html

    );


    $dompdf->setPaper(

        'A4',

        'portrait'

    );


    $dompdf->render();


    /*
    |--------------------------------------------------------------------------
    | Clean Any Accidental Output
    |--------------------------------------------------------------------------
    */

    if (ob_get_length()) {

        ob_end_clean();
    }


    /*
    |--------------------------------------------------------------------------
    | Filename
    |--------------------------------------------------------------------------
    */

    $fileName =

        'SimuRithm_Analysis_Report_' .

        date('Ymd_His') .

        '.pdf';


    /*
    |--------------------------------------------------------------------------
    | PDF Headers
    |--------------------------------------------------------------------------
    */

    header(

        'Content-Type: application/pdf'

    );


    header(

        'Content-Disposition: attachment; filename="' .

            $fileName .

            '"'

    );


    header(

        'Cache-Control: private, max-age=0, must-revalidate'

    );


    header(

        'Pragma: public'

    );


    /*
    |--------------------------------------------------------------------------
    | Output PDF
    |--------------------------------------------------------------------------
    */

    echo $dompdf->output();


    exit;
} catch (Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | Clean Output
    |--------------------------------------------------------------------------
    */

    if (ob_get_level()) {

        ob_end_clean();
    }


    /*
    |--------------------------------------------------------------------------
    | Log Error
    |--------------------------------------------------------------------------
    */

    error_log(

        "SimuRithm PDF generation failed: " .

            $e->getMessage()

    );


    /*
    |--------------------------------------------------------------------------
    | Error Response
    |--------------------------------------------------------------------------
    */

    http_response_code(500);

    header(

        'Content-Type: text/plain; charset=UTF-8'

    );


    echo

    "PDF generation failed. " .

        $e->getMessage();


    exit;
}
