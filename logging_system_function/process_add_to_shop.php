<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artifact_id = $_POST['artifact_id'];
    $price = $_POST['price'];

    // Update the 'required' column to 'No'
    $update_sql = "UPDATE artefacts SET required = 'No' WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $artifact_id);
    $update_stmt->execute();
    $update_stmt->close();

    // Insert into stock_list
    $insert_sql = "INSERT INTO stock_list (artifact_id, price, availability) VALUES (?, ?, 'Yes')";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("id", $artifact_id, $price);

    if ($insert_stmt->execute()) {
        echo "Artefact added to shop successfully";
    } else {
        echo "Error: " . $insert_stmt->error;
    }

    $insert_stmt->close();
    mysqli_close($conn);

    // Redirect to the shop page
    header("Location: shop.php");
    exit();
}
?>