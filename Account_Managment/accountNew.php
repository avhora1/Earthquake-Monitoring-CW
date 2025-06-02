<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../connection.php';
include $_SERVER['DOCUMENT_ROOT'].'/sidebar.php';
include '../headerNew.php';
// Only logged in accounts allowed
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
$account_id = $_SESSION['account_id'];
$account_name = $_SESSION['account_name'];
$account_type = $_SESSION['account_type'];

// ------ Change Username Logic ------
$uname_error = "";
$uname_success = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_username'])) {
    $new_username = trim($_POST['new_username'] ?? "");
    if (empty($new_username)) {
        $uname_error = "Please enter a new username.";
    } else {
        // Check uniqueness
        $check_sql = "SELECT id FROM registered_accounts WHERE LOWER(username) = LOWER(?) AND id != ?";
        $check_stmt = sqlsrv_query($conn, $check_sql, [$new_username, $account_id]);
        if ($check_stmt === false) {
            $uname_error = "Database error: " . print_r(sqlsrv_errors(), true);
        } elseif (sqlsrv_fetch_array($check_stmt, SQLSRV_FETCH_ASSOC)) {
            $uname_error = "Username is already taken. Please choose another.";
        } else {
            $update_sql = "UPDATE registered_accounts SET username = ? WHERE id = ?";
            $update_stmt = sqlsrv_query($conn, $update_sql, [$new_username, $account_id]);
            if ($update_stmt === false) {
                $uname_error = "Database error: " . print_r(sqlsrv_errors(), true);
            } else {
                $_SESSION['account_name'] = $new_username;
                $account_name = $new_username;
                $uname_success = "Username changed successfully!";
            }
        }
    }
}

// ------ Change Password Logic ------
$change_error = "";
$change_success = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    include '../connection.php';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    // Password requirements for "Strong"
    $pw_strong = (
        strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[\W_]/', $password)
    );
    if (empty($password) || empty($password_confirm)) {
        $change_error = "Please fill in both password fields.";
    } elseif ($password !== $password_confirm) {
        $change_error = "Passwords do not match.";
    } elseif (!$pw_strong) {
        $change_error = "Your password must be at least 'Strong' and meet all requirements.";
    }
    if (!$change_error) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE registered_accounts SET password = ? WHERE id = ?";
        $params = [$hashed, $account_id];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            $change_error = "Database error (update): " . print_r(sqlsrv_errors(), true);
        } else {
            $change_success = "Password changed successfully!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Manage Account | Quake</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/quake.css">
    <style>
    .account-panel {
        flex: 1.6;
        min-width: 550px;
        background: linear-gradient(113deg, rgba(40, 44, 64, .91), rgba(22, 26, 38, .95) 90%);
        border-radius: 20px;
        box-shadow: 0 0 34px #090e206e;
        padding: 40px 45px 36px 45px;
        margin: 0 16px 0 0;
    }

    .account-panel h2 {
        font-size: 2.1rem;
        font-weight: 800;
        margin-bottom: 3px;
    }

    .account-panel .section-desc {
        color: #bfc7df;
        margin-bottom: 26px;
        font-size: 1.07em;
        line-height: 1.5;
    }

    .account-panel dl {
        margin-bottom: 24px;
        margin-top: 0;
        font-size: 1.1em;
        display: grid;
        grid-template-columns: 170px 1fr;
        gap: 7px 0;
    }

    .account-panel dt {
        color: #e5e7ed;
        font-weight: 500;
    }

    .account-panel dd {
        margin: 0 0 0 0;
        color: #fff;
    }

    .form-section {
        margin-bottom: 36px;
    }

    .account-actions-form {
        display: flex;
        flex-direction: column;
        gap: 18px;
        margin-bottom: 7px;
        max-width: 410px;
    }

    .account-actions-form input[type="password"],
    .account-actions-form input[type="text"] {
        background: transparent;
        border: none;
        border-bottom: 1.5px solid #3d414d;
        color: #fff;
        font-size: 1.06em;
        padding: 6px 10px 6px 10px;
        border-radius: 0;
        outline: none;
        min-width: 140px;
        transition: border-color .18s;
    }

    .account-actions-form input[type="password"]:focus,
    .account-actions-form input[type="text"]:focus {
        border-color: #ff9100;
    }

    .pwreq-list,
    #pwreq-list {
        margin: 8px 0 0 0;
        padding-left: 0;
        font-size: 0.97em;
        line-height: 1.4;
        list-style: none;
    }

    #pwreq-list .pwreq-item {
        color: #ff4a4a;
        transition: color .25s, text-decoration .25s;
    }

    #pwreq-list .pwreq-item.valid {
        color: #23ed5e;
        text-decoration: line-through;
    }

    .success-msg {
        margin-bottom: 10px;
        color: #22ff74;
        font-weight: 600;
    }

    .error-msg {
        margin-bottom: 10px;
        color: #ff4a4a;
        font-weight: 600;
    }

    .add-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    @media (max-width:1100px) {
        .account-panel {
            padding: 18px 6vw;
        }
    }

    @media (max-width:700px) {
        .account-panel {
            min-width: none;
            padding: 9px 1vw 18px 2vw;
        }
    }
    </style>
