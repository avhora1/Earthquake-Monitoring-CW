<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

// Fetch and sanitize form input
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$earthquake_id = isset($_POST['earthquake_id']) ? $_POST['earthquake_id'] : null;
$type = isset($_POST['type']) ? $_POST['type'] : null;
$shelving_loc = isset($_POST['shelving_loc']) ? $_POST['shelving_loc'] : null;
$pallet_id = !empty($_POST['pallet_id']) ? intval($_POST['pallet_id']) : null;

// Check
if ($id === 0 || !$type || !$shelving_loc) {
    echo "<script>alert('Please fill in all required fields.'); window.location.href='manage_artefacts.php';</script>";
    exit;
}
// increasing the old shelf by 1
$sqlCapInc = "UPDATE shelves SET capacity = capacity + 1 WHERE shelf = (SELECT shelving_loc FROM artefacts WHERE id = ?);";
$params = array($id);
sqlsrv_query($conn, $sqlCapInc, $params);

// Prepare statement
$sql = "UPDATE artefacts SET
            earthquake_id = ?,
            type = ?,
            shelving_loc = ?,
            pallet_id = ?
        WHERE id = ?";
$params = [
    $earthquake_id,
    $type,
    $shelving_loc,
    $pallet_id,
    $id
];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Debug error
    $errors = print_r(sqlsrv_errors(), true);
    echo "<script>alert('Error updating artefact: $errors'); window.location.href='manage_artefacts.php';</script>";
    exit;
}
// update the capacity on the shelves
$sqlCapDec = "UPDATE shelves SET capacity = capacity - 1 WHERE shelf = ? AND capacity > 0";
$params = array($shelving_loc);
sqlsrv_query($conn, $sqlCapDec, $params);

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// Success in process_edit_artefact.php
header("Location: manage_artefacts.php?updated=1");
exit;
?>