<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// Get and sanitize id
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Check if the artefact is present in the shop (stock_list)
$sql = "SELECT COUNT(*) AS cnt FROM stock_list WHERE artifact_id = ? AND availability = 'Yes'";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if ($row && $row['cnt'] > 0) {
    // Present error to use and redirect
    header("Location: manage_pallets.php?shopviolation=1");
    exit;
}

// Now safe to delete
if ($id === 0) {
    echo "<script>alert('Invalid artefact ID.'); window.location.href='manage_pallets.php';</script>";
    exit;
}

$sql = "DELETE FROM artefacts WHERE id = ?";
$params = [ $id ];

$stmt = sqlsrv_query($conn, $sql, $params);

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