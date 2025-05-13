<?php
session_start();

// Check for valid ID in URL (GET method)
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Remove artefact from the session basket if present
    if (isset($_SESSION['basket'][$id])) {
        unset($_SESSION['basket'][$id]);
        header("Location: basket.php?removed=1");
        exit;
    }
    // If not present, then redirect to basket
    else {
        header("Location: basket.php");
        exit;
    }
}
// If ID isn't valid redirect back to basket
else {
    header("Location: basket.php");
    exit;
}
?>