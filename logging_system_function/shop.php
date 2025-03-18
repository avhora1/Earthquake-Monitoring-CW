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
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Artifact ID</th><th>Type</th><th>Price (€)</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
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

        mysqli_close($conn);
        ?>
    </div>

    <button onclick="window.location.href='index.html'">Back to Main Page</button>
</body>
</html>