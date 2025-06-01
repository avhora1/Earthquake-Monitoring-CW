<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $est_date = $_POST['est_date'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Using SQL prepared statements to prevent SQL injection attacks
    
    $stmt = add_observatory($conn, $name, $est_date, $latitude, $longitude);

    // Free statement resources, ensures good memory management
    sqlsrv_free_stmt($stmt);

    sqlsrv_close($conn);

    header("Location: manage_observatoriesNew.php");
    exit();
}
?>