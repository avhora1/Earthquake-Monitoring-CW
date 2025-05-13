<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
include '../header.php';
$registration_error = "";
$registration_success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serverName = "UK-DIET-SQL-T1";
    $connectionOptions = [
        "Database" => "Group6_DB",
        "Uid" => "UserGroup6",
        "PWD" => "UpqrxGOkJdQ64MFC"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) die(print_r(sqlsrv_errors(), true));

    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $password_confirm = $_POST['password_confirm'] ?? "";
    $code = trim($_POST['code'] ?? "");

    // Password requirements for "Strong"
    $pw_strong = (
        strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[\W_]/', $password)
    );

    // Validate fields
    if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $registration_error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = "Invalid email format.";
    } elseif ($password !== $password_confirm) {
        $registration_error = "Passwords do not match.";
    } elseif (!$pw_strong) {
        $registration_error = "Your password must be at least 'Strong' and meet all requirements.";
    }

    // Account type logic (SERVER-SIDE: only admins know the secrets)
$account_type = "guest";
$code = trim($_POST['code'] ?? "");
$codes = [
    'junior_scientist' => '6c8f19b2-8a71-48b3-ba1e-123456JS',
    'senior_scientist' => 'c5bf13d7-fb91-42a8-af84-123456SS',
    'admin'            => '829fa94f-3384-41f1-9876-123456AD'
];

if (empty($code)) {
    $account_type = "guest";
} elseif ($code === $codes['junior_scientist']) {
    $account_type = "junior_scientist";
} elseif ($code === $codes['senior_scientist']) {
    $account_type = "senior_scientist";
} elseif ($code === $codes['admin']) {
    $account_type = "admin";
} else {
    $registration_error = "Invalid registration code. Please contact your admin.";
}


    // Check for duplicate usernames or emails
    if (!$registration_error) {
        $sql = "SELECT username, email FROM registered_accounts WHERE username = ? OR email = ?";
        $params = [$username, $email];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            $registration_error = "Database error (search): " . print_r(sqlsrv_errors(), true);
        } else {
            $already_username = false;
            $already_email = false;
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                if (strcasecmp($row['username'], $username) === 0) $already_username = true;
                if (strcasecmp($row['email'], $email) === 0) $already_email = true;
            }
            if ($already_username && $already_email) {
                $registration_error = "Username and email are already in use.";
            } elseif ($already_username) {
                $registration_error = "Username is already in use.";
            } elseif ($already_email) {
                $registration_error = "Email is already in use.";
            }
        }
    }

    // Save if no error at this point
    if (!$registration_error) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql2 = "INSERT INTO registered_accounts (username, email, password, account_type) VALUES (?, ?, ?, ?)";
        $params2 = [$username, $email, $hashed, $account_type];
        $stmt2 = sqlsrv_query($conn, $sql2, $params2);
        if ($stmt2 === false) {
            $registration_error = "Database error (save): " . print_r(sqlsrv_errors(), true);
        } else {
            $registration_success = "Registration successful! <a href='signin.php'>Sign in</a>.";
        }
    }
}
?>
<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <title>Register</title>
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
    </style>
</head>
<body class="bg-body-tertiary" onload="checkPasswordStrength()">
<main class="form-register">
    <form method="post" autocomplete="off" novalidate>
        <h1 class="h3 mb-3 fw-normal text-center">Register</h1>
        <?php if ($registration_error): ?>
            <div class="alert alert-danger"><?= $registration_error ?></div>
        <?php elseif ($registration_success): ?>
            <div class="alert alert-success"><?= $registration_success ?></div>
        <?php endif; ?>

        <div class="form-floating mb-2">
            <input type="text" class="form-control" id="username" name="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? "") ?>"
                   placeholder="Username" required>
            <label for="username">Username</label>
        </div>
        <div class="form-floating mb-2">
            <input type="email" class="form-control" id="email" name="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? "") ?>"
                   placeholder="name@example.com" required>
            <label for="email">Email address</label>
        </div>

        <div class="form-floating mb-2">
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Password" required oninput="checkPasswordStrength()">
            <label for="password">Password</label>
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

        <div class="form-floating mb-2">
            <input type="password" class="form-control" id="password_confirm" name="password_confirm"
                   placeholder="Password (again)" required>
            <label for="password_confirm">Confirm Password</label>
        </div>

        <div class="form-floating mb-3">
            <input type="text" class="form-control" id="code" name="code"
                   value="<?= htmlspecialchars($_POST['code'] ?? "") ?>"
                   placeholder="Optional code">
            <label for="code">Account Code (optional)</label>
        </div>

        <button id="reg-btn" class="btn btn-warning w-100 py-2" type="submit">Register</button>
        <p class="mt-3 text-center"><a href="signin.php">Already have an account? Sign in</a></p>
    </form>
</main>
<script>
function checkPasswordStrength() {
    const pwd = document.getElementById('password').value;
    const fill = document.getElementById('password-strength-fill');
    const label = document.getElementById('password-strength-label');
    const meterBox = document.getElementById('pw-meter-box');
    const pwInput = document.getElementById('password');
    const regButton = document.getElementById('reg-btn');
    const badMsg = document.getElementById('pw-bad-message');
    // Requirement elements
    const reqs = [
        { regex: /.{8,}/, el: 'req-length' },
        { regex: /[A-Z]/, el: 'req-upper' },
        { regex: /[a-z]/, el: 'req-lower' },
        { regex: /[0-9]/, el: 'req-digit' },
        { regex: /[\W_]/, el: 'req-special' }
    ];
    let strength = 0;
    // Show requirements only if there is input
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

    // Password strength meter display
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

    // "Strong" means at least 4/5 requirements, "Very Strong" is 5/5
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