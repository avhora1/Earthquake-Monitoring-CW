<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Artefact</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add Artefact</h1>
    <form id="artefactForm" action="process_artefact.php" method="POST" onsubmit="return validateArtefactForm()">
        <label for="earthquake_id">Earthquake:</label>
        <select id="earthquake_id" name="earthquake_id" required>
            <?php
            include 'connection.php';
            $sql = "SELECT id, country, date FROM earthquakes";
            $result = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<option value='" . $row['id'] . "'>" . $row['country'] . " - " . $row['date'] . "</option>";
            }
            mysqli_close($conn);
            ?>
        </select><br><br>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="solidified lava">Solidified Lava</option>
            <option value="foreign debris">Foreign Debris (Tephra)</option>
            <option value="ash sample">Ash Sample</option>
            <option value="ground soil">Ground Soil</option>
        </select><br><br>

        <label for="shelving_loc">Shelving Location:</label>
        <select id="shelving_loc" name="shelving_loc" required>
            <?php
            foreach (range('A', 'L') as $char) {
                echo "<option value='$char'>$char</option>";
            }
            ?>
        </select><br><br>

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
            echo "<tr><th>ID</th><th>Earthquake ID</th><th>Type</th><th>Timestamp</th><th>Shelving Location</th><th>Pallet ID</th></tr>";
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["earthquake_id"] . "</td>";
                echo "<td>" . $row["type"] . "</td>";
                echo "<td>" . $row["time_stamp"] . "</td>";
                echo "<td>" . $row["shelving_loc"] . "</td>";
                echo "<td>" . $row["pallet_id"] . "</td>";
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