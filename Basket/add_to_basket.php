<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';

$id = intval($_GET['id'] ?? 0);
if ($id < 1) {
    http_response_code(400);
    echo "Invalid ID";
    exit;
}
if (!isset($_SESSION['basket'])) $_SESSION['basket'] = [];
$_SESSION['basket'][$id] = 1;
echo "OK";
exit;
?>