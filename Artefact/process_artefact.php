<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

date_default_timezone_set('Europe/London');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $earthquake_id = $_POST['earthquake_id'];
    $type = $_POST['type'];
    $shelving_loc = $_POST['shelving_loc'];
    $datetime_variable = new DateTime("now");
    $time_stamp = $datetime_variable->format('Y-m-d H:i:s');

    // SQL query with placeholders because we want to do SQL prepared statements (they're safer from SQL injection attacks)
    $sql = "INSERT INTO artefacts (earthquake_id, type, time_stamp, shelving_loc) 
            VALUES (?, ?, CONVERT(DATETIME, ?, 120), ?)"; // Need to explicitly convert the datetime so SQL can interpret the generated time stamp, doesn't work otherwise

    $params = array($earthquake_id, $type, $time_stamp, $shelving_loc);

    $stmt = sqlsrv_query($conn, $sql, $params);

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