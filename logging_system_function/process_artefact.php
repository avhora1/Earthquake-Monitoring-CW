<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $earthquake_id = $_POST['earthquake_id'];
    $type = $_POST['type'];
    $shelving_loc = $_POST['shelving_loc'];
    $time_stamp = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO artefacts (earthquake_id, type, time_stamp, shelving_loc) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $earthquake_id, $type, $time_stamp, $shelving_loc);

    if ($stmt->execute()) {
        echo "New artefact record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);

    // Redirect to the main page
    header("Location: add_artefact.php");
    exit();
}
?>