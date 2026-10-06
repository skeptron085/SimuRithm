<!-- ==========================================================
     Feature Importance
=========================================================== -->

<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-bar-chart-fill text-success"></i>

                Feature Importance

            </h5>

            <span
                id="featureBadge"
                class="badge bg-success">

                0 Features

            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="alert alert-success">

            The chart below shows how much each feature contributed to
            the trained machine learning model.

        </div>

        <div class="row">

            <div class="col-lg-7">

                <canvas
                    id="featureChart"
                    height="280">
                </canvas>

            </div>

            <div class="col-lg-5">

                <div class="table-responsive">

                    <table
                        id="featureTable"
                        class="table table-bordered table-hover table-striped">

                        <thead class="table-success">

                            <tr>

                                <th>Rank</th>

                                <th>Feature</th>

                                <th>Importance</th>

                            </tr>

                        </thead>

                        <tbody id="featureBody">

                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center text-muted">

                                    Upload a dataset first.

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>