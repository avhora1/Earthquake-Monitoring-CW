<?php
include 'connection.php';

$sql = "SELECT * FROM observatories";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    echo "<table>";
    echo "<tr><th>ID</th><th>Name</th><th>Establishment Date</th><th>Latitude</th><th>Longitude</th></tr>";
    while($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>";
        echo "<td>" . $row["name"] . "</td>";
        echo "<td>" . $row["est_date"] . "</td>";
        echo "<td>" . $row["latitude"] . "</td>";
        echo "<td>" . $row["longitude"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No observatories found.";
}

mysqli_close($conn);
?>