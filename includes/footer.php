<?php
require_once __DIR__ . '/../config/config.php';
?>

<footer class="bg-white border-top mt-5 py-3 shadow-sm">

    <div class="container-fluid">

        <div class="row align-items-center">

            <!-- Left -->

            <div class="col-md-4">

                <h6 class="mb-1 fw-bold text-primary">

                    <i class="bi bi-cpu-fill"></i>

                    <?= APP_NAME ?>

                </h6>

                <small class="text-muted">

                    Intelligent Decision Support System for Automated Machine Learning Model Selection

                </small>

            </div>

            <!-- Center -->

            <div class="col-md-4 text-center">

                <span class="badge bg-primary">

                    Version <?= APP_VERSION ?>

                </span>

                <span class="badge bg-success">

                    PHP <?= PHP_VERSION ?>

                </span>

                <span
                    id="footerApiStatus"
                    class="badge bg-secondary">

                    Checking API...

                </span>

            </div>

            <!-- Right -->

            <div class="col-md-4 text-md-end">

                <small class="text-muted">

                    Last Analysis

                </small>

                <br>

                <strong id="lastAnalysis">

                    No analysis yet

                </strong>

            </div>

        </div>

        <hr>

        <div class="row">

            <div class="col-md-6">

                <small class="text-muted">

                    © <?= date("Y") ?>

                    <?= APP_NAME ?>

                    | Developed by

                    <?= APP_DEVELOPER ?>

                    | Documented by

                    <?= APP_DOCUMENTORS ?>

                </small>

            </div>

            <div class="col-md-6 text-md-end">

                <small class="text-muted">

                    Bootstrap 5

                    |

                    Chart.js

                    |

                    Flask

                    |

                    XAMPP

                </small>

            </div>

        </div>

    </div>

</footer>