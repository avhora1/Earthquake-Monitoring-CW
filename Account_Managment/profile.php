<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
// Only logged in accounts allowed
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
$account_name = $_SESSION['account_name'];
$account_type = $_SESSION['account_type'];
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>Your Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f6f7f9;
        }
        .profile-header {
            padding: 2rem 1rem 1rem 1rem;
            background-color: #fff;
            border-bottom: 1px solid #eee;
            margin-bottom: 2rem;
        }
        .profile-content {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 0 25px 0 #e2e5ec;
            padding: 2rem;
        }
        .profile-title {
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #175999;
        }
        .section-desc {
            color: #687283;
            font-size: 1.02rem;
        }
        .profile-actions .btn {
            margin-bottom: 0.5rem;
        }
        @media (max-width: 700px) {
            .profile-content { padding: 1rem; }
        }
    </style>
</head>
<body>
<?php include "../header.php"; ?>
<div class="profile-header text-center">
    <h1 class="profile-title">Your Profile</h1>
    <p class="section-desc">
        Manage your Observatory account. Change your username/password, view your account type and orders,
        and access team/administration functions depending on your status.
    </p>
</div>
<div class="profile-content">
    <h4>Account Information</h4>
    <dl class="row mb-4">
        <dt class="col-sm-4">Username:</dt>
        <dd class="col-sm-8"><?= htmlspecialchars($account_name) ?></dd>
        <dt class="col-sm-4">Account Type:</dt>
        <dd class="col-sm-8 text-capitalize"><?= str_replace("_", " ", htmlspecialchars($account_type)) ?></dd>
    </dl>

    <div class="profile-actions mb-4">
        <a href="change_username.php" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-pencil"></i> Change Username
        </a>
        <a href="change_password.php" class="btn btn-outline-primary w-100 text-start mb-2">
            <i class="bi bi-key"></i> Change Password
        </a>
        <a href="orders.php?type=current" class="btn btn-outline-success w-100 text-start mb-2">
            <i class="bi bi-bag"></i> View Current Orders
        </a>
        <a href="orders.php?type=past" class="btn btn-outline-secondary w-100 text-start mb-2">
            <i class="bi bi-archive"></i> View Past Orders
        </a>
    </div>
    
    <?php if (in_array($account_type, ['junior_scientist','senior_scientist','admin'])): ?>
    <div class="mb-4">
        <h5>Team Structure</h5>
        <p class="section-desc">View your place and teammates in the team structure.</p>
        <a href="team_structure.php" class="btn btn-outline-info w-100 text-start mb-2"><i class="bi bi-people"></i> View Team Structure</a>
    </div>
    <?php endif; ?>
   
    <?php if ($account_type === 'admin'): ?>
    <div class="mb-4">
        <h5>User Administration</h5>
        <p class="section-desc">As an administrator, you can search, edit or remove users and manage account privileges.</p>
        <form method="get" action="user_administration.php" class="input-group mb-3" style="max-width:400px;">
            
        </form>
        <a href="user_administration.php" class="btn btn-outline-dark w-100 text-start mb-2"><i class="bi bi-table"></i> User Administration</a>
    </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>