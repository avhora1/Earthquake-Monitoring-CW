<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $country = $_POST['country'];
    $magnitude = $_POST['magnitude'];
    $type = $_POST['type'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $observatory_id = $_POST['observatory_id'];

    $stmt = $conn->prepare("INSERT INTO earthquakes (country, magnitude, type, date, time, latitude, longitude, observatory_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssddi", $country, $magnitude, $type, $date, $time, $latitude, $longitude, $observatory_id);

    if ($stmt->execute()) {
        echo "New earthquake record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);

    // Redirect to the main page
    header("Location: add_earthquake.php");
    exit();
}
?>