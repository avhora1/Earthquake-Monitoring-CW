<?php
include $_SERVER['DOCUMENT_ROOT'].'/session.php';
$account_type = $_SESSION['account_type'] ?? '';
$account_username = $_SESSION['account_name'] ?? '';
if (!isset($_POST['move_user'])) exit;

// --- DB connection ---
$serverName = "UK-DIET-SQL-T1";
$connectionOptions = [
    "Database" => "Group6_DB",
    "Uid" => "UserGroup6",
    "PWD" => "UpqrxGOkJdQ64MFC"
];
$conn = sqlsrv_connect($serverName, $connectionOptions);

// --- Fetch all users ---
$sql = "SELECT username, account_type FROM registered_accounts";
$stmt = sqlsrv_query($conn, $sql);
$users = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $users[$row['username']] = $row['account_type'];
}
$move_user = $_POST['move_user'];
// Find if selected user is a manager
$sql = "SELECT username FROM registered_accounts WHERE manager_username = ?";
$stmt = sqlsrv_query($conn, $sql, [$move_user]);
$is_manager = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$is_manager) exit; // No subordinates, do not show select

// Begin select HTML
echo '<label for="replacement_manager" class="form-label">Select Replacement Manager for Subordinates</label>';
echo '<select class="form-select" name="replacement_manager" id="replacement_manager" required>';
echo '<option value="">Select user</option>';
foreach ($users as $uname => $utype) {
    if ($uname == $move_user) continue; // Cannot be their own replacement
    // For admin: any non-admin, for senior: any junior they manage
    if ($account_type === 'admin' && $utype !== 'admin') {
        echo "<option value=\"$uname\">$uname ($utype)</option>";
    } elseif ($account_type === 'senior_scientist' && $utype === 'junior_scientist') {
        // Extra security: check if managed by this senior
        $cursor = $uname;
        $has_me = false;
        // We'll fetch all managers for this user and check up the chain
        $check_sql = "SELECT manager_username FROM registered_accounts WHERE username = ?";
        while ($cursor) {
            $row = sqlsrv_query($conn, $check_sql, [$cursor]);
            $data = sqlsrv_fetch_array($row, SQLSRV_FETCH_ASSOC);
            if ($data && $data['manager_username']) {
                if ($data['manager_username'] == $account_username) { $has_me = true; break; }
                $cursor = $data['manager_username'];
            } else break;
        }
        if ($has_me || $uname == $account_username)
            echo "<option value=\"$uname\">$uname ($utype)</option>";
    }
}
echo '</select>';
?>