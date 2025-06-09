<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';

// AJAX detection (also works with jQuery or vanilla fetch)
$isAjax = (
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
);

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (isset($_SESSION['basket'][$id])) {
        unset($_SESSION['basket'][$id]);
        if ($isAjax) {
            echo "OK";
            exit;
        } else {
            header("Location: basket.php?removed=1");
            exit;
        }
    } else {
        if ($isAjax) {
            http_response_code(400);
            echo "Not in basket";
            exit;
        } else {
            header("Location: basket.php");
            exit;
        }
    }
} else {
    if ($isAjax) {
        http_response_code(400);
        echo "Invalid ID";
        exit;
    } else {
        header("Location: basket.php");
        exit;
    }
}
?>