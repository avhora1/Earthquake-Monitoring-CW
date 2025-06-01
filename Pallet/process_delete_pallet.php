<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;


$stmt =delete_pallet($conn, $id);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_pallets.php?deleted=1");
exit;
?>