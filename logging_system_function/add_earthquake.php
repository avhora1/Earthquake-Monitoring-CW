<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Earthquake</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Earthquake</h1>
    <form id="earthquakeForm" action="process_earthquake.php" method="POST" onsubmit="return validateEarthquakeForm()">
        <label for="country">Country:</label>
        <input type="text" id="country" name="country" required><br><br>

        <label for="magnitude">Magnitude:</label>
        <input type="number" step="0.1" id="magnitude" name="magnitude" required><br><br>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="tectonic">Tectonic</option>
            <option value="volcanic">Volcanic</option>
            <option value="collapse">Collapse</option>
            <option value="explosion">Explosion</option>
        </select><br><br>

        <label for="date">Date:</label>
        <input type="date" id="date" name="date" required><br><br>

        <label for="time">Time:</label>
        <input type="time" id="time" name="time" required><br><br>

        <label for="latitude">Latitude:</label>
        <input type="number" step="0.000001" id="latitude" name="latitude" required><br><br>

        <label for="longitude">Longitude:</label>
        <input type="number" step="0.000001" id="longitude" name="longitude" required><br><br>

        <label for="observatory_id">Observatory:</label>
        <select id="observatory_id" name="observatory_id" required>
            <?php
            include 'connection.php';
            $sql = "SELECT id, name FROM observatories";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            mysqli_close($conn);
            ?>
        </select><br><br>

        <input type="submit" value="Submit">
    </form>

    <button onclick="window.location.href='index.html'">Back to Main Page</button>

    <h2>Earthquakes List</h2>
    <div id="earthquakesTable">
        <?php
        include 'connection.php';
        $sql = "SELECT * FROM earthquakes";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) > 0) {
            echo "<table>";
            echo "<tr><th>ID</th><th>Country</th><th>Magnitude</th><th>Type</th><th>Date</th><th>Time</th><th>Latitude</th><th>Longitude</th><th>Observatory ID</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["country"] . "</td>";
                echo "<td>" . $row["magnitude"] . "</td>";
                echo "<td>" . $row["type"] . "</td>";
                echo "<td>" . $row["date"] . "</td>";
                echo "<td>" . $row["time"] . "</td>";
                echo "<td>" . $row["latitude"] . "</td>";
                echo "<td>" . $row["longitude"] . "</td>";
                echo "<td>" . $row["observatory_id"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No earthquakes found.";
        }

        mysqli_close($conn);
        ?>
    </div>

    <script src="script.js"></script>
</body>
</html>