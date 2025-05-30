<?php
// Always start the session if not already started
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// If the user is not already logged in (no session), they are a guest
if (!isset($_SESSION['account_loggedin']) || !$_SESSION['account_loggedin']) {
    $_SESSION['account_loggedin'] = false;
    $_SESSION['account_type'] = 'guest';
    $_SESSION['account_name'] = 'Guest';
}

// If user is logged in, check account_type and update if needed (e.g., account_type is null)
if (isset($_SESSION['account_loggedin']) && $_SESSION['account_loggedin']) {
    // Fetch fresh data from DB if needed (e.g., privilege changed mid-session)
    if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] === null) {
        $serverName = "UK-DIET-SQL-T1";
        $connectionOptions = [
            "Database" => "Group6_DB",
            "Uid" => "UserGroup6",
            "PWD" => "UpqrxGOkJdQ64MFC"
        ];
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn !== false && isset($_SESSION['account_id'])) {
            $stmt = sqlsrv_query($conn, "SELECT firstname, lastname, account_type, username FROM registered_accounts WHERE id = ?", [$_SESSION['account_id']]);
            if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $role = $row['account_type'] ?? 'guest';
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['account_type'] = $role ? $role : 'guest';
                $_SESSION['account_name'] = $row['username']; // in case it needs updating
            } else {
                // User disappeared from DB? Treat as guest/logged out.
                $_SESSION = array();
                session_destroy();
                header('Location: /Sign-in/signin.php');
                exit;
            }
        }
    }
    // If account_type is still null after DB check, set guest
    if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] === null) {
        $_SESSION['account_type'] = 'guest';
    }
}

?>