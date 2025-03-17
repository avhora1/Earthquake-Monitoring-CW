<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pallet_size = $_POST['pallet_size'];
    $arrival_date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO pallets (pallet_size, arrival_date) VALUES (?, ?)");
    $stmt->bind_param("ss", $pallet_size, $arrival_date);

    if ($stmt->execute()) {
        $last_pallet_id = $conn->insert_id;
        echo "New pallet record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    mysqli_close($conn);

    // Redirect to the page for adding artefacts linked to the pallet
    header("Location: add_artefact_to_pallet.php?pallet_id=" . $last_pallet_id);
    exit();
}
?>