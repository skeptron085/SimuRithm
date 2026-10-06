<!-- ==========================================================
     NLP Header Analysis
=========================================================== -->

<div class="card mb-4">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <h5 class="mb-0">

                <i class="bi bi-cpu-fill text-primary"></i>

                NLP Header Analysis

            </h5>

            <span
                id="nlpBadge"
                class="badge bg-primary">

                0 Columns

            </span>

        </div>

    </div>

    <div class="card-body">

        <div class="alert alert-info">

            <i class="bi bi-info-circle"></i>

            SimuRithm analyzes every dataset header using Natural Language
            Processing (NLP) to determine its semantic role in the machine
            learning workflow.

        </div>

        <div class="table-responsive">

            <table
                id="nlpTable"
                class="table table-bordered table-hover table-striped w-100">

                <thead class="table-primary">

                    <tr>

                        <th width="60">#</th>

                        <th>Column Name</th>

                        <th>Extracted Keywords</th>

                        <th>Data Type</th>

                        <th width="220">Detected Role</th>

                    </tr>

                </thead>

                <tbody id="nlpBody">

                    <tr>

                        <td colspan="4"
                            class="text-center text-muted">

                            Upload a dataset to begin NLP analysis.

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>