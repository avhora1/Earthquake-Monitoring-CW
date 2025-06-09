<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'].'/connection.php'; // provides $conn (SQLSRV resource)

// Adjust if your table or column are named differently.
$sql = "SELECT DISTINCT country FROM earthquakes WHERE country IS NOT NULL AND country <> '' ORDER BY country ASC";
$stmt = sqlsrv_query($conn, $sql);

$countries = [];
if ($stmt !== false) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $countries[] = ['name' => $row['country']];
    }
}

echo json_encode($countries);
exit;
?>