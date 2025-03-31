<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove from Shop</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Remove Artefact from Shop</h1>
    <form id="removeFromShopForm" action="process_remove_from_shop.php" method="POST" onsubmit="return validateRemoveFromShopForm()">
        <label for="artifact_id">Stock ID:</label>
        <select id="artifact_id" name="artifact_id" required>
            <?php
            include '../connection.php';
            // Gets the stock id so you can select the artefact that you'd like to remove. 
            $sql = "SELECT id FROM stock_list WHERE availability = 'Yes'";
            $stmt = sqlsrv_query($conn, $sql);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo "<option value='" . $row['id'] . "'>" . $row['id'] . "</option>";
            }
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($conn);
            ?>
        </select><br><br>

        <input type="submit" value="Submit">
    </form>

    <button onclick="window.location.href='../index.html'">Back to Main Page</button>

    <h2>Artifacts in Shop</h2>
    <div id="shopTable">
        <?php
        include '../connection.php';
        $sql = "SELECT s.id, s.artifact_id, a.type, s.price FROM stock_list s JOIN artefacts a ON s.artifact_id = a.id WHERE s.availability = 'Yes'";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($stmt)) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Artifact ID</th><th>Type</th><th>Price (€)</th></tr>";
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["artifact_id"] . "</td>";
                echo "<td>" . $row["type"] . "</td>";
                echo "<td>" . $row["price"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No artefacts available.";
        }
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($conn);
        ?>
    </div>

    <script src="../script.js"></script>
</body>
</html>