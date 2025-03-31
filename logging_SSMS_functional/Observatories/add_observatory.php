<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Observatory</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
    <h1>Add Observatory</h1>
    <form id="observatoryForm" action="process_form.php" method="POST" onsubmit="return validateForm()">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="est_date">Establishment Date:</label>
        <input type="date" id="est_date" name="est_date" required><br><br>

        <label for="latitude">Latitude:</label>
        <input type="number" step="0.000001" id="latitude" name="latitude" required><br><br>

        <label for="longitude">Longitude:</label>
       <input type="number" step="0.000001" id="longitude" name="longitude" required><br><br>

        <input type="submit" value="Submit">
    </form>

    <button onclick="window.location.href='../index.html'">Back to Main Page</button>

    <h2>Observatories List</h2>
    <div id="observatoriesTable">
        <?php include 'fetch_observatories.php'; ?>
    </div>

    <script src="../script.js"></script>
</body>
</html>