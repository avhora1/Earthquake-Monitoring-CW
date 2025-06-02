<?php
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'].'/connection.php'; // defines $conn

$sql = "SELECT id, name, country, est_date, latitude, longitude FROM observatories ORDER BY name";
$stmt = sqlsrv_query($conn, $sql);
$result = [];
if ($stmt) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $result[] = [
            'id'        => $row['id'],
            'name'      => $row['name'],
            'country'   => $row['country'],
            'est_date'  => $row['est_date'] instanceof DateTime
                            ? $row['est_date']->format('Y-m-d')
                            : $row['est_date'],
            'latitude'  => (float)$row['latitude'],
            'longitude' => (float)$row['longitude']
        ];
    }
    sqlsrv_free_stmt($stmt);
}
echo json_encode($result);
exit;
?>