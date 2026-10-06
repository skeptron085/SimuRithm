<!-- ==========================================================
     Benchmark Results
=========================================================== -->

<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-speedometer2 text-primary"></i>

                Machine Learning Benchmark

            </h5>

            <span
                id="benchmarkBadge"
                class="badge bg-primary">

                0 Algorithms

            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="alert alert-primary">

            SimuRithm automatically benchmarks multiple machine learning
            algorithms and compares their performance.

        </div>

        <div class="table-responsive">

            <table
                id="benchmarkTable"
                class="table table-bordered table-striped table-hover w-100">

                <thead class="table-primary">

                    <tr>

                        <th>Rank</th>

                        <th>Category</th>

                        <th>Algorithm</th>

                        <th>Target</th>

                        <th>Primary Metric</th>

                        <th>Score</th>

                        <th>Accuracy</th>

                        <th>Cross Validation</th>

                        <th>Precision</th>

                        <th>Recall</th>

                        <th>F1</th>

                        <th>R² / RMSE</th>

                        <th>Silhouette</th>

                        <th>Time (sec)</th>

                        <th>Status</th>

                    </tr>

                </thead>

                <tbody id="benchmarkBody">

                    <tr>

                        <td colspan="15"
                            class="text-center text-muted">

                            Upload a dataset to benchmark algorithms.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>