<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'].'/connection.php'; // defines $conn

$sql = "SELECT id, name FROM observatories ORDER BY name";
$stmt = sqlsrv_query($conn, $sql);
$result = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $result[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }
    sqlsrv_free_stmt($stmt);
}
echo json_encode($result);
exit;
?>