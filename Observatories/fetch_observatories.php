<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// Query to fetch observatories
$sql = "SELECT * FROM observatories";
$result = sqlsrv_query($conn, $sql);

if (sqlsrv_has_rows($result)) {
    // Start Bootstrap-styled table
    echo "<table class='table table-dark table-striped table-bordered table-hover'>";
    echo "<thead class='thead-dark'>";
    echo "<tr>
            <th scope='col'>ID</th>
            <th scope='col'>Name</th>
            <th scope='col'>Establishment Date</th>
            <th scope='col'>Latitude</th>
            <th scope='col'>Longitude</th>
          </tr>";
    echo "</thead>";
    echo "<tbody>";

    // Loop through the results and output table rows
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["name"]) . "</td>";

        // Check if 'est_date' is a DateTime object and format it
        if ($row["est_date"] instanceof DateTime) {
            echo "<td>" . htmlspecialchars($row["est_date"]->format('Y-m-d')) . "</td>";
        } else {
            echo "<td>" . htmlspecialchars($row["est_date"]) . "</td>"; // Fallback for unexpected types
        }

        echo "<td>" . htmlspecialchars($row["latitude"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["longitude"]) . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>"; // End table
} else {
    // Output message when no rows are returned
    echo "<div class='alert alert-warning' role='alert'>No observatories found.</div>";
}

// Close the database connection
sqlsrv_close($conn);
?>