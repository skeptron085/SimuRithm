<!-- ==========================================================
     Benchmark Charts
=========================================================== -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-primary text-white">

        <div class="row align-items-center">

            <div class="col-md-4">

                <h5 class="mb-0">

                    <i class="bi bi-bar-chart-line-fill"></i>

                    Benchmark Charts

                </h5>

            </div>

            <div class="col-md-4">

                <label
                    for="benchmarkMetric"
                    class="visually-hidden">

                    Benchmark chart metric

                </label>

                <select
                    id="benchmarkMetric"
                    class="form-select">

                    <option value="accuracy">

                        Accuracy

                    </option>

                    <option value="precision">

                        Precision

                    </option>

                    <option value="recall">

                        Recall

                    </option>

                    <option value="f1">

                        F1 Score

                    </option>

                    <option value="cv_score">

                        Cross Validation

                    </option>

                    <option value="execution_time">

                        Execution Time

                    </option>

                    <option value="rmse">

                        RMSE

                    </option>

                    <option value="r2">

                        R² Score

                    </option>

                    <option value="silhouette">

                        Silhouette

                    </option>

                </select>

            </div>

            <div class="col-md-4">

                <label
                    for="benchmarkChartType"
                    class="visually-hidden">

                    Benchmark chart type

                </label>

                <select
                    id="benchmarkChartType"
                    class="form-select">

                    <option value="bar">

                        Bar Chart

                    </option>

                    <option value="line">

                        Line Chart

                    </option>

                    <option value="pie">

                        Pie Chart

                    </option>

                    <option value="doughnut">

                        Doughnut Chart

                    </option>

                    <option value="radar">

                        Radar Chart

                    </option>

                    <option value="polarArea">

                        Polar Area

                    </option>

                    <option value="scatter">

                        Scatter Plot

                    </option>

                </select>

            </div>

        </div>

    </div>

    <div class="card-body">

        <canvas
            id="benchmarkChart"
            height="120">

        </canvas>

    </div>

</div>