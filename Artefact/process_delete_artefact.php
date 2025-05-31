<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// Get and sanitize id
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id === 0) {
    echo "<script>alert('Invalid artefact ID.'); window.location.href='manage_artefacts.php';</script>";
    exit;
}
$sql1 = "UPDATE shelves SET capacity = capacity + 1 WHERE shelf = (SELECT shelving_loc FROM artefacts WHERE id = ?)";
$sql2 = "DELETE FROM artefacts WHERE id = ?";
$params = [ $id ];
$stmt1 = sqlsrv_query($conn, $sql1, $params);
$stmt2 = sqlsrv_query($conn, $sql2, $params);

if ($stmt2 === false) {
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>alert('Error deleting artefact: $errors'); window.location.href='manage_artefacts.php';</script>";
    exit;
}

sqlsrv_free_stmt($stmt1);
sqlsrv_free_stmt($stmt2);
sqlsrv_close($conn);

header("Location: manage_artefacts.php?deleted=1");
exit;
?>