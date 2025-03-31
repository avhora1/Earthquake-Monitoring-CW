<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $est_date = $_POST['est_date'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $conn->prepare("INSERT INTO observatories (name, est_date, latitude, longitude) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssdd", $name, $est_date, $latitude, $longitude);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);

    // Redirect to the main page
    header("Location: add_observatory.php");
    exit();
}
?>