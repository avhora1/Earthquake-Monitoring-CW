<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artifact_id = $_POST['artifact_id'];

    // First, update the 'required' column to 'Yes' in artefacts table.
    // The subquery still works, because we delete from stock_list after.
    $update_artefact_sql = "UPDATE artefacts SET required = 'Yes' WHERE id = (SELECT artifact_id FROM stock_list WHERE id = ?)";
    $update_artefact_params = array($artifact_id);
    $update_artefact_stmt = sqlsrv_query($conn, $update_artefact_sql, $update_artefact_params);

    if ($update_artefact_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        sqlsrv_free_stmt($update_artefact_stmt);

        // Now delete the stock_list entry
        $delete_stock_sql = "DELETE FROM stock_list WHERE id = ?";
        $delete_stock_params = array($artifact_id);
        $delete_stock_stmt = sqlsrv_query($conn, $delete_stock_sql, $delete_stock_params);

        if ($delete_stock_stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            sqlsrv_free_stmt($delete_stock_stmt);
            sqlsrv_close($conn);
            // Success
            header("Location: shop.php");
            exit();
        }
    }
}
?>