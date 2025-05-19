<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    header('Location: /Sign-in/signin.php');
    exit();
}
include '../header.php';
$change_error = "";
$change_success = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $serverName = "UK-DIET-SQL-T1";
    $connectionOptions = [
        "Database" => "Group6_DB",
        "Uid" => "UserGroup6",
        "PWD" => "UpqrxGOkJdQ64MFC"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) die(print_r(sqlsrv_errors(), true));
    $account_id = $_SESSION['account_id'];
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
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-register { max-width: 400px; margin: auto; padding: 2rem 0; }
        #password-strength-bar .progress-bar { transition: width 0.25s; }
        .pwreq-list { list-style: none; padding-left: 0; margin-bottom:0.5rem }
        .pwreq-item { font-size: 0.97em; transition: color 0.3s; }
        .pwreq-item.valid { color: #198754; text-decoration: line-through; }
        .pwreq-item.invalid { color: #dc3545; text-decoration: none; }
        .pwreq-icon { font-weight: bold; width: 1.3em; display: inline-block;}
        .pw-outline-invalid { border-color: #dc3545 !important; }
        #pw-bad-message { color: #dc3545; font-size: 0.97em; display:none; }
        input[type="password"]::-ms-reveal,
        input[type="password"]::-webkit-credentials-auto-fill-button,
        input[type="password"]::-webkit-textfield-decoration-container {
            display: none !important;
        }
        input[type="password"]::-webkit-input-password-toggle-button {
            display: none !important;
        }
    </style>
</head>
<body class="bg-body-tertiary" onload="checkPasswordStrength()">
<main class="form-register">
    <form method="post" autocomplete="off" novalidate>
        <h1 class="h3 mb-3 fw-normal text-center">Change your password</h1>
        <p class="text-center text-muted mb-4">Choose a strong new password. You can use the eye icon to check your entry.</p>
        <?php if ($change_error): ?>
            <div class="alert alert-danger"><?= $change_error ?></div>
        <?php elseif ($change_success): ?>
            <div class="alert alert-success"><?= $change_success ?></div>
        <?php endif; ?>
        <!-- Password input with show/hide button -->
        <div class="form-floating mb-2 position-relative">
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Password" required
                   oninput="checkPasswordStrength();showPwBtn('password','showPasswordBtn')" onfocus="showPwBtn('password','showPasswordBtn')" onblur="hidePwBtn('password','showPasswordBtn')">
            <label for="password">New Password</label>
            <button type="button"
                    id="showPasswordBtn"
                    class="btn p-0 bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 d-none"
                    tabindex="-1" 
                    onclick="togglePassword('password','eyeIcon')">
                <i class="bi bi-eye fs-5" id="eyeIcon"></i>
            </button>
        </div>
        <!-- Password Strength Meter and Requirements Checklist -->
        <div id="pw-meter-box" class="mb-2" style="display: none;">
            <div id="password-strength-bar" class="progress" style="height: 8px;">
                <div id="password-strength-fill" class="progress-bar" style="width: 0%; background-color: #dc3545;"></div>
            </div>
            <small id="password-strength-label" class="form-text mb-1"></small>
            <ul class="pwreq-list mt-2">
                <li id="req-length" class="pwreq-item invalid">
                    <span class="pwreq-icon">✖</span> At least 8 characters
                </li>
                <li id="req-upper" class="pwreq-item invalid">
                    <span class="pwreq-icon">✖</span> At least 1 uppercase letter
                </li>
                <li id="req-lower" class="pwreq-item invalid">
                    <span class="pwreq-icon">✖</span> At least 1 lowercase letter
                </li>
                <li id="req-digit" class="pwreq-item invalid">
                    <span class="pwreq-icon">✖</span> At least 1 number
                </li>
                <li id="req-special" class="pwreq-item invalid">
                    <span class="pwreq-icon">✖</span> At least 1 special character
                </li>
            </ul>
        </div>
        <div id="pw-bad-message">Password must be <b>at least Strong</b> and meet all requirements.</div>
        <!-- Confirm password with show/hide button -->
        <div class="form-floating mb-2 position-relative">
            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                   placeholder="Confirm Password" required
                   oninput="showPwBtn('password_confirm','showPasswordBtnConfirm')" onfocus="showPwBtn('password_confirm','showPasswordBtnConfirm')" onblur="hidePwBtn('password_confirm','showPasswordBtnConfirm')">
            <label for="password_confirm">Confirm New Password</label>
            <button type="button"
                    id="showPasswordBtnConfirm"
                    class="btn p-0 bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 d-none"
                    tabindex="-1"
                    onclick="togglePassword('password_confirm','eyeIconConfirm')">
                <i class="bi bi-eye fs-5" id="eyeIconConfirm"></i>
            </button>
        </div>
        <button id="reg-btn" class="btn btn-warning w-100 py-2" type="submit">Change Password</button>
        <p class="mt-3 text-center"><a href="profile.php">&larr; Back to Profile</a></p>
    </form>
</main>
<script>
function togglePassword(inputId, iconId) {
    const pwd = document.getElementById(inputId);
    const eye = document.getElementById(iconId);
    if (pwd.type === 'password') {
        pwd.type = 'text';
        eye.classList.remove('bi-eye');
        eye.classList.add('bi-eye-slash');
    } else {
        pwd.type = 'password';
        eye.classList.add('bi-eye');
        eye.classList.remove('bi-eye-slash');
    }
}
function showPwBtn(inputId, btnId) {
    var pwd = document.getElementById(inputId);
    var btn = document.getElementById(btnId);
    if (pwd.value.length > 0 || document.activeElement === pwd) {
        btn.classList.remove('d-none');
    }
}
function hidePwBtn(inputId, btnId) {
    var pwd = document.getElementById(inputId);
    var btn = document.getElementById(btnId);
    setTimeout(function() {
      if (pwd.value.length === 0 && document.activeElement !== pwd) {
          btn.classList.add('d-none');
      }
    }, 100);
}
function checkPasswordStrength() {
    const pwd = document.getElementById('password').value;
    const fill = document.getElementById('password-strength-fill');
    const label = document.getElementById('password-strength-label');
    const meterBox = document.getElementById('pw-meter-box');
    const pwInput = document.getElementById('password');
    const regButton = document.getElementById('reg-btn');
    const badMsg = document.getElementById('pw-bad-message');
    const reqs = [
        { regex: /.{8,}/, el: 'req-length' },
        { regex: /[A-Z]/, el: 'req-upper' },
        { regex: /[a-z]/, el: 'req-lower' },
        { regex: /[0-9]/, el: 'req-digit' },
        { regex: /[\W_]/, el: 'req-special' }
    ];
    let strength = 0;
    meterBox.style.display = pwd.length > 0 ? 'block' : 'none';
    for (const req of reqs) {
        let elem = document.getElementById(req.el);
        let icon = elem.querySelector('.pwreq-icon');
        if (req.regex.test(pwd)) {
            elem.classList.remove('invalid');
            elem.classList.add('valid');
            icon.textContent = '✔';
        } else {
            elem.classList.remove('valid');
            elem.classList.add('invalid');
            icon.textContent = '✖';
        }
        if (req.regex.test(pwd)) strength++;
    }
    let meter = [
        { color: "#dc3545", text: "Very Weak" },
        { color: "#fd7e14", text: "Weak" },
        { color: "#ffc107", text: "Moderate" },
        { color: "#198754", text: "Strong" },
        { color: "#105f1f", text: "Very Strong" }
    ];
    let meterIndex = Math.min(strength, meter.length-1);
    fill.style.width = (strength / 5 * 100) + "%";
    fill.style.backgroundColor = meter[meterIndex].color;
    label.textContent = meter[meterIndex].text;
    label.style.color = meter[meterIndex].color;
    let strong = (strength >= 4);
    if (pwd.length === 0) {
        pwInput.classList.remove('pw-outline-invalid');
        badMsg.style.display = "none";
        regButton.disabled = false;
    } else if (!strong) {
        pwInput.classList.add('pw-outline-invalid');
        badMsg.style.display = "block";
        regButton.disabled = true;
    } else {
        pwInput.classList.remove('pw-outline-invalid');
        badMsg.style.display = "none";
        regButton.disabled = false;
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>