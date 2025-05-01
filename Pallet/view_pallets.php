<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Pallets</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light grey background */
        }
        h1 {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            color: #212529;
        }
        .table-container {
            margin-top: 50px;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div class="container mt-4">
        <h1>Pallets List</h1>
        <?php
        include '../connection.php';
        $sql = "SELECT * FROM pallets";
        $result = sqlsrv_query($conn, $sql);

        if ($result === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($result)) {
            echo "<table class='table table-dark table-striped table-bordered table-hover'>";
            echo "<thead class='thead-dark'>";
            echo "<tr><th scope='col'>ID</th><th scope='col'>Pallet Size</th><th scope='col'>Arrival Date</th></tr>";
            echo "</thead><tbody>";

            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                // Ensure fields are non-empty or provide fallback
                echo "<td>" . htmlspecialchars($row["id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["pallet_size"] ?? "N/A") . "</td>";

                $arrival_date = $row["arrival_date"] instanceof DateTime
                    ? $row["arrival_date"]->format('Y-m-d H:i:s')
                    : ($row["arrival_date"] ?? "N/A");
                echo "<td>" . htmlspecialchars($arrival_date) . "</td>";

                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-warning' role='alert'>No pallets found.</div>";
        }
        sqlsrv_close($conn);
        ?>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>