<!-- ==========================================================
     Intelligent Recommendation
=========================================================== -->

<div class="card border-success shadow-sm mb-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">

            <i class="bi bi-stars"></i>

            Intelligent Algorithm Recommendation

        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3 text-center">

                <i
                    class="bi bi-award-fill text-warning"
                    style="font-size:90px;">
                </i>

            </div>

            <div class="col-md-9">

                <h3
                    id="recommendedAlgorithm"
                    class="text-success">

                    No Recommendation

                </h3>

                <hr>

                <p>

                    <strong>Machine Learning Task</strong>

                    <br>

                    <span id="recommendedTask">

                        -

                    </span>

                </p>

                <p>

                    <strong>Performance Score</strong>

                    <br>

                    <span id="recommendedScore">

                        -

                    </span>

                </p>

                <p>

                    <strong>Recommendation Reason</strong>

                    <br>

                    <span id="recommendedReason">

                        Upload a dataset to generate a recommendation.

                    </span>

                </p>

                <div class="table-responsive mt-3" id="recommendationByCategoryWrap" style="display:none;">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Category</th>
                                <th>Recommended Model</th>
                                <th>Metric</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody id="recommendationByCategory"></tbody>
                    </table>
                </div>

            </div>

        </div>

    </div>

</div>