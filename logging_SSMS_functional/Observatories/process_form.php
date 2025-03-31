<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $est_date = $_POST['est_date'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Using SQL prepared statements to prevent SQL injection attacks
    $sql = "INSERT INTO observatories (name, est_date, latitude, longitude) VALUES (?, ?, ?, ?)";

    $params = array($name, $est_date, $latitude, $longitude);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Free statement resources, ensures good memory management
    sqlsrv_free_stmt($stmt);

    sqlsrv_close($conn);

    header("Location: add_observatory.php");
    exit();
}
?>