<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$pallet_size = $_POST['pallet_size'] ?? '';

if ($id === 0 || !$pallet_size) {
    echo "<script>alert('Fill in all fields.'); window.location.href='manage_pallets.php';</script>";
    exit;
}

$sql = "UPDATE pallets SET pallet_size = ? WHERE id = ?";
$params = [$pallet_size, $id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $errors = json_encode(print_r(sqlsrv_errors(), true));
    echo "<script>alert($errors); window.location.href='manage_pallets.php';</script>";
    exit;
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_pallets.php?updated=1");
exit;
?>