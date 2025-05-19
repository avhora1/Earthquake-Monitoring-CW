<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$sql = "DELETE FROM pallets WHERE id = ?";
$params = [$id];

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    // Is this a FK violation? (SQLSTATE 23000, Code 547)
    $errors = sqlsrv_errors();
    $fk_violation = false;
    foreach ($errors as $error) {
        if (
            isset($error['SQLSTATE']) && $error['SQLSTATE'] == '23000' &&
            isset($error['code']) && $error['code'] == 547
        ) {
            $fk_violation = true;
            break;
        }
    }
    if ($fk_violation) {
        header("Location: manage_pallets.php?fkviolation=1");
    } else {
        header("Location: manage_pallets.php?dberror=1");
    }
    exit;
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

header("Location: manage_pallets.php?deleted=1");
exit;
?>