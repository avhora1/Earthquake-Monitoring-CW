<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

date_default_timezone_set('Europe/London');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $earthquake_id = $_POST['earthquake_id'];
    $pallet_id = $_POST['pallet_id'];
    $type = $_POST['type'];
    $shelving_loc = $_POST['shelving_loc'];
    $description = $_POST['description'];
    $datetime_variable = new DateTime("now");
    $time_stamp = $datetime_variable->format('Y-m-d H:i:s');

    $sql = "INSERT INTO artefacts (earthquake_id, pallet_id, type, time_stamp, shelving_loc, description) 
            VALUES (?, ?, ?, CONVERT(DATETIME, ?, 120), ?, ?)"; // Need to explicitly convert otherwise SQL won't interpret correctly

    $params = array($earthquake_id, $pallet_id, $type, $time_stamp, $shelving_loc, $description);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Print error to see why query failed (to fix it)
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "New artefact record created successfully";
    }

    sqlsrv_free_stmt($stmt);

    sqlsrv_close($conn);

    // Redirect to the page for adding artefacts linked to the pallet
    header("Location: add_artefact_to_pallet.php?pallet_id=" . $pallet_id);
    exit();
}
?>