<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Artefacts</title>
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
    <div class="table-container container">
        <h1>Artefacts List</h1>
        <?php
        include '../connection.php';

        $sql = "SELECT * FROM artefacts";
        $result = sqlsrv_query($conn, $sql);

        if ($result === false) {
            die(print_r(sqlsrv_errors(), true)); // Debug if query fails
        }

        if (sqlsrv_has_rows($result)) {
            echo "<table class='table table-dark table-striped table-bordered table-hover'>";
            echo "<thead class='thead-dark'>";
            echo "<tr>
                    <th scope='col'>ID</th>
                    <th scope='col'>Earthquake ID</th>
                    <th scope='col'>Type</th>
                    <th scope='col'>Timestamp</th>
                    <th scope='col'>Shelving Location</th>
                    <th scope='col'>Pallet ID</th>
                    <th scope='col'>Required</th>
                  </tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                // Handle each field: Check for NULL values before passing to htmlspecialchars()
                echo "<td>" . htmlspecialchars($row["id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["earthquake_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["type"] ?? "N/A") . "</td>";

                // Handle the 'time_stamp' column for DateTime or NULL cases
                $timestamp = $row["time_stamp"] instanceof DateTime
                    ? $row["time_stamp"]->format('Y-m-d H:i:s')
                    : ($row["time_stamp"] ?? "N/A");
                echo "<td>" . htmlspecialchars($timestamp) . "</td>";

                echo "<td>" . htmlspecialchars($row["shelving_loc"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["pallet_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["required"] ?? "N/A") . "</td>";
                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
        } else {
            echo "<div class='alert alert-warning' role='alert'>No artefacts found.</div>";
        }

        sqlsrv_close($conn);
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>