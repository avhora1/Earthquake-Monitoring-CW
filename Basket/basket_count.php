<?php
session_start();
header('Content-Type: application/json');
$basket_count = isset($_SESSION['basket']) ? count($_SESSION['basket']) : 0;
echo json_encode(['count' => $basket_count]);
exit;
?>