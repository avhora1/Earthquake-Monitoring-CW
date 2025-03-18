<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artifact_id = $_POST['artifact_id'];

    // Update the 'availability' column to 'No' in the stock_list table
    $update_stock_sql = "UPDATE stock_list SET availability = 'No' WHERE id = ?";
    $update_stock_stmt = $conn->prepare($update_stock_sql);
    $update_stock_stmt->bind_param("i", $artifact_id);

    if ($update_stock_stmt->execute()) {
        // Update the 'required' column to 'Yes' in the artefacts table
        $update_artefact_sql = "UPDATE artefacts SET required = 'Yes' WHERE id = (SELECT artifact_id FROM stock_list WHERE id = ?)";
        $update_artefact_stmt = $conn->prepare($update_artefact_sql);
        $update_artefact_stmt->bind_param("i", $artifact_id);
        $update_artefact_stmt->execute();
        $update_artefact_stmt->close();

        echo "Artefact removed from shop successfully";
    } else {
        echo "Error: " . $update_stock_stmt->error;
    }

    $update_stock_stmt->close();
    mysqli_close($conn);

    // Redirect to the shop page
    header("Location: shop.php");
    exit();
}
?>