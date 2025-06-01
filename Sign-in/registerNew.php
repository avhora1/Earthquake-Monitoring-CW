<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
$registration_error = "";
$registration_success = "";
$firstname = $_POST['firstname'] ?? "";
$lastname  = $_POST['lastname'] ?? "";
$username  = $_POST['username'] ?? "";
$email     = $_POST['email'] ?? "";
$org_code  = $_POST['org_code'] ?? "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serverName = "UK-DIET-SQL-T1";
    $connectionOptions = [
        "Database" => "Group6_DB",
        "Uid" => "UserGroup6",
        "PWD" => "UpqrxGOkJdQ64MFC"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);
    if ($conn === false) die(print_r(sqlsrv_errors(), true));
    $firstname = trim($_POST['firstname'] ?? "");
    $lastname = trim($_POST['lastname'] ?? "");
    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $password_confirm = $_POST['password_confirm'] ?? "";
    $org_code = trim($_POST['org_code'] ?? "");
    // Password requirements for "Strong"
    $pw_strong = (
        strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[\W_]/', $password)
    );
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
        $registration_error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registration_error = "Invalid email format.";
    } elseif ($password !== $password_confirm) {
        $registration_error = "Passwords do not match.";
    } elseif (!$pw_strong) {
        $registration_error = "Your password must be at least 'Strong' and meet all requirements.";
    }
    // Account type logic 
    $account_type = "guest";
    $codes = [
        'junior_scientist' => '6c8f19b2-8a71-48b3-ba1e-123456JS',
        'senior_scientist' => 'c5bf13d7-fb91-42a8-af84-123456SS',
        'admin'            => '829fa94f-3384-41f1-9876-123456AD'
    ];
    if (empty($org_code)) {
        $account_type = "guest";
    } elseif ($org_code === $codes['junior_scientist']) {
        $account_type = "junior_scientist";
    } elseif ($org_code === $codes['senior_scientist']) {
        $account_type = "senior_scientist";
    } elseif ($org_code === $codes['admin']) {
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
    if (!$registration_error) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql2 = "INSERT INTO registered_accounts (firstname, lastname, username, email, password, account_type) VALUES (?, ?, ?, ?, ?, ?)";
        $params2 = [$firstname, $lastname, $username, $email, $hashed, $account_type];
        $stmt2 = sqlsrv_query($conn, $sql2, $params2);
        if ($stmt2 === false) {
            $registration_error = "Database error (save): " . print_r(sqlsrv_errors(), true);
        } else {
            $registration_success = "Registration successful! <a href='signin.php'>Login.</a>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: radial-gradient(78.82% 50% at 50% 50%, #000525 0%, #000 100%);
    }

    .earth img {
        position: absolute;
        top: 20vh;
        right: 10vw;
        z-index: -99;
        width: 80vh;
        height: 80vh;
    }

    .crack img {
        position: absolute;
        left: 25vw;
        z-index: 2;
        width: auto;
        height: 15vh;
    }

    .col-4 {
        position: relative;
    }

    .glass-box {
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
        backdrop-filter: blur(1.2vh);
        -webkit-backdrop-filter: blur(1.2vh);
        border: 2px solid rgba(255, 255, 255, 0.09);
        border-radius: 1rem;
        min-height: 80vh;
        transform: translateY(3vh);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
        padding-left: 4.5vh;
        padding-right: 4.5vh;
        padding-bottom: 2vh;
        position: relative;
    }

    .glass-box h1 {
        font-family: 'Roboto', Arial, sans-serif;
        padding-top: 2vh;
        font-size: 6vh;
        font-weight: 900;
        color: #FF7400;
        margin-bottom: 12px;
    }

    .glass-box p {
        padding-right: 3vh;
        color: rgb(255, 255, 255);
        padding-bottom: 1vh;
        font-weight: 400;
        font-size: 1.3rem;
    }

    .login-logo {
        display: block;
        padding-top: 2vh;
        width: 20vh;
        margin-left: auto;
        margin-right: auto;
    }

    .glass-box input {
        width: 100%;
        font-size: 1.3rem;
        color: #fff;
        background: none !important;
        border: none;
        border-bottom: 2px solid rgb(255, 255, 255);
        padding: 12px 2px 6px 0;
        margin-bottom: 32px;
        outline: none;
        font-family: 'Roboto', Arial, sans-serif;
        transition: border-color 0.2s;
    }

    .glass-box input:hover {
        border-bottom: 2px solid #ff9100;
        background: none;
    }

    .login-btn {
        display: block;
        width: 70%;
        margin: 0 auto 24px auto;
        padding: 12px 0;
        border-radius: 1rem;
        background: linear-gradient(92deg, #FF8008 0.64%, #FFC837 98.46%);
        border: none;
        color: #fff;
        font-size: 1.36rem;
        font-weight: 500;
        box-shadow: 0 0 18px #fa8c1690;
        cursor: pointer;
        transition: background 0.19s, box-shadow 0.18s;
        position: absolute;
        margin-top: -6vh;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
    }

    .login-btn:hover {
        background: linear-gradient(90deg, #ffbe3d, #ff9100);
        box-shadow: 0 0 32px #fa8c16bb;
    }

    /* Floating Password Strength Glass Card */
    #pw-strength-glass {
        position: absolute;
        top: 60%;
        left: 105%;
        width: 330px;
        min-height: 210px;
        z-index: 99;
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
        border-radius: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.09);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 16%);
        padding: 22px 18px 20px 22px;
        color: #fff;
        opacity: 0;
        pointer-events: none;
        transition: left .44s cubic-bezier(.5, 1.7, .46, .9), opacity .2s;
        backdrop-filter: blur(1.5vh);
        -webkit-backdrop-filter: blur(1.5vh);
    }

    #pw-strength-glass.active {
        left: 104%;
        opacity: 1;
        pointer-events: auto;
    }

    #pw-strength-label {
        font-size: 1.17rem;
        font-weight: 700;
    }

    #pw-strength-bar {
        height: 8px;
        border-radius: 6px;
        background: none;
        margin-bottom: 18px;
        margin-top: 5px;
    }

    #pw-strength-bar .fill {
        height: 8px;
        border-radius: 6px;
        width: 0%;
        background: #ff9100;
        transition: width 0.2s, background .2s;
    }

    .pwreq-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .pwreq-item {
        font-size: 1.06rem;
        margin-bottom: 4px;
        transition: color 0.2s;
        color: rgb(255, 255, 255);
    }

    .pwreq-item.valid {
        color: #17cb62;
        text-decoration: line-through;
    }

    .pwreq-item.invalid {
        color: rgb(255, 255, 255);
    }

    .pwreq-icon {
        font-weight: bold;
        width: 1.2em;
        display: inline-block;
    }

    /* Floating Registration Success Glass Card */
    #reg-success-glass {
        position: absolute;
        top: 16%;
        left: 105%;
        min-width: 270px;
        max-width: 360px;
        min-height: 68px;
        padding: 19px 22px 17px 28px;
        z-index: 150;
        background: linear-gradient(153deg, rgba(255, 255, 255, 0.21) 0%, rgba(255, 255, 255, 0.08) 100%);
        border-radius: 1rem;
        border: 2px solid rgba(255, 255, 255, 0.09);
        box-shadow: 0 4px 32px 0 rgb(0 0 0 / 16%);
        backdrop-filter: blur(1.5vh);
        -webkit-backdrop-filter: blur(1.5vh);
        color: rgb(255, 255, 255);
        font-size: 1.19rem;
        text-align: center;
        animation: pop-in 0.5s cubic-bezier(.8, -0.06, .83, .67);
        opacity: 0.97;
        pointer-events: auto;
        font-weight: bold;
    }

    @keyframes pop-in {
        from {
            opacity: 0;
            transform: scale(.93) translateY(12px);
        }

        to {
            opacity: 0.97;
            transform: scale(1) translateY(0);
        }
    }

    #reg-success-glass a {
        color: #ff9100;
        text-decoration: none;
    }

    .login-message {
        text-align: center;
        margin-top: 10px;
        color: #fff;
        font-size: 1rem;
        position: absolute;
        left: 0;
        right: 0;
    }

    .login-message a {
        color: #ff9100;
    }
    </style>
