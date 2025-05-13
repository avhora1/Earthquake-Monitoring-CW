<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Artefact from Shop</title>
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
        .btn-danger {
            background-color: #dc3545;
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
        <h1>Remove Artefact from Shop</h1>
        <form id="removeFromShopForm" action="process_remove_from_shop.php" method="POST" onsubmit="return validateRemoveFromShopForm()">
            <div class="mb-3">
                <label for="artifact_id" class="form-label">Stock ID:</label>
                <select id="artifact_id" name="artifact_id" class="form-select" required>
                    <?php
                    include '../connection.php';
                    $sql = "SELECT id FROM stock_list WHERE availability = 'Yes'";
                    $stmt = sqlsrv_query($conn, $sql);

                    if ($stmt === false) {
                        die(print_r(sqlsrv_errors(), true));
                    }

                    // Populate options for Stock IDs
                    if (sqlsrv_has_rows($stmt)) {
                        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                            echo "<option value='" . htmlspecialchars($row['id']) . "'>Stock ID: " . htmlspecialchars($row['id']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No stock items found</option>";
                    }

                    sqlsrv_free_stmt($stmt);
                    sqlsrv_close($conn);
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-danger">Remove</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href='../index.php'">Back to Main Page</button>
        </form>
    </div>

    <!-- Shop Table Section -->
    <div class="table-container">
        <h2>Artifacts in Shop</h2>
        <?php
        include '../connection.php';
        $sql = "SELECT s.id, s.artifact_id, a.type, s.price FROM stock_list s JOIN artefacts a ON s.artifact_id = a.id WHERE s.availability = 'Yes'";
        $stmt = sqlsrv_query($conn, $sql);

        if ($stmt === false) {
            die('<div class="alert alert-danger">Error fetching data from the database.</div>');
        }

        if (sqlsrv_has_rows($stmt)) {
            echo "<table class='table table-dark table-striped table-hover'>";
            echo "<thead>";
            echo "<tr>
                    <th scope='col'>Stock ID</th>
                    <th scope='col'>Artifact ID</th>
                    <th scope='col'>Type</th>
                    <th scope='col'>Price (€)</th>
                </tr>";
            echo "</thead>";
            echo "<tbody>";

            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["artifact_id"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars($row["type"] ?? "N/A") . "</td>";
                echo "<td>" . htmlspecialchars(number_format($row["price"] ?? 0, 2)) . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
        } else {
            echo "<p class='no-artefacts'>No artefacts available in the shop.</p>";
        }

        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validateRemoveFromShopForm() {
            const artifactId = document.getElementById("artifact_id").value;
            if (!artifactId) {
                alert("Please select a Stock ID to remove.");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>