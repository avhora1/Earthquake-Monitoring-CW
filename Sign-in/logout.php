<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
// Unset all of the session variables
$_SESSION = array();

// If you want to destroy the session cookie, do this (best practice!):
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: /index.php');
exit;
?>