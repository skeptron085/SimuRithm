<?php

if (
    !isset($_POST['analysis'])
) {

    http_response_code(400);

    exit("No analysis data received.");
}

$analysis = $_POST['analysis'];

?>

<form
    id="pdfForm"
    method="POST"
    action="../export_pdf.php">

    <input
        type="hidden"
        name="analysis"
        value="<?= htmlspecialchars($analysis, ENT_QUOTES); ?>">

</form>

<script>
    document.getElementById(
        "pdfForm"
    ).submit();
</script>