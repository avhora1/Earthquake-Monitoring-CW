<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Shop</h1>
    <h2>Available Artifacts</h2>
    <div id="shopTable">
        <?php
        include 'connection.php';
        $sql = "SELECT s.id, s.artifact_id, a.type, s.price FROM stock_list s JOIN artefacts a ON s.artifact_id = a.id WHERE s.availability = 'Yes'";
        $result = sqlsrv_query($conn, $sql);

        // Check if the query returned any rows so can see if there's an error server side. 
        if ($result === false) {
            die(print_r(sqlsrv_errors(), true));
        }

        if (sqlsrv_has_rows($result)) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Artifact ID</th><th>Type</th><th>Price (€)</th></tr>";
            while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
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

        sqlsrv_close($conn);
        ?>
    </div>

    <button onclick="window.location.href='index.html'">Back to Main Page</button>
</body>
</html>