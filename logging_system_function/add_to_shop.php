<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add to Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Artefact to Shop</h1>
    <form id="addToShopForm" action="process_add_to_shop.php" method="POST" onsubmit="return validateAddToShopForm()">
        <label for="artifact_id">Artefact ID:</label>
        <select id="artifact_id" name="artifact_id" required>
            <?php
            include 'connection.php';
            $sql = "SELECT id FROM artefacts WHERE required = 'Yes'";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . $row['id'] . "</option>";
            }
            mysqli_close($conn);
            ?>
        </select><br><br>

        <label for="price">Price (€):</label>
        <input type="number" step="0.01" id="price" name="price" required><br><br>

        <input type="submit" value="Submit">
    </form>

    <button onclick="window.location.href='index.html'">Back to Main Page</button>

    <h2>Artefacts List</h2>
    <div id="artefactsTable">
        <?php
        include 'connection.php';
        $sql = "SELECT * FROM artefacts";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Earthquake ID</th><th>Type</th><th>Timestamp</th><th>Shelving Location</th><th>Pallet ID</th><th>Required</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["earthquake_id"] . "</td>";
                echo "<td>" . $row["type"] . "</td>";
                echo "<td>" . $row["time_stamp"] . "</td>";
                echo "<td>" . $row["shelving_loc"] . "</td>";
                echo "<td>" . $row["pallet_id"] . "</td>";
                echo "<td>" . $row["required"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No artefacts found.";
        }

        mysqli_close($conn);
        ?>
    </div>

    <script src="script.js"></script>
</body>
</html>