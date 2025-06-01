<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';

$login_error = '';
$username = $_POST['username'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serverName = "UK-DIET-SQL-T1";
    $connectionOptions = [
        "Database" => "Group6_DB",
        "Uid" => "UserGroup6",
        "PWD" => "UpqrxGOkJdQ64MFC"
    ];
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die("DB connection error: ".print_r(sqlsrv_errors(), true));
    }
    $password = $_POST['password'] ?? '';
    $sql = "SELECT firstname, lastname, id, username, password, account_type FROM registered_accounts WHERE username = ?";
    $stmt = sqlsrv_query($conn, $sql, [$username]);
    if ($stmt === false) {
        die("Query error: ".print_r(sqlsrv_errors(), true));
    }
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['account_loggedin'] = true;
            $_SESSION['account_id'] = $row['id'];
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['account_name'] = $row['username'];
            $_SESSION['account_type'] = $row['account_type'];
            header('Location: ../index.php');
            exit;
        } else {
            $login_error = "Incorrect username and/or password.";
        }
    } else {
        $login_error = "Account does not exist.";
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
    top: 6vh;
    left: 12vw;
    z-index: 2;
    width: auto;
    height: 15vh;
}

.glass-box {
    background: linear-gradient(153deg, rgba(255, 255, 255, 0.20) 0%, rgba(255, 255, 255, 0.00) 100%);
    border: 2px solid rgba(255, 255, 255, 0.09);
    backdrop-filter: blur(1vh);
    border-radius: 1rem;
    min-height: 80vh;
    top: 20vh;
    box-shadow: 0 4px 32px 0 rgb(0 0 0 / 10%);
    padding-left: 4.5vh;
    padding-right: 4.5vh;
}

.glass-box h1 {
    font-family: 'Roboto', Arial, sans-serif;
    padding-top: 8vh;
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
    margin-bottom: 40px;
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
    margin-bottom: 6px;
}

.login-signup-message a {
    color: #ff9100;
    text-decoration: underline;
}
</style>

<body>

    <div class="crack">
        <img src="/assets/images/crack.png" alt="">
    </div>

    <div class="earth">
        <img src="/assets/images/earth.png" alt="">
    </div>

    <div class="container-fluid">
        <div class="row justify-content-start align-items-center" style="min-height: 92vh;">
            <div class="col-1"></div>
            <div class="col-4">
                <div class="glass-box">
                    <a href="../">
                        <img class="login-logo" src="/assets/brand/Quake Logo.png" alt="">

                    </a>
                    <h1 style="color: #FF7400;">Login</h1>
                    <p>Welcome Back!</p>

                    <?php if ($login_error): ?>
                    <div style="color:#FF7400; background:rgba(55,0,0,0.24); border-radius:8px;
                                     padding:10px 18px; margin:10px 0 20px 0; font-size:1.11rem;">
                        <?= htmlspecialchars($login_error) ?>
                    </div>
                    <?php endif; ?>


                    <form style="margin-top:10px;text-align:left;" action="" method="post" autocomplete="off">
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>"
                            placeholder="Username" required autofocus>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <button type="submit" class="login-btn">Login</button>
                    </form>
                    <div class="login-signup-message">
                        Don’t have an account?
                        <a href="/sign-in/register.php">Sign up</a>
                    </div>
                </div>
            </div>

</body>

</html>