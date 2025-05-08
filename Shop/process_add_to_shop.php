<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artifact_id = $_POST['artifact_id'];
    $price = $_POST['price'];

    // Update the 'required' column to 'No' because if its in the shop, its no longer required
    $update_sql = "UPDATE artefacts SET required = 'No' WHERE id = ?";
    $update_params = array($artifact_id);
    $update_stmt = sqlsrv_query($conn, $update_sql, $update_params);

    // Debug if there are any errors 
    if ($update_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // SQL Prepared statements to protect against SQL injection attacks. 
    $insert_sql = "INSERT INTO stock_list (artifact_id, price, availability) VALUES (?, ?, 'Yes')";
    $insert_params = array($artifact_id, $price);
    $insert_stmt = sqlsrv_query($conn, $insert_sql, $insert_params);

    if ($insert_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        echo "Artefact added to shop successfully";
    }

    // Free up usage for good data management
    sqlsrv_free_stmt($update_stmt);
    sqlsrv_free_stmt($insert_stmt);
    sqlsrv_close($conn);

    header("Location: shop.php");
    exit();
}
?>