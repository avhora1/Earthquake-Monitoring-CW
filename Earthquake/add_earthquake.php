<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Earthquake</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f8f9fa;
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
        <h1>Add Earthquake</h1>
        <form id="earthquakeForm" action="process_earthquake.php" method="POST" onsubmit="return validateEarthquakeForm()">
            <div class="mb-3">
                <label for="country" class="form-label">Country:</label>
                <input type="text" id="country" name="country" class="form-control" required placeholder="Enter country name">
            </div>
            <div class="mb-3">
                <label for="magnitude" class="form-label">Magnitude:</label>
                <input type="number" step="0.1" id="magnitude" name="magnitude" class="form-control" required placeholder="Enter magnitude">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type:</label>
                <select id="type" name="type" class="form-select" required>
                    <option value="tectonic">Tectonic</option>
                    <option value="volcanic">Volcanic</option>
                    <option value="collapse">Collapse</option>
                    <option value="explosion">Explosion</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Date:</label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="time" class="form-label">Time:</label>
                <input type="time" id="time" name="time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="latitude" class="form-label">Latitude:</label>
                <input type="number" step="0.000001" id="latitude" name="latitude" class="form-control" required placeholder="e.g., 51.5074">
            </div>
            <div class="mb-3">
                <label for="longitude" class="form-label">Longitude:</label>
                <input type="number" step="0.000001" id="longitude" name="longitude" class="form-control" required placeholder="e.g., -0.1278">
            </div>
            <div class="mb-3">
                <label for="observatory_id" class="form-label">Observatory:</label>
                <select id="observatory_id" name="observatory_id" class="form-select" required>
                    <?php
                    include '../connection.php';
                    $sql = "SELECT id, name FROM observatories";
                    $result = sqlsrv_query($conn, $sql);
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    sqlsrv_close($conn);
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-warning">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../script.js"></script>
</body>
</html>