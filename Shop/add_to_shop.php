<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Artefact to Shop</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container, .table-container {
            margin: auto;
            margin-top: 30px;
            max-width: 800px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        h1, h2 {
            text-align: center;
            margin-top: 20px;
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
        .table {
            margin-top: 20px;
        }
        .no-artefacts {
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: #dc3545;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include '../header.php'; ?>

    <!-- Form Section -->
    <div class="form-container">
        <h1>Add Artefact to Shop</h1>
        <form id="addToShopForm" action="process_add_to_shop.php" method="POST" onsubmit="return validateAddToShopForm()">
            <div class="mb-3">
                <label for="artifact_id" class="form-label">Artefact ID:</label>
                <select id="artifact_id" name="artifact_id" class="form-select" required>
                    <?php
                    include '../connection.php';
                    $sql = "SELECT id FROM artefacts WHERE required = 'Yes'";
                    $result = sqlsrv_query($conn, $sql);
  
                    if ($result === false) {
                        die(print_r(sqlsrv_errors(), true)); // Debug errors if query fails
                    }
                    if (sqlsrv_has_rows($result)) {
                        while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($row['id']) . "'>ID " . htmlspecialchars($row['id']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No artefacts found</option>";
                    }
  
                    sqlsrv_close($conn);
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price (€):</label>
                <input type="number" step="0.01" id="price" name="price" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-warning">Submit</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>

    <!-- Artefacts List Section -->
    <div class="table-container">
        <h2>Artefacts List</h2>
        <?php
        include '../connection.php';
        $sql = "SELECT * FROM artefacts";
        $result = sqlsrv_query($conn, $sql);

        if ($result === false) {
            die('<div class="alert alert-danger">Error fetching data from the database.</div>');
        }

        if (sqlsrv_has_rows($result)) {
            echo "<table class='table table-dark table-striped table-hover'>";
            echo "<thead>";
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
                echo "<td>" . htmlspecialchars($row["id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["earthquake_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["type"] ?? "N/A") . "</td>";

                // Handle DateTime objects properly
                $timestamp = $row["time_stamp"] instanceof DateTime
                    ? $row["time_stamp"]->format('Y-m-d H:i:s')
                    : ($row["time_stamp"] ?? "N/A");
                echo "<td>" . htmlspecialchars($timestamp) . "</td>";

                echo "<td>" . htmlspecialchars($row["shelving_loc"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["pallet_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["required"] ?? "N/A") . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='no-artefacts'>No artefacts found.</p>";
        }

        sqlsrv_close($conn);
        ?>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateAddToShopForm() {
            const price = document.getElementById("price").value;
            if (price <= 0) {
                alert("Price must be greater than 0.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>