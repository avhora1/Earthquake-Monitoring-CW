<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
date_default_timezone_set('Europe/London');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pallet_size = $_POST['pallet_size'];
    $datetime_variable = new DateTime("now");
    $arrival_date = $datetime_variable->format('Y-m-d H:i:s');

    $sql = "INSERT INTO pallets (pallet_size, arrival_date)
            VALUES (?, CONVERT(DATETIME, ?, 120))"; // Need to explicitly convert to datetime here, otherwise SQL can't interpret properly.

    $params = array($pallet_size, $arrival_date);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "New artefact created successfully";
    }
    // Retrieve the last inserted ID in pallets using ident_current() to set up the last_pallet_id variable 
    $last_id_query = "SELECT ident_current('pallets') AS last_pallet_id";
    $id_stmt = sqlsrv_query($conn, $last_id_query);

    if ($id_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($id_stmt, SQLSRV_FETCH_ASSOC);
    if ($row === false || !isset($row['last_pallet_id'])) {
        die("No pallet ID was retrieved.");
    }
    $last_pallet_id = $row['last_pallet_id'];

    // Free resources for good resource management
    sqlsrv_free_stmt($stmt);
    sqlsrv_free_stmt($id_stmt);
    sqlsrv_close($conn);

    header("Location: add_artefact_to_pallet.php?pallet_id=" . $last_pallet_id);
    exit();
}
?>