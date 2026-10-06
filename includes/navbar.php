<?php
require_once __DIR__ . '/../config/config.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">

    <div class="container-fluid">

        <button
            class="btn btn-outline-light btn-sm me-2 d-lg-none"
            type="button"
            id="sidebarToggle"
            aria-label="Open workflow navigation"
            aria-controls="simurithmSidebar"
            aria-expanded="false">

            <i class="bi bi-list"></i>

        </button>

        <!-- Brand -->
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>">

            <i class="bi bi-cpu-fill me-2"></i>

            <?= APP_NAME; ?>

            <small class="text-warning ms-2">
                v<?= APP_VERSION; ?>
            </small>

        </a>

        <!-- Mobile Toggle -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="navbarContent">

            <!-- Left Menu -->

            <ul class="navbar-nav me-auto">

                <li class="nav-item">

                    <a class="nav-link active" href="#">

                        <i class="bi bi-speedometer2"></i>

                        Dashboard

                    </a>

                </li>

            </ul>

            <!-- Right Menu -->

            <ul class="navbar-nav align-items-center">

                <!-- Flask Status -->

                <li class="nav-item me-3">

                    <span
                        id="flaskStatus"
                        class="badge bg-secondary">

                        Checking API...

                    </span>

                </li>

                <!-- Date -->

                <li class="nav-item me-3 text-white">

                    <i class="bi bi-calendar-event"></i>

                    <span id="currentDate"></span>

                </li>

                <!-- Time -->

                <li class="nav-item me-3 text-white">

                    <i class="bi bi-clock"></i>

                    <span id="currentTime"></span>

                </li>

                <!-- Refresh -->

                <li class="nav-item me-2">

                    <button
                        class="btn btn-outline-light btn-sm"
                        id="btnRefresh">

                        <i class="bi bi-arrow-clockwise"></i>

                    </button>

                </li>

                <!-- Fullscreen -->

                <li class="nav-item">

                    <button
                        class="btn btn-outline-light btn-sm"
                        id="btnFullscreen">

                        <i class="bi bi-arrows-fullscreen"></i>

                    </button>

                </li>

                <li class="nav-item me-2">

                    <a
                        href="#"
                        id="btnExportExcel"
                        class="btn btn-success">

                        <i class="bi bi-file-earmark-excel me-1"></i>

                        Export Excel

                    </a>

                </li>


                <li class="nav-item">

                    <a
                        href="#"
                        id="btnExportPDF"
                        class="btn btn-danger">

                        <i class="bi bi-file-earmark-pdf me-1"></i>

                        Export PDF

                    </a>

                </li>

            </ul>

        </div>

    </div>

</nav>