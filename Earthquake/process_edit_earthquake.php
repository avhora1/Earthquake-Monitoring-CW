<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';


// Fetch and sanitize POST data
$id = isset($_POST['id']) ? ($_POST['id']) : 0;
$country = isset($_POST['country']) ? $_POST['country'] : null;
$magnitude = isset($_POST['magnitude']) ? $_POST['magnitude'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;
$date = isset($_POST['date']) ? $_POST['date'] : null;
$time = isset($_POST['time']) ? $_POST['time'] : null;
$latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
$longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
$observatory_id = isset($_POST['observatory_id']) ? $_POST['observatory_id'] : null;

$stmt =  edit_earthquake($conn, $id, $country, $magnitude, $type, $date, $time, $latitude, $longitude, $observatory_id);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_earthquakes.php?updated=1");
exit;
?>