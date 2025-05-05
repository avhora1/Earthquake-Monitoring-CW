<?php
include '../connection.php';

// Get and sanitize id
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id === 0) {
    echo "<script>alert('Invalid earthquake ID.'); window.location.href='manage_artefacts.php';</script>";
    exit;
}

$sql = "DELETE FROM earthquakes WHERE id = ?";
$params = [$id];
$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Look for foreign key violation
    $errors = sqlsrv_errors();
    $fk_violation = false;
    foreach ($errors as $error) {
        if (
            // SQLSTATE 23000 is constraint violation, code 547 is for foreign key reference
            (isset($error['SQLSTATE']) && $error['SQLSTATE'] == '23000') &&
            (isset($error['code']) && $error['code'] == 547)
        ) {
            $fk_violation = true;
            break;
        }
    }
    if ($fk_violation) {
        // Redirect with message flag in the URL
        header("Location: manage_earthquakes.php?fkviolation=1");
        exit;
    } else {
        // Some other SQL error
        header("Location: manage_earthquakes.php?dberror=1");
        exit;
    }
}
// Success
header("Location: manage_earthquakes.php?deleted=1");
exit;