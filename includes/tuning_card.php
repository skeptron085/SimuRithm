<!-- ==========================================================
     Hyperparameter Tuning
=========================================================== -->

<div class="card mb-4">

    <div class="card-header bg-dark text-white">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-sliders"></i>

                Hyperparameter Optimization

            </h5>

            <span
                id="tuningBadge"
                class="badge bg-warning text-dark">

                Waiting

            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="alert alert-warning">

            SimuRithm automatically searches for the optimal
            hyperparameters to improve model performance.

        </div>

        <div class="row">

            <!-- Left -->

            <div class="col-lg-4">

                <div class="card border-success">

                    <div class="card-body text-center">

                        <h6>

                            Best Cross Validation Score

                        </h6>

                        <h2
                            id="bestCVScore"
                            class="text-success">

                            -

                        </h2>

                        <small class="text-muted d-block" id="tuningAlgorithm"></small>

                    </div>

                </div>

            </div>

            <!-- Right -->

            <div class="col-lg-8">

                <table
                    class="table table-bordered table-hover">

                    <thead class="table-dark">

                        <tr>

                            <th width="40%">

                                Hyperparameter

                            </th>

                            <th>

                                Best Value

                            </th>

                        </tr>

                    </thead>

                    <tbody id="tuningBody">

                        <tr>

                            <td colspan="2"
                                class="text-center text-muted">

                                No tuning results yet.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>