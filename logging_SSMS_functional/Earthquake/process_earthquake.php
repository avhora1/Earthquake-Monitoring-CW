<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $country = $_POST['country'];
    $magnitude = $_POST['magnitude'];
    $type = $_POST['type'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $observatory_id = $_POST['observatory_id'];

    // SQL query with placeholders because we want to do SQL prepared statements (they're safer from SQL injection attacks)
    $sql = "INSERT INTO earthquakes (country, magnitude, type, date, time, latitude, longitude, observatory_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $params = array($country, $magnitude, $type, $date, $time, $latitude, $longitude, $observatory_id);

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        // Print error to see why query failed (to fix it)
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "New earthquake record created successfully";
    }

    // Free the statement resources so it doesn't take up anymore memory server side 
    sqlsrv_free_stmt($stmt);

    sqlsrv_close($conn);

    header("Location: add_earthquake.php");
    exit();
}
?>