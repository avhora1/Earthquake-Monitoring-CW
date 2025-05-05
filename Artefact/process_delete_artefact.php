<?php
include '../connection.php';

// Get and sanitize id
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id === 0) {
    echo "<script>alert('Invalid artefact ID.'); window.location.href='manage_artefacts.php';</script>";
    exit;
}

$sql = "DELETE FROM artefacts WHERE id = ?";
$params = [ $id ];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>alert('Error deleting artefact: $errors'); window.location.href='manage_artefacts.php';</script>";
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo "<script>alert('Artefact deleted successfully.'); window.location.href='manage_artefacts.php';</script>";
exit;
?>