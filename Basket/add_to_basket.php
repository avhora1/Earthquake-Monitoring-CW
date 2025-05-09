<?php
session_start();
$id = intval($_GET['id']);
if (!isset($_SESSION['basket'])) $_SESSION['basket'] = [];
// Only allow availability = 'Yes'
// (Actually check from DB here; for now, allow adding):
$_SESSION['basket'][$id] = 1;
header("Location: ../Shop/shop.php");
exit;
?>