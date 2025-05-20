<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Earthquakes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        h1 { text-align: center; margin-top: 20px; font-weight: bold; color: #212529; }
        .table-container { margin-top: 30px; max-width: 98%; margin-left: auto; margin-right: auto; }
        .modal { color: #000; }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div class="table-container container">
        <h1>Earthquakes List</h1>
        <?php
        include '../connection.php';

        // Fetch earthquakes and join with observatories for better context
        $sql = "SELECT e.id, e.country, e.magnitude, e.type, e.date, e.time, e.latitude, e.longitude, o.name AS observatory_name
                FROM earthquakes e
                LEFT JOIN observatories o ON e.observatory_id = o.id";
        $result = sqlsrv_query($conn, $sql);

        if (sqlsrv_has_rows($result)) {
            // Start the table with Bootstrap styles
            echo "<table class='table table-dark table-striped table-bordered table-hover'>";
            echo "<thead class='thead-dark'>";
            echo "<tr>
                    <th>ID</th>
                    <th>Country</th>
                    <th>Magnitude</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Latitude</th>
                    <th>Longitude</th>
                    <th>Observatory</th>
                  </tr>";
            echo "</thead>";
            echo "<tbody>";

            // Iterate through the results to populate the table
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["country"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["magnitude"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["type"]) . "</td>";

                // Format date if it's a DateTime object
                if ($row["date"] instanceof DateTime) {
                    echo "<td>" . htmlspecialchars($row["date"]->format('Y-m-d')) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($row["date"]) . "</td>";
                }

                // Format time if it's a DateTime object
                if ($row["time"] instanceof DateTime) {
                    echo "<td>" . htmlspecialchars($row["time"]->format('H:i:s')) . "</td>";
                } else {
                    echo "<td>" . htmlspecialchars($row["time"]) . "</td>";
                }

                echo "<td>" . htmlspecialchars($row["latitude"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["longitude"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["observatory_name"]) . "</td>";
                echo "</tr>";
            }
            echo "</tbody>";
            echo "</table>";
        } else {
            // No data found
            echo "<div class='alert alert-warning' role='alert'>No earthquakes found.</div>";
        }

        // Close the database connection
        sqlsrv_close($conn);
        ?>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>