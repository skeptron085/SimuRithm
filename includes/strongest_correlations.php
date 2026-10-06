<!-- ==========================================================
     Strongest Correlations
=========================================================== -->

<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-graph-up-arrow text-success"></i>

                Strongest Correlations

            </h5>

            <span
                id="strongCorrelationBadge"
                class="badge bg-success">

                0 Relationships

            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="alert alert-success">

            <i class="bi bi-lightbulb-fill"></i>

            SimuRithm automatically extracts the strongest positive and
            negative relationships from the correlation matrix.

        </div>

        <div class="table-responsive">

            <table
                id="strongCorrelationTable"
                class="table table-bordered table-hover table-striped w-100">

                <thead class="table-success">

                    <tr>

                        <th width="70">Rank</th>

                        <th>Variable 1</th>

                        <th>Variable 2</th>

                        <th width="140">Correlation</th>

                        <th width="220">Interpretation</th>

                    </tr>

                </thead>

                <tbody id="strongCorrelationBody">

                    <tr>

                        <td colspan="5"
                            class="text-center text-muted">

                            Upload a dataset to identify the strongest correlations.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>