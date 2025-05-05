<?php
include '../connection.php';

// Fetch and sanitize form input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$earthquake_id = isset($_POST['earthquake_id']) ? intval($_POST['earthquake_id']) : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;
$shelving_loc = isset($_POST['shelving_loc']) ? $_POST['shelving_loc'] : null;
$pallet_id = !empty($_POST['pallet_id']) ? intval($_POST['pallet_id']) : null;
$required = isset($_POST['required']) ? $_POST['required'] : 'No';

// Check
if ($id === 0 || !$type || !$shelving_loc || !$required) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='manage_artefacts.php';</script>";
    exit;
}

// Prepare statement
$sql = "UPDATE artefacts SET
            earthquake_id = ?,
            type = ?,
            shelving_loc = ?,
            pallet_id = ?,
            required = ?
        WHERE id = ?";
$params = [
    $earthquake_id,
    $type,
    $shelving_loc,
    $pallet_id,
    $required,
    $id
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Debug error
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>alert('Error updating artefact: $errors'); window.location.href='manage_artefacts.php';</script>";
    exit;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Success!
echo "<script>alert('Artefact updated successfully.'); window.location.href='manage_artefacts.php';</script>";
exit;
?>