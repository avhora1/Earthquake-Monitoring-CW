<?php
include 'connection.php';

$sql = "SELECT * FROM observatories";
$result = sqlsrv_query($conn, $sql);

if (sqlsrv_has_rows($result)) { 
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Establishment Date</th><th>Latitude</th><th>Longitude</th></tr>";

    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";

        // Check if 'est_date' is a DateTime object and format it 
        // because SQL server returns DATETIME columns as DateTime objects, so est_date column is DateTime object and NOT plain strings
        if ($row["est_date"] instanceof DateTime) {
            echo "<td>" . $row["est_date"]->format('Y-m-d') . "</td>";
        } else {
            echo "<td>" . $row["est_date"] . "</td>"; // Fallback for unexpected types
        }

        echo "<td>" . $row["latitude"] . "</td>";
        echo "<td>" . $row["longitude"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No observatories found.";
}

sqlsrv_close($conn);
?>