</head>

<body>

    <!-- MAIN CONTENT AREA -->
    <div class="main-content">
        <div class="account-panel">
            <h2>Manage Account</h2>
            <div class="section-desc">
                Manage your Observatory account. Change your username/password, view your account type and orders.
            </div>

            <dl>
                <dt>Username:</dt>
                <dd><?= htmlspecialchars($account_name) ?></dd>
                <dt>Account Type:</dt>
                <dd><?= str_replace("_", " ", htmlspecialchars(ucfirst($account_type))) ?></dd>
            </dl>

            <!-- Change Username -->
            <div class="form-section">
                <h3 style="font-size:1.17em; font-weight:700;">Change Username</h3>
                <?php if ($uname_error): ?>
                <div class="error-msg"><?= $uname_error ?></div>
                <?php elseif ($uname_success): ?>
                <div class="success-msg"><?= $uname_success ?></div>
                <?php endif; ?>
                <form class="account-actions-form" method="post" autocomplete="off">
                    <div>
                        <label for="new_username">New Username</label>
                        <input type="text" id="new_username" name="new_username" value=""
                            placeholder="New username (unique)" required autocomplete="off">
                    </div>
                    <button class="add-btn" type="submit" name="change_username">Change Username</button>
                </form>
            </div>

            <!-- Change Password -->
            <div class="form-section">
                <h3 style="font-size:1.17em; font-weight:700;">Change Password</h3>
                <?php if ($change_error): ?>
                <div class="error-msg"><?= $change_error ?></div>
                <?php elseif ($change_success): ?>
                <div class="success-msg"><?= $change_success ?></div>
                <?php endif; ?>
                <form class="account-actions-form" autocomplete="off" method="post" id="changePasswordForm">
                    <div>
                        <label for="password">New Password</label>
                        <input type="password" name="password" id="password" required minlength="8"
                            autocomplete="new-password" oninput="checkPasswordStrength()" style="margin-bottom:0;">
                        <ul id="pwreq-list">
                            <li id="req-length" class="pwreq-item">At least 8 characters</li>
                            <li id="req-upper" class="pwreq-item">At least 1 uppercase letter</li>
                            <li id="req-lower" class="pwreq-item">At least 1 lowercase letter</li>
                            <li id="req-digit" class="pwreq-item">At least 1 number</li>
                            <li id="req-special" class="pwreq-item">At least 1 special character</li>
                        </ul>
                    </div>
                    <div>
                        <label for="password_confirm">Confirm Password</label>
                        <input type="password" name="password_confirm" id="password_confirm" required minlength="8"
                            autocomplete="new-password">
                    </div>
                    <button class="add-btn" type="submit" name="change_password" id="changepw-btn" disabled>Change
                        Password</button>
                </form>
            </div>

            <script>
            // Live highlight for password requirements
            function checkPasswordStrength() {
                const pwd = document.getElementById('password').value;
                let reqs = [{
                        regex: /.{8,}/,
                        id: 'req-length'
                    },
                    {
                        regex: /[A-Z]/,
                        id: 'req-upper'
                    },
                    {
                        regex: /[a-z]/,
                        id: 'req-lower'
                    },
                    {
                        regex: /[0-9]/,
                        id: 'req-digit'
                    },
                    {
                        regex: /[\W_]/,
                        id: 'req-special'
                    }
                ];
                let strength = 0;
                for (const req of reqs) {
                    let li = document.getElementById(req.id);
                    if (req.regex.test(pwd)) {
                        li.classList.add("valid");
                        strength++;
                    } else {
                        li.classList.remove("valid");
                    }
                }
                document.getElementById('changepw-btn').disabled = (strength < 5);
            }
            // Run on page load in case browser autofills
            window.addEventListener('DOMContentLoaded', checkPasswordStrength);
            </script>
</body>

</html>