<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Artefact to Pallet</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Light grey background */
        }
        .form-container {
            margin: auto;
            margin-top: 50px;
            max-width: 600px;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
            color: #212529;
        }
        .form-label {
            font-weight: 500;
        }
        .btn-warning {
            background-color: #ffc107;
        }
        .btn-secondary {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>
    <div class="form-container">
        <h1>Add Artefact to Pallet</h1>
        <form id="artefactForm" action="process_artefact_to_pallet.php" method="POST" onsubmit="return validateArtefactForm()">
            <input type="hidden" id="pallet_id" name="pallet_id" value="<?php echo htmlspecialchars($_GET['pallet_id'] ?? ''); ?>">

            <div class="mb-3">
                <label for="earthquake_id" class="form-label">Earthquake:</label>
                <select id="earthquake_id" name="earthquake_id" class="form-select" required>
                    <?php
                    include '../connection.php';
                    $sql = "SELECT id, country, date FROM earthquakes";
                    $result = sqlsrv_query($conn, $sql);

                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    if (sqlsrv_has_rows($result)) {
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                            $formattedDate = $row["date"] instanceof DateTime
                                ? $row["date"]->format('Y-m-d')
                                : ($row["date"] ?? "N/A");
                            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['country'] ?? "N/A") . " - " . htmlspecialchars($formattedDate) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No earthquakes found</option>";
                    }

                    sqlsrv_close($conn);
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="type" class="form-label">Type:</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="solidified lava">Solidified Lava</option>
                    <option value="foreign debris">Foreign Debris (Tephra)</option>
                    <option value="ash sample">Ash Sample</option>
                    <option value="ground soil">Ground Soil</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="shelving_loc" class="form-label">Shelving Location:</label>
                <select id="shelving_loc" name="shelving_loc" class="form-select" required>
                    <?php
                    foreach (range('A', 'L') as $char) {
                        echo "<option value='" . htmlspecialchars($char) . "'>$char</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-warning">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>

    <div class="container mt-4">
        <h2>Artefacts List</h2>
        <?php
        include '../connection.php';
        $sql = "SELECT * FROM artefacts";
        $result = sqlsrv_query($conn, $sql);

        if ($result === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($result)) {
            echo "<table class='table table-dark table-striped table-bordered table-hover'>";
            echo "<thead class='thead-dark'>";
            echo "<tr><th>ID</th><th>Earthquake ID</th><th>Type</th><th>Timestamp</th><th>Shelving Location</th><th>Pallet ID</th><th>Required</th></tr>";
            echo "</thead><tbody>";

            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["earthquake_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["type"] ?? "N/A") . "</td>";

                $time_stamp = $row["time_stamp"] instanceof DateTime
                    ? $row["time_stamp"]->format('Y-m-d H:i:s')
                    : ($row["time_stamp"] ?? "N/A");
                echo "<td>" . htmlspecialchars($time_stamp) . "</td>";

                echo "<td>" . htmlspecialchars($row["shelving_loc"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["pallet_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["required"] ?? "N/A") . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='alert alert-warning' role='alert'>No artefacts found.</div>";
        }

        sqlsrv_close($conn);
        ?>
    </div>
    <!-- Include Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>