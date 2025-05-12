<?php
include '../header.php';

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

    // Check if username exists
    $sql = "SELECT id, username, password, account_type FROM registered_accounts WHERE username = ?";
    $stmt = sqlsrv_query($conn, $sql, [$username]);

    if ($stmt === false) {
        die("Query error: ".print_r(sqlsrv_errors(), true));
    }

    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // User exists; check password
        if (password_verify($password, $row['password'])) {
            $_SESSION['account_loggedin'] = true;
            $_SESSION['account_id'] = $row['id'];
            $_SESSION['account_name'] = $row['username'];  // change to $row['account_name'] if that's the field name!
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
  </head>
  <body>
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
            <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($username) ?>" placeholder="Username" required autofocus>
            <label for="username">Username</label>
          </div>
          <div class="form-floating mb-2">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
            <label for="password">Password</label>
          </div>
          <button class="btn btn-warning w-100 py-2" type="submit">Sign in</button>
          <p class="mt-3 text-center mb-0">Don't have an account? <a href="register.php" class="form-link">Register</a></p>
          <p class="mt-3 mb-2 text-body-secondary text-center">&copy; 2017–2025</p>
        </form>
      </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>