<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
require_once '../queryLibrary.php';

// Get and sanitize id
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id === 0) {
    echo "<script>alert('Invalid artefact ID.'); window.location.href='manage_artefacts.php';</script>";
    exit;
}
//calling the function that deletes artefacts
$stmt = delete_artefact($conn, $id);

if ($stmt2 === false) {
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>alert('Error deleting artefact: $errors'); window.location.href='manage_artefacts.php';</script>";
    exit;
}


sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_artefactsNew.php");
exit;
?>