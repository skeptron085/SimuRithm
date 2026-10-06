<!-- ==========================================================
     Loading Modal
=========================================================== -->

<div
    class="modal fade"
    id="loadingModal"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content shadow">

            <div class="modal-body text-center p-5">

                <!-- Spinner -->

                <div
                    class="spinner-border text-primary mb-4"
                    style="width:70px;height:70px;"
                    role="status">

                    <span class="visually-hidden">

                        Loading...

                    </span>

                </div>

                <!-- Title -->

                <h4 class="fw-bold mb-2">

                    Processing Dataset

                </h4>

                <!-- Current Step -->

                <p
                    id="loadingText"
                    class="text-muted">

                    Preparing analysis...

                </p>

                <!-- Progress -->

                <div class="progress mb-3">

                    <div
                        id="loadingProgress"
                        class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                        role="progressbar"
                        style="width:0%;">

                        0%

                    </div>

                </div>

                <!-- Steps -->

                <div class="text-start mt-4">

                    <small
                        id="stepUpload"
                        class="text-secondary d-block">

                        ⏳ Uploading Dataset

                    </small>

                    <small
                        id="stepStatistics"
                        class="text-secondary d-block">

                        ⏳ Computing Statistics

                    </small>

                    <small
                        id="stepNLP"
                        class="text-secondary d-block">

                        ⏳ NLP Header Analysis

                    </small>

                    <small
                        id="stepCorrelation"
                        class="text-secondary d-block">

                        ⏳ Correlation Analysis

                    </small>

                    <small
                        id="stepBenchmark"
                        class="text-secondary d-block">

                        ⏳ Benchmarking Algorithms

                    </small>

                    <small
                        id="stepRecommendation"
                        class="text-secondary d-block">

                        ⏳ Generating Recommendation

                    </small>

                </div>

            </div>

        </div>

    </div>

</div>