</head>

<body>
    <div class="earth">
        <img src="/assets/images/earth.png" alt="">
    </div>
    <div class="container-fluid">
        <div class="row justify-content-start align-items-center" style="min-height: 92vh;">
            <div class="col-1"></div>
            <div class="col-4" style="position:relative;">
                <?php if ($registration_success): ?>
                <div id="reg-success-glass">
                    <?= $registration_success ?>
                </div>
                <?php endif; ?>
                <div class="glass-box">
                    <div class="crack">
                        <img src="/assets/images/crack.png" alt="">
                    </div>
                    <a href="../">
                        <img class="login-logo" src="/assets/brand/Quake Logo.png" alt="">
                    </a>
                    <h1>Register</h1>
                    <p>Create an account today.</p>
                    <?php if ($registration_error): ?>
                    <div style="color:#FF7400; background:rgba(55,0,0,0.24); border-radius:8px;
                                     padding:10px 18px; margin:10px 0 20px 0; font-size:1.11rem;">
                        <?= htmlspecialchars($registration_error) ?>
                    </div>
                    <?php endif; ?>
                    <form style="margin-top:10px;text-align:left;" action="" method="post" autocomplete="off">
                        <input type="text" name="firstname" id="firstname" placeholder="First Name" required
                            value="<?= $registration_success ? '' : htmlspecialchars($firstname) ?>" autocomplete="off">
                        <input type="text" name="lastname" id="lastname" placeholder="Last Name" required
                            value="<?= $registration_success ? '' : htmlspecialchars($lastname) ?>" autocomplete="off">
                        <input type="text" name="username" id="username" placeholder="Username" required
                            value="<?= $registration_success ? '' : htmlspecialchars($username) ?>"
                            autocomplete="new-username">
                        <input type="email" name="email" id="email" placeholder="Email" required
                            value="<?= $registration_success ? '' : htmlspecialchars($email) ?>"
                            autocomplete="new-email">
                        <input type="password" name="password" id="password" placeholder="Password" required
                            onfocus="showPwStrength()" onblur="hidePwStrength()" oninput="updatePwStrength()"
                            autocomplete="new-password">
                        <input type="password" name="password_confirm" id="password_confirm"
                            placeholder="Confirm Password" required autocomplete="new-password">
                        <input type="text" name="org_code" id="org_code" placeholder="Organisation Code (optional)"
                            style="margin-bottom:10vh;"
                            value="<?= $registration_success ? '' : htmlspecialchars($org_code) ?>" autocomplete="off">
                        <button type="submit" class="login-btn">Sign up</button>
                    </form>
                    <div class="login-message" style="position:static; text-align: center;">
                        Already have an account? <a href="/sign-in/signin.php">Login</a>
                    </div>
                </div>
                <!-- Floating Password Strength Glass Card (OUTSIDE .glass-box) -->
                <div id="pw-strength-glass">
                    <div id="pw-strength-label">Password Strength: <span id="pw-strength-value">Very Weak</span>
                    </div>
                    <div id="pw-strength-bar">
                        <div class="fill" id="pw-strength-fill"></div>
                    </div>
                    <ul class="pwreq-list mt-2">
                        <li id="pw-len" class="pwreq-item invalid">
                            <span class="pwreq-icon">✖</span> At least 8 characters
                        </li>
                        <li id="pw-upper" class="pwreq-item invalid">
                            <span class="pwreq-icon">✖</span> At least 1 uppercase letter
                        </li>
                        <li id="pw-lower" class="pwreq-item invalid">
                            <span class="pwreq-icon">✖</span> At least 1 lowercase letter
                        </li>
                        <li id="pw-digit" class="pwreq-item invalid">
                            <span class="pwreq-icon">✖</span> At least 1 number
                        </li>
                        <li id="pw-spec" class="pwreq-item invalid">
                            <span class="pwreq-icon">✖</span> At least 1 special character
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script>
    function showPwStrength() {
        document.getElementById('pw-strength-glass').classList.add('active');
        updatePwStrength();
    }

    function hidePwStrength() {
        setTimeout(function() {
            document.getElementById('pw-strength-glass').classList.remove('active');
        }, 200);
    }

    function updatePwStrength() {
        const pwd = document.getElementById('password').value;
        const fill = document.getElementById('pw-strength-fill');
        const valueLabel = document.getElementById('pw-strength-value');
        const rules = [{
                valid: pwd.length >= 8,
                id: 'pw-len'
            },
            {
                valid: /[A-Z]/.test(pwd),
                id: 'pw-upper'
            },
            {
                valid: /[a-z]/.test(pwd),
                id: 'pw-lower'
            },
            {
                valid: /[0-9]/.test(pwd),
                id: 'pw-digit'
            },
            {
                valid: /[\W_]/.test(pwd),
                id: 'pw-spec'
            }
        ];
        let passed = rules.reduce((a, rule) => rule.valid ? a + 1 : a, 0);
        let grades = [{
                txt: "Very Weak",
                color: "#dc3545"
            },
            {
                txt: "Weak",
                color: "#fd7e14"
            },
            {
                txt: "Moderate",
                color: "#ffc107"
            },
            {
                txt: "Strong",
                color: "#17cb62"
            },
            {
                txt: "Very Strong",
                color: "#17a368"
            }
        ];
        let g = grades[Math.min(passed, grades.length - 1)];
        fill.style.width = (passed / grades.length * 100) + '%';
        fill.style.backgroundColor = g.color;
        valueLabel.textContent = g.txt;
        valueLabel.style.color = g.color;
        rules.forEach(rule => {
            let li = document.getElementById(rule.id);
            if (rule.valid) {
                li.classList.remove('invalid');
                li.classList.add('valid');
                li.querySelector('.pwreq-icon').textContent = '✔';
            } else {
                li.classList.add('invalid');
                li.classList.remove('valid');
                li.querySelector('.pwreq-icon').textContent = '✖';
            }
        });
    }
    </script>
</body>

</html>