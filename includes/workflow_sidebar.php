<?php
require_once __DIR__ . '/../config/config.php';
?>

<aside id="simurithmSidebar" class="simurithm-sidebar" aria-label="Analysis workflow">
    <a href="#upload" class="sidebar-brand-link" data-workflow-page="upload">
        <span class="sidebar-logo" aria-hidden="true"><i class="bi bi-cpu-fill"></i></span>
        <span class="sidebar-brand-text">
            <strong><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></strong>
            <small>Machine Learning Model Selection</small>
        </span>
    </a>

    <div class="sidebar-navigation">
        <div class="sidebar-section-title">ANALYSIS WORKFLOW</div>
        <nav class="workflow-navigation" aria-label="Workflow pages">
            <a href="#upload" class="sidebar-menu-item" data-workflow-page="upload" aria-current="page">
                <span class="sidebar-step-number">01</span>
                <span class="sidebar-menu-icon"><i class="bi bi-cloud-arrow-up-fill"></i></span>
                <span class="sidebar-menu-text">Upload dataset</span>
            </a>
            <a href="#explore" class="sidebar-menu-item" data-workflow-page="explore">
                <span class="sidebar-step-number">02</span>
                <span class="sidebar-menu-icon"><i class="bi bi-table"></i></span>
                <span class="sidebar-menu-text">Explore data</span>
            </a>
            <a href="#configure" class="sidebar-menu-item" data-workflow-page="configure">
                <span class="sidebar-step-number">03</span>
                <span class="sidebar-menu-icon"><i class="bi bi-sliders"></i></span>
                <span class="sidebar-menu-text">Configure analysis</span>
            </a>
            <a href="#results" class="sidebar-menu-item" data-workflow-page="results">
                <span class="sidebar-step-number">04</span>
                <span class="sidebar-menu-icon"><i class="bi bi-graph-up-arrow"></i></span>
                <span class="sidebar-menu-text">Results</span>
            </a>
        </nav>
    </div>

    <div class="sidebar-footer">
        <span class="sidebar-status-dot" aria-hidden="true"></span>
        <span><?= htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8') ?></span>
        <small>v<?= htmlspecialchars((string) APP_VERSION, ENT_QUOTES, 'UTF-8') ?></small>
    </div>
</aside>