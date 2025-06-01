<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
require_once '../queryLibrary.php';

date_default_timezone_set('Europe/London');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $earthquake_id = $_POST['earthquake_id'];
    $type = $_POST['type'];
    $shelving_loc = $_POST['shelving_loc'];
    $datetime_variable = new DateTime("now");
    $time_stamp = $datetime_variable->format('Y-m-d H:i:s');
    $description = $_POST['description'] ?? '';

    //calling function to add artefacts
    $stmt = add_new_artefact($conn, $earthquake_id, $type, $shelving_loc, $datetime_variable, $time_stamp, $description);

    if ($stmt === false) {
        // Print error to see why query failed (to fix it)
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "New artefact record created successfully";
    }

   
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    header("Location: add_artefact.php");
    exit();
}
?>