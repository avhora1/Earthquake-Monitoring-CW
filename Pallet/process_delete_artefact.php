<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';

// Get and sanitize id
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

//deleting the artefact
$stmt = delete_artefact($conn, $id);

if ($stmt === false) {
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>alert('Error deleting artefact: $errors'); window.location.href='manage_pallets.php';</script>";
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_pallets.php?deleted_artefact=1");
exit;
?>