<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
include '../header.php';

$current_username = $_SESSION['account_name'];
$account_id = $_SESSION['account_id'];

$change_error = "";
$change_success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['new_username'] ?? "");

    $serverName = "UK-DIET-SQL-T1";
    $connectionOptions = [
        "Database" => "Group6_DB",
        "Uid" => "UserGroup6",
        "PWD" => "UpqrxGOkJdQ64MFC"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) die(print_r(sqlsrv_errors(), true));

    if (empty($new_username)) {
        $change_error = "Please enter a new username.";
    } else {
        // Check if username already exists (case-insensitive)
        $check_sql = "SELECT id FROM registered_accounts WHERE LOWER(username) = LOWER(?) AND id != ?";
        $check_stmt = sqlsrv_query($conn, $check_sql, [$new_username, $account_id]);
        if ($check_stmt === false) {
            $change_error = "Database error: " . print_r(sqlsrv_errors(), true);
        } elseif (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
            $change_error = "Username is already taken. Please choose another.";
        } else {
            // Username is unique, update it
            $update_sql = "UPDATE registered_accounts SET username = ? WHERE id = ?";
            $update_stmt = sqlsrv_query($conn, $update_sql, [$new_username, $account_id]);
            if ($update_stmt === false) {
                $change_error = "Database error: " . print_r(sqlsrv_errors(), true);
            } else {
                $_SESSION['account_name'] = $new_username;
                $current_username = $new_username;
                $change_success = "Username changed successfully!";
            }
        }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>Change Username</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-change { max-width: 400px; margin: 2rem auto; padding: 2rem 1.5rem; background: #fff; border-radius: 12px; box-shadow: 0 2px 8px 0 #eee; }
    </style>
</head>
<body class="bg-body-tertiary">
<main class="form-change">
    <form method="post" autocomplete="off" novalidate>
        <h1 class="h4 mb-3 fw-normal text-center">Change Username</h1>
        <p class="mb-4 text-muted text-center">Enter a new username. Pick something unique!</p>
        <?php if($change_error): ?>
            <div class="alert alert-danger"><?= $change_error ?></div>
        <?php elseif($change_success): ?>
            <div class="alert alert-success"><?= $change_success ?></div>
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label">Current Username</label>
            <input type="text" class="form-control bg-body-secondary" value="<?= htmlspecialchars($current_username) ?>" disabled>
        </div>
        <div class="mb-3">
            <label for="new_username" class="form-label">New Username</label>
            <input type="text" class="form-control" id="new_username" name="new_username" placeholder="New username" required autocomplete="off">
        </div>
        <button class="btn btn-warning w-100" type="submit">Change Username</button>
        <p class="mt-3 text-center"><a href="profile.php">&larr; Back to Profile</a></p>
    </form>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>