<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
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
    $firstname = trim($_POST['firstname'] ?? "");
    $lastname = trim($_POST['lastname'] ?? "");
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
    // Account type logic 
    $account_type = "guest";
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

    if (!$registration_error) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $sql2 = "INSERT INTO registered_accounts (firstname, lastname, username, email, password, account_type) VALUES (?, ?, ?, ?, ?, ?)";
        $params2 = [$firstname, $lastname, $username, $email, $hashed, $account_type];
        $stmt2 = sqlsrv_query($conn, $sql2, $params2);
        if ($stmt2 === false) {
            $registration_error = "Database error (save): " . print_r(sqlsrv_errors(), true);
        } else {
            $registration_success = "Registration successful! <a href='signin.php'>Sign in</a>.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">


</head>
<style>
* {
    color: rgb(255, 255, 255)
}

body {
    background: radial-gradient(78.82% 50% at 50% 50%, #000525 0%, #000 100%);
}

.earth img {
    position: absolute;
    top: 20vh;
    right: 10vw;
    z-index: -1;
    width: 80vh;
    height: 80vh;
}

.crack img {
    position: absolute;
    left: 3vw;
    z-index: 2;
    width: auto;
    height: 15vh;
}

.glass-box {
    background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
    backdrop-filter: blur(1vh);
    border-radius: 1rem;
    min-height: 80vh;
    transform: translateY(3vh);
    box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
    padding-left: 4.5vh;
    padding-right: 4.5vh;
}

.glass-box h1 {
    font-family: 'Roboto', Arial, sans-serif;
    padding-top: 0vh;
    font-size: 6vh;
    font-weight: 900;
    color: #fff;
    margin-bottom: 20px;
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
    padding-top: 6vh;
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
    margin-bottom: 35px;
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

.login-signup-message {
    text-align: center;
    margin-top: 10px;
    color: #fff;
    font-size: 1rem;
    position: absolute;
    bottom: 2vh;
    left: 0;
    right: 0;
}

.login-signup-message a {
    color: #ff9100;
    text-decoration: underline;
}
</style>

<body>

    <div class="earth">
        <img src="/assets/images/earth.png" alt="">
    </div>

    <div class="container-fluid">
        <div class="row justify-content-start align-items-center" style="min-height: 92vh;">
            <div class="col-1"></div>
            <div class="col-4">
                <div class="glass-box">

                    <div class="crack">
                        <img src="/assets/images/crack.png" alt="">
                    </div>
                    <a href="../">
                        <img class="login-logo" src="/assets/brand/Quake Logo.png" alt="">

                    </a>
                    <h1 style="color: #FF7400;">Register</h1>
                    <p>Create an account today.</p>
                    <form style="margin-top:10px;text-align:left;" action="" method="post" autocomplete="off">
                        <input type="text" name="firstname" id="firstname" placeholder="First Name" required>
                        <input type="text" name="lastname" id="lastname" placeholder="Last Name" required>
                        <input type="text" name="username" id="username" placeholder="Username" required>
                        <input type="email" name="email" id="email" placeholder="Email" required>
                        <input type="password" name="password" id="password" placeholder="Password" required>
                        <input type="password" name="password_confirm" placeholder="Confirm Password" required>
                        <input type="text" name="org_code" id="code" placeholder="Organisation Code (optional)"
                            style="margin-bottom:10vh;">
                        <button type="submit" class="login-btn">Sign up</button>
                    </form>
                    <div class="login-signup-message" style="position:static; text-align: center;">
                        Already have an account? <a href="/sign-in/signin.php">Login</a>
                    </div>

</body>

</html>