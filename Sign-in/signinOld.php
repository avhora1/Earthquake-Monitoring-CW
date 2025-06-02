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
<!doctype html>
<html lang="en" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign-in</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="sign-in.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
      .form-signin { max-width: 400px; margin: auto; }
      .position-relative .btn-outline-secondary {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        background: #343a40;
        color: #fff;
      }
      .form-signin input[type="password"], .form-signin input[type="text"] { padding-right: 2.5rem; }
      /* Hide browser's default password toggle where possible */
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
  <body>
    <?php include "../header.php"; ?>
    <div class="d-flex align-items-center py-4 bg-body-tertiary" style="min-height:calc(100vh - 64px);">
      <main class="form-signin w-100 m-auto">
        <form action="" method="post" autocomplete="off">
          <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
          <?php if ($login_error): ?>
            <div class="alert alert-danger" role="alert">
              <?= htmlspecialchars($login_error) ?>
            </div>
          <?php endif; ?>
          <div class="form-floating mb-2">
            <input type="text" class="form-control" id="username" name="username"
                   value="<?= htmlspecialchars($username) ?>" placeholder="Username" required autofocus>
            <label for="username">Username</label>
          </div>
          <div class="form-floating mb-2 position-relative">
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Password" required
                   onfocus="showToggleBtn()" onblur="hideToggleBtn()" oninput="showToggleBtn()">
            <label for="password">Password</label>
            <button type="button"
        id="showPasswordBtn"
        class="btn p-0 bg-transparent border-0 position-absolute top-50 end-0 translate-middle-y me-2 d-none"
        tabindex="-1" 
        onclick="togglePassword('password','eyeIcon')">
    <i class="bi bi-eye fs-5" id="eyeIcon"></i>
</button>
          </div>
          <button class="btn btn-warning w-100 py-2" type="submit">Sign in</button>
          <p class="mt-3 text-center mb-0">Don't have an account? <a href="register.php" class="form-link">Register</a></p>
          <p class="mt-3 mb-2 text-body-secondary text-center">&copy; 2017–2025</p>
        </form>
      </main>
    </div>
    <script>
    function togglePassword() {
        const pwd = document.getElementById('password');
        const eye = document.getElementById('eyeIcon');
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
    function showToggleBtn() {
        var pwd = document.getElementById('password');
        var btn = document.getElementById('showPasswordBtn');
        if (pwd.value.length > 0 || document.activeElement === pwd) {
            btn.classList.remove('d-none');
        }
    }
    function hideToggleBtn() {
        var pwd = document.getElementById('password');
        var btn = document.getElementById('showPasswordBtn');
        setTimeout(function() {
          if (pwd.value.length === 0 && document.activeElement !== pwd) {
              btn.classList.add('d-none');
          }
        }, 100);
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>