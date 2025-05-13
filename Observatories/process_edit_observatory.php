<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = $_POST['name'] ?? '';
$est_date = $_POST['est_date'] ?? '';
$latitude = $_POST['latitude'] ?? '';
$longitude = $_POST['longitude'] ?? '';

if ($id === 0 || !$name || !$est_date || $latitude === '' || $longitude === '') {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='manage_observatories.php';</script>";
    exit;
}

$sql = "UPDATE observatories SET name=?, est_date=?, latitude=?, longitude=? WHERE id=?";
$params = [$name, $est_date, $latitude, $longitude, $id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $errors = json_encode(print_r(sqlsrv_errors(), true));
    echo "<script>alert($errors); window.location.href='manage_observatories.php';</script>";
    exit;
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_observatories.php?updated=1");
exit;
?>