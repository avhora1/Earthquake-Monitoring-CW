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
    // Optional: set defaults for other vars like account_id
}

// If user is logged in, check account_type and update if needed (e.g., account_type is null)
if (isset($_SESSION['account_loggedin']) && $_SESSION['account_loggedin']) {
    // Fetch fresh data from DB if needed (e.g., privilege changed mid-session)
    if (!isset($_SESSION['account_type']) || $_SESSION['account_type'] === null) {
        // Only run this part if you want to update role during active session
        // Replace below with your real DB connection and id fetch
        $serverName = "UK-DIET-SQL-T1";
        $connectionOptions = [
            "Database" => "Group6_DB",
            "Uid" => "UserGroup6",
            "PWD" => "UpqrxGOkJdQ64MFC"
        ];
        $conn = sqlsrv_connect($serverName, $connectionOptions);
        if ($conn !== false && isset($_SESSION['account_id'])) {
            $stmt = sqlsrv_query($conn, "SELECT account_type, username FROM registered_accounts WHERE id = ?", [$_SESSION['account_id']]);
            if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $role = $row['account_type'] ?? 'guest';
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

// After this point, you can always use $_SESSION['account_type'] safely.
// Only one session per browser/user.
// Ending the session (logout) should be done with your logout.php as previously shown.
?>