<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include '../queryLibrary.php';
// Get and sanitize id
$id = isset($_POST['id']) ? $_POST['id'] : 0;

$stmt = delete_earthquake($conn, $id);


// Success
header("Location: manage_earthquakesNew.php");
exit;