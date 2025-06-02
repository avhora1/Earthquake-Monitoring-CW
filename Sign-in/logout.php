<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';

// Unset all session variables
$_SESSION = [];

// Remove session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"] ?? '/',
        $params["domain"] ?? '',
        $params["secure"] ?? false,
        $params["httponly"] ?? false
    );
}

// Destroy session
session_destroy();

// Redirect with logout GET param
header('Location: /index.php?logout=1');
exit();
?>