<?php
include '../connection.php';

// Fetch and sanitize POST data
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$country = isset($_POST['country']) ? $_POST['country'] : null;
$magnitude = isset($_POST['magnitude']) ? $_POST['magnitude'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;
$date = isset($_POST['date']) ? $_POST['date'] : null;
$time = isset($_POST['time']) ? $_POST['time'] : null;
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
$observatory_id = isset($_POST['observatory_id']) ? $_POST['observatory_id'] : null;

// Check
if (
    $id === 0 ||
    !$country || !$magnitude || !$type || !$date || !$time ||
    $latitude === null || $longitude === null || !$observatory_id
) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='manage_earthquakes.php';</script>";
    exit;
}

// Prepare the SQL UPDATE statement
$sql = "UPDATE earthquakes SET
    country = ?,
    magnitude = ?,
    type = ?,
    date = ?,
    time = ?,
    latitude = ?,
    longitude = ?,
    observatory_id = ?
    WHERE id = ?";
$params = [
    $country,
    $magnitude,
    $type,
    $date,
    $time,
    $latitude,
    $longitude,
    $observatory_id,
    $id
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>
    alert(" . json_encode("Error updating earthquake: $errors") . ");
    window.location.href='manage_earthquakes.php';
    </script>";
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_earthquakes.php?updated=1");
exit;
?>