<?php
require_once __DIR__ . '/../config/config.php';
?>

<div class="card">

    <div class="card-header">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h4 class="mb-0">

                    <i class="bi bi-cloud-arrow-up-fill text-primary"></i>

                    Upload Dataset

                </h4>

                <small class="text-muted">

                    Upload CSV, XLS or XLSX dataset for analysis

                </small>

            </div>

            <span
                id="datasetStatus"
                class="badge bg-secondary">

                No Dataset

            </span>

        </div>

    </div>

    <div class="card-body">

        <!-- Upload Area -->

        <div
            id="uploadArea"
            class="upload-area">

            <div class="upload-icon">

                <i class="bi bi-file-earmark-arrow-up"></i>

            </div>

            <div class="upload-text">

                Drag & Drop Dataset Here

            </div>

            <div class="text-muted mt-2">

                or

            </div>

            <button
                type="button"
                class="btn btn-primary mt-3"
                id="btnBrowse">

                <i class="bi bi-folder2-open"></i>

                Browse Dataset

            </button>

            <label for="datasetFile" class="visually-hidden">
                Choose a dataset file
            </label>

            <input
                type="file"
                id="datasetFile"
                name="datasetFile"
                hidden
                accept=".csv,.xlsx,.xls">

        </div>

        <hr>

        <!-- File Information -->

        <div
            id="fileInformation"
            style="display:none;">

            <div class="row">

                <div class="col-md-6">

                    <table class="table table-bordered">

                        <tbody>

                            <tr>

                                <th width="35%">

                                    File Name

                                </th>

                                <td id="fileName">

                                    -

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Extension

                                </th>

                                <td id="fileExtension">

                                    -

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    File Size

                                </th>

                                <td id="fileSize">

                                    -

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="col-md-6">

                    <table class="table table-bordered">

                        <tbody>

                            <tr>

                                <th width="35%">

                                    Status

                                </th>

                                <td>

                                    <span
                                        id="uploadStatus"
                                        class="badge bg-warning">

                                        Waiting

                                    </span>

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Supported

                                </th>

                                <td>

                                    CSV

                                    |

                                    XLS

                                    |

                                    XLSX

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Max Size

                                </th>

                                <td>

                                    1 GB

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

        <!-- Progress -->

        <div
            id="uploadProgressContainer"
            style="display:none;">

            <label class="mb-2">

                Upload Progress

            </label>

            <div class="progress">

                <div

                    id="uploadProgress"

                    class="progress-bar progress-bar-striped progress-bar-animated"

                    role="progressbar"

                    style="width:0%;">

                    0%

                </div>

            </div>

        </div>

        <hr>

        <!-- Buttons -->

        <div class="text-end">

            <button

                class="btn btn-secondary"

                id="btnClear">

                <i class="bi bi-x-circle"></i>

                Clear

            </button>

            <button

                class="btn btn-success"

                id="btnUpload">

                <i class="bi bi-upload"></i>

                Upload Dataset

            </button>

        </div>

    </div>

</div>