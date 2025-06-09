<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'].'/connection.php';

if (!isset($_GET['shelf']) || !isset($_GET['slot'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing shelf or slot']);
    exit;
}

$shelf = $_GET['shelf'];
$slot = 8 - (int)$_GET['slot'];

// 1. Get all artefacts for this shelf, in your desired order (by id, timestamp, etc)
$sql = "SELECT * FROM artefacts WHERE shelving_loc = ? ORDER BY id ASC";
$params = [$shelf];
$stmt = sqlsrv_query($conn, $sql, $params);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Query failed']);
    exit;
}

// 2. Find the Nth item (0-based)
$i = 0;
$nth_row = null;
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    if ($i == $slot) {
        $nth_row = $row;
        break;
    }
    $i++;
}

if ($nth_row !== null) {
    echo json_encode($nth_row);
} else {
    echo json_encode(['error' => 'Not found']);
}
?>