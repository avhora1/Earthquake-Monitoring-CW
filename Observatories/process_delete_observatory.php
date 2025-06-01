<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

// Try to delete; if it's linked to earthquakes, FK constraint will fail

delete_observatory($conn, $id);

sqlsrv_close($conn);

header("Location: manage_observatoriesNew.php");
exit;
?>