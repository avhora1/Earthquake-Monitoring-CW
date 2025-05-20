<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';

// Remove session variables and cookies
$_SESSION = [];
$logout_success = true;

// Remove session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    $cookie_result = setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"] ?? '/',
        $params["domain"] ?? '',
        $params["secure"] ?? false,
        $params["httponly"] ?? false
    );
    $logout_success = $logout_success && $cookie_result !== false;
}

// Actually destroy session
$destroy_result = session_destroy();
$logout_success = $logout_success && $destroy_result;

// Toast content
if ($logout_success) {
    $message = "<i class='bi bi-check-circle-fill'></i> Logged out successfully! Redirecting…";
    $toast_class = "success";
} else {
    $message = "<i class='bi bi-x-circle-fill'></i> Logout unsuccessful! Try again or close your browser.";
    $toast_class = "danger";
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Logout</title>
    <meta http-equiv="refresh" content="3;url=/index.php">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .toast-container { position: fixed; top:1rem; right: 1rem; z-index: 1080; }
    </style>
</head>
<body class="bg-light">
    <div class="toast-container p-3">
        <div id="logoutToast" class="toast show align-items-center text-bg-<?= $toast_class ?> border-0"
             role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
            <div class="d-flex">
                <div class="toast-body fw-semibold"><?= $message ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            let toastEl = document.getElementById('logoutToast');
            if (toastEl) (new bootstrap.Toast(toastEl)).hide();
        }, 3000);
    </script>
</body>
</html>