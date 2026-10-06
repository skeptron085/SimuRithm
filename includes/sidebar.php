```php
<?php
/*
|--------------------------------------------------------------------------
| SimuRithm Sidebar
|--------------------------------------------------------------------------
| Release 1.3
|--------------------------------------------------------------------------
| Purpose:
|   - Main navigation for SimuRithm
|   - Process-based page navigation
|   - Active page highlighting
|   - Responsive Bootstrap sidebar
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| Detect Current Page
|--------------------------------------------------------------------------
*/

$currentPage = basename($_SERVER['PHP_SELF']);


/*
|--------------------------------------------------------------------------
| Helper Function
|--------------------------------------------------------------------------
|
| Determines whether the current page belongs to a menu item.
|
*/

function sidebarActive($pages)
{
    global $currentPage;

    if (!is_array($pages)) {

        $pages = [$pages];
    }

    return in_array($currentPage, $pages, true)
        ? 'active'
        : '';
}


/*
|--------------------------------------------------------------------------
| Dashboard Active State
|--------------------------------------------------------------------------
*/

$dashboardActive = sidebarActive([
    'dashboard.php'
]);

?>


<!-- ================================================================
     SIMURITHM SIDEBAR
================================================================ -->

<aside
    id="simurithmSidebar"
    class="simurithm-sidebar">


    <!-- ============================================================
         SIDEBAR BRAND
    ============================================================= -->

    <div class="sidebar-brand">


        <a
            href="dashboard.php"
            class="sidebar-brand-link">


            <!-- Logo Icon -->

            <div class="sidebar-logo">

                <i class="bi bi-cpu-fill"></i>

            </div>


            <!-- Brand Text -->

            <div class="sidebar-brand-text">

                <span class="sidebar-brand-name">

                    <?= defined('APP_NAME')
                        ? APP_NAME
                        : 'SimuRithm'
                    ?>

                </span>

                <small>

                    Intelligent ML Decision Support

                </small>

            </div>


        </a>


    </div>


    <!-- ============================================================
         SIDEBAR NAVIGATION
    ============================================================= -->

    <div class="sidebar-navigation">


        <!-- ========================================================
             MAIN
        ========================================================= -->

        <div class="sidebar-section-title">

            MAIN

        </div>


        <!-- Dashboard -->

        <a
            href="dashboard.php"
            class="sidebar-menu-item <?= $dashboardActive ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-grid-1x2-fill"></i>

            </span>


            <span class="sidebar-menu-text">

                Dashboard

            </span>


        </a>


        <!-- ========================================================
             DATASET
        ========================================================= -->

        <div class="sidebar-section-title">

            DATASET

        </div>


        <!-- Upload -->

        <a
            href="pages/upload.php"
            class="sidebar-menu-item <?= sidebarActive('upload.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-cloud-arrow-up-fill"></i>

            </span>


            <span class="sidebar-menu-text">

                Upload Dataset

            </span>


        </a>


        <!-- Dataset Overview -->

        <a
            href="pages/dataset_overview.php"
            class="sidebar-menu-item <?= sidebarActive('dataset_overview.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-table"></i>

            </span>


            <span class="sidebar-menu-text">

                Dataset Overview

            </span>


        </a>


        <!-- Dataset Analysis -->

        <a
            href="pages/dataset_analysis.php"
            class="sidebar-menu-item <?= sidebarActive('dataset_analysis.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-search"></i>

            </span>


            <span class="sidebar-menu-text">

                Dataset Analysis

            </span>


        </a>


        <!-- ========================================================
             ANALYSIS
        ========================================================= -->

        <div class="sidebar-section-title">

            ANALYSIS

        </div>


        <!-- Target & Task -->

        <a
            href="pages/target_task.php"
            class="sidebar-menu-item <?= sidebarActive('target_task.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-bullseye"></i>

            </span>


            <span class="sidebar-menu-text">

                Target & Task

            </span>


        </a>


        <!-- Statistics -->

        <a
            href="pages/statistics.php"
            class="sidebar-menu-item <?= sidebarActive('statistics.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-bar-chart-line"></i>

            </span>


            <span class="sidebar-menu-text">

                Statistics

            </span>


        </a>


        <!-- Correlation -->

        <a
            href="pages/correlation.php"
            class="sidebar-menu-item <?= sidebarActive('correlation.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-diagram-3"></i>

            </span>


            <span class="sidebar-menu-text">

                Correlation

            </span>


        </a>


        <!-- ========================================================
             MACHINE LEARNING
        ========================================================= -->

        <div class="sidebar-section-title">

            MACHINE LEARNING

        </div>


        <!-- Benchmark -->

        <a
            href="pages/benchmark.php"
            class="sidebar-menu-item <?= sidebarActive('benchmark.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-cpu"></i>

            </span>


            <span class="sidebar-menu-text">

                Benchmark

            </span>


        </a>


        <!-- Recommendation -->

        <a
            href="pages/recommendation.php"
            class="sidebar-menu-item <?= sidebarActive('recommendation.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-stars"></i>

            </span>


            <span class="sidebar-menu-text">

                Recommendation

            </span>


        </a>


        <!-- Feature Importance -->

        <a
            href="pages/feature_importance.php"
            class="sidebar-menu-item <?= sidebarActive('feature_importance.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-bar-chart-steps"></i>

            </span>


            <span class="sidebar-menu-text">

                Feature Importance

            </span>


        </a>


        <!-- Hyperparameter Tuning -->

        <a
            href="pages/tuning.php"
            class="sidebar-menu-item <?= sidebarActive('tuning.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-sliders"></i>

            </span>


            <span class="sidebar-menu-text">

                Hyperparameter Tuning

            </span>


        </a>


        <!-- ========================================================
             RESULTS
        ========================================================= -->

        <div class="sidebar-section-title">

            RESULTS

        </div>


        <!-- Charts -->

        <a
            href="pages/charts.php"
            class="sidebar-menu-item <?= sidebarActive('charts.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-graph-up"></i>

            </span>


            <span class="sidebar-menu-text">

                Charts

            </span>


        </a>


        <!-- Reports -->

        <a
            href="pages/reports.php"
            class="sidebar-menu-item <?= sidebarActive('reports.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-file-earmark-text"></i>

            </span>


            <span class="sidebar-menu-text">

                Reports

            </span>


        </a>


        <!-- ========================================================
             SYSTEM
        ========================================================= -->

        <div class="sidebar-section-title">

            SYSTEM

        </div>


        <!-- Analysis History -->

        <a
            href="pages/history.php"
            class="sidebar-menu-item <?= sidebarActive('history.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-clock-history"></i>

            </span>


            <span class="sidebar-menu-text">

                Analysis History

            </span>


        </a>


        <!-- System Logs -->

        <a
            href="pages/logs.php"
            class="sidebar-menu-item <?= sidebarActive('logs.php') ?>">


            <span class="sidebar-menu-icon">

                <i class="bi bi-journal-text"></i>

            </span>


            <span class="sidebar-menu-text">

                System Logs

            </span>


        </a>


    </div>


    <!-- ============================================================
         SIDEBAR FOOTER
    ============================================================= -->

    <div class="sidebar-footer">


        <div class="sidebar-footer-divider"></div>


        <!-- Application Version -->

        <div class="sidebar-version">


            <span>

                <i class="bi bi-info-circle"></i>

                SimuRithm

            </span>


            <span class="sidebar-version-number">

                <?= defined('APP_VERSION')
                    ? APP_VERSION
                    : '1.0'
                ?>

            </span>


        </div>


        <!-- API Status -->

        <div
            class="sidebar-api-status"
            id="sidebarApiStatus">


            <span class="api-status-indicator"></span>


            <span>

                API Status

            </span>


            <span
                class="api-status-text"
                id="sidebarApiStatusText">

                Checking...

            </span>


        </div>


    </div>


</aside>


<!-- ================================================================
     SIDEBAR MOBILE OVERLAY
================================================================ -->

<div
    id="sidebarOverlay"
    class="sidebar-overlay">
</div>


<!-- ================================================================
     SIDEBAR TOGGLE BUTTON
================================================================ -->

<button
    type="button"
    id="sidebarToggle"
    class="sidebar-toggle"
    aria-label="Toggle sidebar">


    <i class="bi bi-list"></i>


</button>
```