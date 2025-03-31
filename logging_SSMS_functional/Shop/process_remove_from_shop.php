<?php
include '../connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $artifact_id = $_POST['artifact_id'];

    // Update the 'availability' column to 'No' in the stock_list table, this maintains a history of what was in the shop
    // And can be expanded later if someones buy's something (availability = No)
    $update_stock_sql = "UPDATE stock_list SET availability = 'No' WHERE id = ?";
    $update_stock_params = array($artifact_id);
    $update_stock_stmt = sqlsrv_query($conn, $update_stock_sql, $update_stock_params);

    if ($update_stock_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    } else {
        // Update the 'required' column to 'Yes' in the artefacts table because when removing from shop, the artefact is by default required. 
        $update_artefact_sql = "UPDATE artefacts SET required = 'Yes' WHERE id = (SELECT artifact_id FROM stock_list WHERE id = ?)";
        $update_artefact_params = array($artifact_id);
        $update_artefact_stmt = sqlsrv_query($conn, $update_artefact_sql, $update_artefact_params);

        if ($update_artefact_stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            echo "Artefact removed from shop successfully";
        }

        sqlsrv_free_stmt($update_artefact_stmt);
    }

    sqlsrv_free_stmt($update_stock_stmt);
    sqlsrv_close($conn);

    header("Location: shop.php");
    exit();
}
?>