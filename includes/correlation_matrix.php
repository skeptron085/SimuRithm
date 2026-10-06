<!-- ==========================================================
     Correlation Matrix
=========================================================== -->

<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-grid-3x3-gap-fill text-primary"></i>

                Correlation Matrix

            </h5>

            <span
                id="correlationBadge"
                class="badge bg-primary">

                0 Variables

            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="alert alert-info">

            <i class="bi bi-info-circle-fill"></i>

            Pearson Correlation values range from
            <strong>-1.00</strong>
            to
            <strong>+1.00</strong>.

        </div>

        <div class="table-responsive">

            <table
                id="correlationTable"
                class="table table-bordered table-hover table-striped w-100">

                <thead id="correlationHead">

                </thead>

                <tbody id="correlationBody">

                    <tr>

                        <td class="text-center text-muted">

                            Upload a dataset to compute the correlation matrix.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>