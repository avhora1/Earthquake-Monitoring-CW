<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Pallet</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Pallet</h1>
    <form id="palletForm" action="process_pallet.php" method="POST" onsubmit="return validatePalletForm()">
        <label for="pallet_size">Pallet Size:</label>
        <select id="pallet_size" name="pallet_size" required>
            <option value="half">Half</option>
            <option value="full">Full</option>
        </select><br><br>

        <input type="submit" value="Submit">
    </form>

    <button onclick="window.location.href='index.html'">Back to Main Page</button>

    <h2>Pallets List</h2>
    <div id="palletsTable">
        <?php
        include 'connection.php';
        $sql = "SELECT * FROM pallets";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Pallet Size</th><th>Arrival Date</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["pallet_size"] . "</td>";
                echo "<td>" . $row["arrival_date"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No pallets found.";
        }

        mysqli_close($conn);
        ?>
    </div>

    <script src="script.js"></script>
</body>
</html>