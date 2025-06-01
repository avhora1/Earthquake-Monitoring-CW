<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';
date_default_timezone_set('Europe/London');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pallet_size = $_POST['pallet_size'];
    $datetime_variable = new DateTime("now");
    $arrival_date = $datetime_variable->format('Y-m-d H:i:s');

    

    $stmt = add_pallets($conn, $pallet_size, $arrival_date);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "New pallet created successfully";
    }
    // Retrieve the last inserted ID in pallets using ident_current() to set up the last_pallet_id variable 
   
    $last_pallet_id = retrieve_last_pallet_ID($conn);

    // Free resources for good resource management
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    header("Location: add_artefact_to_pallet.php?pallet_id=" . $last_pallet_id);
    exit();
}
?>